<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use App\Models\v1\Audio;
use App\Models\v1\Video;
use App\Models\v1\Notice;
use App\Models\v1\Article;
use App\Models\v1\Message;
use App\Mail\ResetcodeMail;
use App\Models\v1\Resetcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Mail\TheGloryHubMessageMail;
use App\Models\v1\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

ini_set('memory_limit','1024M');
ini_set("upload_max_filesize","100M");
ini_set("max_execution_time",60000); //--- 10 minutes
ini_set("post_max_size","135M");
ini_set("file_uploads","On");
//sudo gedit /opt/lampp/etc/php.ini
class UserController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    | THIS FUNCTION REGISTES A USER AND PROVIDES THEM WITH AN ACCESS TOKEN
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    */
    public function register(Request $request)
    {

        $validatedData = $request->validate([
            "user_surname" => "bail|required|max:55",
            "user_firstname" => "bail|required|max:55",
            "user_country" => "bail|required|max:55",
            "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15",
            "user_email" => "bail|email|required|max:100",
            "password" => "bail|required|confirmed|max:30",
        ]);

        $validatedData["user_scope"] = "";

        if(
            $request->user_phone_number == "+233207393447" || 
            $request->user_phone_number == "+233553663643" || 
            $request->user_phone_number == "+233551292981" || 
            $request->user_phone_number == "+233203932568" || 
            $request->user_phone_number == "+233246535399" ){

            $validatedData["user_scope"] = "do_admin_things";
        }
        $validatedData["password"] = bcrypt($request->password);
        $validatedData["user_flagged"] = 0;

        $user1 = User::create($validatedData);


        $login_data["user_phone_number"] = $request->user_phone_number;
        $login_data["password"] = $request->password;

        if (!auth()->attempt($login_data)) {
            return response(["status" => 0, "message" => "Invalid Credentials"]);
        }

        if (auth()->user()->user_flagged) {
            return response(["status" => 0, "message" => "Account access restricted"]);
        }

        $allowed_scope = "";
        if(
            $request->user_phone_number == "+233207393447" || 
            $request->user_phone_number == "+233553663643" || 
            $request->user_phone_number == "+233551292981" || 
            $request->user_phone_number == "+233203932568" || 
            $request->user_phone_number == "+233246535399" ){

            $allowed_scope = "do_admin_things";
        }

        $accessToken = auth()->user()->createToken("authToken", [$allowed_scope])->accessToken;

        $notices = DB::table('notices')
        ->select('notices.*')
        ->orderBy("notice_id", "desc")
        ->simplePaginate(2);
    
        for ($i=0; $i < count($notices); $i++) { 

            $date = date_create($notices[$i]->created_at);
            $notices[$i]->created_at = date_format($date,"M j Y");
            $notices[$i]->notice_image = URL::to('/') . $notices[$i]->notice_image;
        }


        $videos = DB::table('videos')
        ->select('videos.*')
        ->orderBy("video_id", "desc")
        ->simplePaginate(2);
        
    
        for ($i=0; $i < count($videos); $i++) { 
            $date = date_create($videos[$i]->created_at);
            $videos[$i]->created_at = date_format($date,"M j Y");
            $videos[$i]->video_image = URL::to('/') . $videos[$i]->video_image;
            $videos[$i]->video_mp4 = URL::to('/') . $videos[$i]->video_mp4;
        }
    
        $audios = DB::table('audio')
        ->select('audio.*')
        ->orderBy("audio_id", "desc")
        ->simplePaginate(1);
     
        for ($i=0; $i < count($audios); $i++) { 
    
            $date = date_create($audios[$i]->created_at);
            $audios[$i]->created_at = date_format($date,"M j Y");
            $audios[$i]->audio_image = URL::to('/') . $audios[$i]->audio_image;
            $audios[$i]->audio_mp3 = URL::to('/') . $audios[$i]->audio_mp3;
        }
    

        $return = [
            "status" => 1,
            "user" => auth()->user(), 
            "access_token" => $accessToken, 
            "data" => $notices,
            "audios" => $audios,
            "videos" => $videos
        ];
        return response()->json($return, 200);
        
    }

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    | THIS FUNCTION PROVIDES A REGISTERED USER WITH AN ACCESS TOKEN
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    */
        
    public function login(Request $request)
    {

        $login_data = $request->validate([
            "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15",
            "password" => "required"
        ]);

        if (!auth()->attempt($login_data)) {
            return response(["status" => 0, "message" => "Invalid Credentials"]);
        }

        if (auth()->user()->user_flagged) {
            return response(["status" => 0, "message" => "Account access restricted"]);
        }

        $allowed_scope = "";
        if(
            $request->user_phone_number == "+233207393447" || 
            $request->user_phone_number == "+233553663643" || 
            $request->user_phone_number == "+233551292981" || 
            $request->user_phone_number == "+233203932568" || 
            $request->user_phone_number == "+233246535399" ){

            $allowed_scope = "do_admin_things";
        }

        $accessToken = auth()->user()->createToken("authToken", [$allowed_scope])->accessToken;

        $notices = DB::table('notices')
        ->select('notices.*')
        ->orderBy("notice_id", "desc")
        ->simplePaginate(2);
    
        for ($i=0; $i < count($notices); $i++) { 

            $date = date_create($notices[$i]->created_at);
            $notices[$i]->created_at = date_format($date,"M j Y");
            $notices[$i]->notice_image = URL::to('/') . $notices[$i]->notice_image;
        }


        $videos = DB::table('videos')
        ->select('videos.*')
        ->orderBy("video_id", "desc")
        ->simplePaginate(2);
        
    
        for ($i=0; $i < count($videos); $i++) { 
            $date = date_create($videos[$i]->created_at);
            $videos[$i]->created_at = date_format($date,"M j Y");
            $videos[$i]->video_image = URL::to('/') . $videos[$i]->video_image;
            $videos[$i]->video_mp4 = URL::to('/') . $videos[$i]->video_mp4;
        }
    
        $audios = DB::table('audio')
        ->select('audio.*')
        ->orderBy("audio_id", "desc")
        ->simplePaginate(1);
     
        for ($i=0; $i < count($audios); $i++) { 
    
            $date = date_create($audios[$i]->created_at);
            $audios[$i]->created_at = date_format($date,"M j Y");
            $audios[$i]->audio_image = URL::to('/') . $audios[$i]->audio_image;
            $audios[$i]->audio_mp3 = URL::to('/') . $audios[$i]->audio_mp3;
        }
    

        $return = [
            "status" => 1,
            "user" => auth()->user(), 
            "access_token" => $accessToken, 
            "data" => $notices,
            "audios" => $audios,
            "videos" => $videos
        ];
        return response()->json($return, 200);
        
    }

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    | THIS FUNCTION REVOKES A USER'S ACCESS TOKEN
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    */
    public function logout(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response(["status" => 0, "message" => "Permission Denied. Please log out and login again"]);
        }
        $request->user()->token()->revoke();
        return response(["status" => 1, "message" => "Logged out successfully"]);
    }


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_users(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }


    $users_count = DB::table('users')
    ->selectRaw('count(*)')
    ->get();

    $users_count = (array) $users_count[0];
    $users_count = (array) $users_count["count(*)"];

    $users = DB::table('users')
    ->select('users.*')
    ->orderBy("user_id", "desc")
    ->simplePaginate(200);
 
    for ($i=0; $i < count($users); $i++) { 

        $date = date_create($users[$i]->created_at);
        $users[$i]->created_at = date_format($date,"M j Y");
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $users, "users_count" => $users_count]);
}

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    | THIS FUNCTION PROVIDES A REGISTERED user WITH AN ACCESS TOKEN
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    */
        
    public function send_password_reset_code(Request $request)
    {
        $resetcode_controller = new ResetcodeController();

        $request->validate([
            "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15"
        ]);

        $user = User::where('user_phone_number', $request->user_phone_number)->first();

        
        if(!isset($user->user_id)) {
            return response(["status" => 0, "message" => "Account not found"]);
        }

        if($user->user_flagged) {
            return response(["status" => 0, "message" => "Account access restricted"]);
        }

        $resetcode = $resetcode_controller->generate_resetcode();

        $email_data = array(
            'reset_code' => $resetcode,
            'time' => date("F j, Y, g:i a")
        );

        $resetcode_controller->save_resetcode("member", $user->user_id, strval($resetcode));

        Mail::to($user->user_email)->send(new ResetcodeMail($email_data));

        return response([
            "status" => 1, 
            "message" => "Reset code has been sent to your email"
        ]);
    }


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION VERIFIES THE PASSCODE ENTERED
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/

public function verify_reset_code(Request $request)
{
    $resetcode_controller = new ResetcodeController();

    $request->validate([
        "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15",
        "resetcode" => "bail|required|max:7",
        "password" => "bail|required|confirmed|min:8|max:30"
    ]);

    $user = User::where('user_phone_number', $request->user_phone_number)->first();

    if(!isset($user->user_id)) {
        return response(["status" => 0, "message" => "Account not found"]);
    }

    if($user->user_flagged) {
        return response(["status" => 0, "message" => "Account access restricted"]);
    }

    $resetcode = Resetcode::where([
        'user_id' => $user->user_id,
        'user_type' => "member",
        'resetcode' => $request->resetcode,
        'used' => false
    ])
        ->orderBy('resetcode', 'desc')
        ->take(1)
        ->get();


    if (isset($resetcode[0]["user_id"]) && $resetcode[0]["user_id"] == $user->user_id) {
        
        $user->password = bcrypt($request->password);
        $user->save();

        $resetcode_controller->update_resetcode($resetcode[0]["resetcode_id"], $resetcode[0]["user_type"], $resetcode[0]["user_id"], $resetcode[0]["resetcode"], true);
        return response(["status" => 1, "message" => "Password reset successful"]);
    } else {
        return response(["status" => 0, "message" => "Reset failed"]);
    }
}



public function add_audio(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "audio_name" => "bail|required|max:45",
        "audio_description" => "bail|required",
        "audio_image" => "bail|required",
        "audio_mp3" => "bail|required"
    ]);
    
    
    /*
    // get user object
    $user = User::where('user_phone_number', request()->user_phone_number)->first();
    // do the passwords match?

    if ($user == null || !Hash::check(request()->password, $user->password)) {
        // no they don't
        return back()->with('fail','Authorization failed');
    }


    if ($user->user_flagged) {
        return back()->with('fail','Authorization failed');
    }

    if(
        $request->user_phone_number != "+233207393447" && 
        $request->user_phone_number != "+233553663643" && 
        $request->user_phone_number != "+233551292981" && 
        $request->user_phone_number != "+233203932568" && 
        $request->user_phone_number != "+233246535399" ){

            return back()->with('fail','Authorization failed');
    }
    */

    if(!$request->hasFile('audio_image')) {
        return response(["status" => "fail", "message" => "Image not found"]);
        //return back()->with('fail','Image not found');
    }

    if(!$request->hasFile('audio_mp3')) {
        return response(["status" => "fail", "message" => "Audio not found"]);
        //return back()->with('fail','Audio not found');
    }

    if(!$request->file('audio_image')->isValid()) {
        return response(["status" => "fail", "message" => "Image not valid"]);
        //return back()->with('fail','Image not valid');
    }

    if(!$request->file('audio_mp3')->isValid()) {
        return response(["status" => "fail", "message" => "Audio not valid"]);
        //eturn back()->with('fail','Audio not valid');
    }

    $img_path = public_path() . '/uploads/images/';
    $audio_path = public_path() . '/uploads/audios/';

    $img_ext = date("Y-m-d-H-i-s") . ".jpg";
    $audio_ext = date("Y-m-d-H-i-s") . ".mp3";

    $request->file('audio_image')->move($img_path, $img_ext);
    $request->file('audio_mp3')->move($audio_path, $audio_ext);

    $audio = new Audio();
    $audio->audio_name = $validatedData["audio_name"]; 
    $audio->audio_description = $validatedData["audio_description"];
    $audio->audio_image = "/uploads/images/" . $img_ext;
    $audio->audio_mp3 = "/uploads/audios/" . $audio_ext;
    $audio->user_id = auth()->user()->user_id;
    $audio->save();

    $this->send_fcm_notification("New Audio Message", "Visit the Library page to listen to the new audio message", "/topics/ALPHA", "ALPHA");
    return response(["status" => "success", "message" => "Audio added successsfully."]);

}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_audios(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $audios = DB::table('audio')
    ->select('audio.*')
    ->orderBy("audio_id", "desc")
    ->simplePaginate(50);
 
    for ($i=0; $i < count($audios); $i++) { 

        $date = date_create($audios[$i]->created_at);
        $audios[$i]->created_at = date_format($date,"M j Y");
        $audios[$i]->audio_image = URL::to('/') . $audios[$i]->audio_image;
        $audios[$i]->audio_mp3 = URL::to('/') . $audios[$i]->audio_mp3;
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $audios]);
}


public function delete_audio(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "audio_id" => "bail|required|max:18",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    $audio = Audio::find($request->audio_id);

    if(isset($audio) && $audio != null){
    
        unlink(".". $audio->audio_image);
        unlink(".". $audio->audio_mp3);
        $audio->delete();
        return response(["status" => "success", "message" => "Audio deleted successsfully."]);
    } else {
        return response(["status" => "fail", "message" => "Audio not found"]);
    }
}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_favorites(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "favorites" => "bail|required",
    ]);

    if(substr($request->favorites, -1) != "]" || substr($request->favorites, 0, 1) != "["){
        return response(["status" => "fail", "message" => "Parameter error"]);
    } 

    $favorites_ids = substr($request->favorites,1);
    $favorites_ids = substr($favorites_ids,0,-1);

    $favorites_ids_array = explode(" ", $favorites_ids);

    
    $where_array = array(
        ['audio_id', '=',  $favorites_ids_array[0]],
    ); 


    $audios = DB::table('audio')->select('audio.*')->where($where_array);
    
    for ($i=1; $i < count($favorites_ids_array) ; $i++) { 
        $audios->orWhere('audio_id', '=',  $favorites_ids_array[$i]);
    }
    
    $audios = $audios->orderBy("audio_id", "desc")
    ->simplePaginate(100);
 
    for ($i=0; $i < count($audios); $i++) { 

        $date = date_create($audios[$i]->created_at);
        $audios[$i]->created_at = date_format($date,"M j Y");
        $audios[$i]->audio_image = URL::to('/') . $audios[$i]->audio_image;
        $audios[$i]->audio_mp3 = URL::to('/') . $audios[$i]->audio_mp3;
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $audios]);
}



public function add_video(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "video_name" => "bail|required|max:45",
        "video_description" => "bail|required",
        "video_image" => "bail|required",
        "video_mp4" => "bail|required",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    if(!$request->hasFile('video_image')) {
        return response(["status" => "fail", "message" => "Image not found"]);
    }

    if(!$request->hasFile('video_mp4')) {
        return response(["status" => "fail", "message" => "Video not found"]);
    }

    if(!$request->file('video_image')->isValid()) {
        return response(["status" => "fail", "message" => "Image not valid"]);
    }

    if(!$request->file('video_mp4')->isValid()) {
        return response(["status" => "fail", "message" => "Video not valid"]);
    }

    $img_path = public_path() . '/uploads/images/';
    $video_path = public_path() . '/uploads/videos/';

    $img_ext = date("Y-m-d-H-i-s") . ".jpg";
    $video_ext = date("Y-m-d-H-i-s") . ".mp4";

    if (file_exists( $img_path . $img_ext)) {
        return response(["status" => "fail", "message" => "Storage error. Try again later"]);
    }

    if (file_exists( $img_path . $video_ext)) {
        return response(["status" => "fail", "message" => "Storage error. Try again later"]);
    }

    $request->file('video_image')->move($img_path, $img_ext);
    $request->file('video_mp4')->move($video_path, $video_ext);

    $video = new Video();
    $video->video_name = $validatedData["video_name"]; 
    $video->video_description = $validatedData["video_description"];
    $video->video_image = "/uploads/images/" . $img_ext;
    $video->video_mp4 = "/uploads/videos/" . $video_ext;
    $video->user_id = auth()->user()->user_id;
    $video->save();

    $this->send_fcm_notification("New Video", "Visit the Library page to watch the new videos", "/topics/ALPHA", "ALPHA");
    return response(["status" => "success", "message" => "Video added successsfully."]);

}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_videos(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $videos = DB::table('videos')
    ->select('videos.*')
    ->orderBy("video_id", "desc")
    ->simplePaginate(50);

    for ($i=0; $i < count($videos); $i++) { 
        $date = date_create($videos[$i]->created_at);
        $videos[$i]->created_at = date_format($date,"M j Y");
        $videos[$i]->video_image = URL::to('/') . $videos[$i]->video_image;
        $videos[$i]->video_mp4 = URL::to('/') . $videos[$i]->video_mp4;
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $videos]);
}



public function delete_video(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "video_id" => "bail|required|integer",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    $video = Video::find($request->video_id);

    if(isset($video) && $video != null){    
        unlink(".". $video->video_image);
        unlink(".". $video->video_mp4);
        $video->delete();
        return response(["status" => "success", "message" => "Video deleted successsfully."]);
    } else {
        return response(["status" => "fail", "message" => "Video not found"]);
    }
}



public function add_message(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "message_type" => "bail|required|max:50",
        "message_text" => "bail|required|max:200",
    ]);

    $message = new Message();
    $message->message_type = $validatedData["message_type"];
    $message->message_text = $validatedData["message_text"];
    $message->user_id = auth()->user()->user_id;
    $message->save();


        
    $email_data = array(
        'message_type' => $message->message_type,
        'message_text' => $message->message_text,
        'user_name' => auth()->user()->user_firstname . " " . auth()->user()->user_surname,
        'user_phone_number' => auth()->user()->user_phone_number,
        'user_email' => auth()->user()->user_email,
        'user_country' => auth()->user()->user_country,
        'time' => date("F j, Y, g:i a")
    );

    //Mail::to("media.christassembly@gmail.com")->send(new TheGloryHubMessageMail($email_data));
    //Mail::to("fishpottcompany@gmail.com")->send(new TheGloryHubMessageMail($email_data));
    //Mail::to("annodankyikwaku@gmail.com")->send(new TheGloryHubMessageMail($email_data));

    return response(["status" => "success", "message" => "Sent successsfully."]);

}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_prayer_requests(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $where_array = array(
        ['message_type', '=',  'Prayer Request'],
    ); 

    $messages = DB::table('messages')
    ->select('messages.*')
    ->where($where_array)
    ->orderBy("message_id", "desc")
    ->simplePaginate(50);
 
    for ($i=0; $i < count($messages); $i++) { 

        $date = date_create($messages[$i]->created_at);
        $messages[$i]->created_at = date_format($date,"M j Y");
        $this_user = DB::table('users')
        ->where("user_id", "=", $messages[$i]->user_id)
        ->get();
    
        if(isset($this_user[0])){
            $messages[$i]->user_full_name = $this_user[0]->user_firstname . " " . $this_user[0]->user_surname;
            $messages[$i]->user_phone = $this_user[0]->user_phone_number;
        } else {
            $messages[$i]->user_full_name = "[Unavailable]";
            $messages[$i]->user_phone = "[Unavailable]";
        }
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $messages]);
}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_feedbacks(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $where_array = array(
        ['message_type', '=',  'Feedback'],
    ); 

    $messages = DB::table('messages')
    ->select('messages.*')
    ->where($where_array)
    ->orderBy("message_id", "desc")
    ->simplePaginate(50);
 
    for ($i=0; $i < count($messages); $i++) { 

        $date = date_create($messages[$i]->created_at);
        $messages[$i]->created_at = date_format($date,"M j Y");
        $this_user = DB::table('users')
        ->where("user_id", "=", $messages[$i]->user_id)
        ->get();
    
        if(isset($this_user[0])){
            $messages[$i]->user_full_name = $this_user[0]->user_firstname . " " . $this_user[0]->user_surname;
            $messages[$i]->user_phone = $this_user[0]->user_phone_number;
        } else {
            $messages[$i]->user_full_name = "[Unavailable]";
            $messages[$i]->user_phone = "[Unavailable]";
        }
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $messages]);
}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_testimonies(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
         $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $where_array = array(
        ['message_type', '=',  'Testimonies'],
    ); 

    $messages = DB::table('messages')
    ->select('messages.*')
    ->where($where_array)
    ->orderBy("message_id", "desc")
    ->simplePaginate(50);
 
    for ($i=0; $i < count($messages); $i++) { 

        $date = date_create($messages[$i]->created_at);
        $messages[$i]->created_at = date_format($date,"M j Y");
        $this_user = DB::table('users')
        ->where("user_id", "=", $messages[$i]->user_id)
        ->get();
    
        if(isset($this_user[0])){
            $messages[$i]->user_full_name = $this_user[0]->user_firstname . " " . $this_user[0]->user_surname;
            $messages[$i]->user_phone = $this_user[0]->user_phone_number;
        } else {
            $messages[$i]->user_full_name = "[Unavailable]";
            $messages[$i]->user_phone = "[Unavailable]";
        }
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $messages]);
}


public function add_article(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "article_type" => "bail|required|max:100",
        "article_title" => "bail|required|max:30",
        "article_body" => "bail|required",
        "article_image" => "bail|required",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    if(!$request->hasFile('article_image')) {
        return response(["status" => "fail", "message" => "Image not found"]);
    }


    if(!$request->file('article_image')->isValid()) {
        return response(["status" => "fail", "message" => "Image not valid"]);
    }

    $img_path = public_path() . '/uploads/images/';
    
    $img_ext = uniqid() . date("Y-m-d-H-i-s") . ".jpg";

    $request->file('article_image')->move($img_path, $img_ext);

    $article = new Article();
    $article->article_type = $validatedData["article_type"]; 
    $article->article_title = $validatedData["article_title"];
    $article->article_body = $validatedData["article_body"];
    $article->article_title = $validatedData["article_title"];
    $article->article_image = "/uploads/images/" . $img_ext;
    $article->user_id = auth()->user()->user_id;
    $article->save();

    $this->send_fcm_notification("New Articles", "Visit the Read page to read new articles", "/topics/ALPHA", "ALPHA");
    return response(["status" => "success", "message" => "Article added successsfully."]);

}


public function delete_article(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "article_id" => "bail|required|max:18",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    $article = Article::find($request->article_id);

    if(isset($article) && $article != null){
    
        unlink(".". $article->article_image);
        $article->delete();
        return response(["status" => "success", "message" => "Article deleted successsfully."]);
    } else {
        return response(["status" => "fail", "message" => "Article not found"]);
    }
}


/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
    public function get_articles(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
        }

        if (auth()->user()->user_flagged) {
            $request->user()->token()->revoke();
            return response(["status" => "fail", "message" => "Account access restricted"]);
        }

        $articles = DB::table('articles')
        ->select('articles.*')
        ->orderBy("article_id", "desc")
        ->simplePaginate(50);
    
        for ($i=0; $i < count($articles); $i++) { 

            $date = date_create($articles[$i]->created_at);
            $articles[$i]->created_at = date_format($date,"M j Y");
            $articles[$i]->article_image = URL::to('/') . $articles[$i]->article_image;
        }

        return response(["status" => "success", "message" => "Operation successful", "data" => $articles]);
    }

public function update_notice(Request $request)
{

    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $validatedData = $request->validate([
        "notice_image" => "bail|required",
    ]);
    

    if(!$request->hasFile('notice_image')) {
        return response(["status" => "fail", "message" => "Image not found"]);
    }

    if(!$request->file('notice_image')->isValid()) {
        return response(["status" => "fail", "message" => "Image not valid"]);
    }


    $img_path = public_path() . '/uploads/images/';

    $img_ext = uniqid() . date("Y-m-d-H-i-s") . ".jpg";


    //$notice = Notice::find(1);
    /*
    $notice = DB::table('notices')
    ->select('notices.*')
    ->orderBy("notice_id", "desc")
    ->get();

    if(isset($notice[0]) && $notice[0]->notice_image != ""){ 

        $request->file('notice_image')->move($img_path, $img_ext);
        $notice = Notice::find($notice[0]->notice_id);
        unlink(".". $notice->notice_image);
        $notice->notice_image = "/uploads/images/" . $img_ext;
        $notice->user_id = auth()->user()->user_id;
        $notice->save();
    } else {
        $request->file('notice_image')->move($img_path, $img_ext);
        $notice = new Notice();
        $notice->notice_image = "/uploads/images/" . $img_ext;
        $notice->user_id = auth()->user()->user_id;
        $notice->save();
    }
    */

    $request->file('notice_image')->move($img_path, $img_ext);
    $notice = new Notice();
    $notice->notice_image = "/uploads/images/" . $img_ext;
    $notice->user_id = auth()->user()->user_id;
    $notice->save();


    $this->send_fcm_notification("New Notice", "Click to view the new notice", "/topics/ALPHA", "ALPHA");
    //return back()->with('success','File has been uploaded.');
    return response(["status" => "success", "message" => "Notice added successsfully."]);

}

public function get_dashboard(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $notices = DB::table('notices')
    ->select('notices.*')
    ->orderBy("notice_id", "desc")
    ->simplePaginate(2);
    
 
    for ($i=0; $i < count($notices); $i++) { 

        $date = date_create($notices[$i]->created_at);
        $notices[$i]->created_at = date_format($date,"M j Y");
        $notices[$i]->notice_image = URL::to('/') . $notices[$i]->notice_image;
    }


    $videos = DB::table('videos')
    ->select('videos.*')
    ->orderBy("video_id", "desc")
    ->simplePaginate(2);
    

    for ($i=0; $i < count($videos); $i++) { 
        $date = date_create($videos[$i]->created_at);
        $videos[$i]->created_at = date_format($date,"M j Y");
        $videos[$i]->video_image = URL::to('/') . $videos[$i]->video_image;
        $videos[$i]->video_mp4 = URL::to('/') . $videos[$i]->video_mp4;
    }

    $audios = DB::table('audio')
    ->select('audio.*')
    ->orderBy("audio_id", "desc")
    ->simplePaginate(1);
 
    for ($i=0; $i < count($audios); $i++) { 

        $date = date_create($audios[$i]->created_at);
        $audios[$i]->created_at = date_format($date,"M j Y");
        $audios[$i]->audio_image = URL::to('/') . $audios[$i]->audio_image;
        $audios[$i]->audio_mp3 = URL::to('/') . $audios[$i]->audio_mp3;
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $notices, "audios" => $audios, "videos" => $videos]);
}

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| THIS FUNCTION GETS THE LIST OF ALL THE RATES
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
|
*/
public function get_transaction_id(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }


    $validatedData = $request->validate([
        "amount" => "bail|required|integer",
        "reason" => "bail|required",
    ]);

    $ref_num = auth()->user()->user_id . uniqid() . date('ymdhis');
    $transaction = new Transaction();
    $transaction->transaction_ext_id = $ref_num; 
    $transaction->amount = $validatedData["amount"];
    $transaction->reference = $validatedData["reason"];
    $transaction->user_id = auth()->user()->user_id;
    $transaction->status_description = "[not set]"; 
    $transaction->save();


    return response([
        "status" => "success", 
        "message" => $ref_num, 
        "app_user" => "caw5fc4efa195d0c", 
        "app_key" => "ZTI0ZTBiMWQ5YWQxMGJmZTE3NTQ0OWE0NTg3OWRkZGU=", 
        "merchant_id" => "eaef24e8-13e2-11e9-b63f-f23c9170642f"
        ]);
        
        // "merchant_id" => "tk_eaefc074-13e2-11e9-b63f-f23c9170642f"
        //"merchant_id" => "TTM-00004771"
}


public function update_transaction(Request $request)
{
    $validatedData = $request->validate([
        "status" => "bail|required",
        "reason" => "bail|required",
        "transaction_id" => "bail|required",
    ]);

    
    $transaction = Transaction::where('transaction_ext_id', $request->transaction_id)->first();
    $transaction->status = $request->status; 
    $transaction->status_description = utf8_decode(urldecode($request->reason));
    $transaction->save();

}


public function get_payments(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }

    $transactions = DB::table('transactions')
    ->select('transactions.*')
    ->orderBy("transaction_id", "desc")
    ->simplePaginate(100);

    for ($i=0; $i < count($transactions); $i++) { 

        $date = date_create($transactions[$i]->created_at);
        $transactions[$i]->created_at = date_format($date,"M j Y");

        $user = User::find($transactions[$i]->user_id);
        $transactions[$i]->payer_name = $user->user_firstname . " " . $user->user_surname;
        $transactions[$i]->payer_phone = $user->user_phone_number;
        $transactions[$i]->payer_email = $user->user_email;
        $transactions[$i]->payer_country = $user->user_country;
    }

    return response(["status" => "success", "message" => "Operation successful", "data" => $transactions]);
}


public function send_notification(Request $request)
{
    if (!Auth::guard('api')->check()) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (!$request->user()->tokenCan('do_admin_things')) {
        return response(["status" => "fail", "message" => "Permission Denied. Please log out and login again"]);
    }

    if (auth()->user()->user_flagged) {
        $request->user()->token()->revoke();
        return response(["status" => "fail", "message" => "Account access restricted"]);
    }


    $validatedData = $request->validate([
        "notification_title" => "bail|required|max:20",
        "notification_message" => "bail|required|max:50",
    ]);

    $this->send_fcm_notification($request->notification_title, $request->notification_message, "/topics/ALPHA", "ALPHA");

    return response(["status" => "success", "message" => "Operation successful"]);
}

public function send_fcm_notification($title,$body,$target,$chid)
   {

    define( 'API_ACCESS_KEY', 'AAAABb3fzMY:APA91bFeAZ6QQwlQoiiugGLWUARoh4gf3avvcdLJNIlEWv2kBljnpOL3leahkgk4FArNuzk_ejZbE74aDjuEj1vSAWLAYKAneHJEmXhzjEZFJC3SlgfZRqNW3ZOTwlHMyuPXYh6oLwok' );

  $fcmMsg = array(
    'title' => $title,
    'body' => $body,
    'channelId' => $chid,

  );
  $fcmFields = array(
    'to' => $target, //tokens sending for notification
    'notification' => $fcmMsg,

  );

  $headers = array(
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json'
  );

$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
$result = curl_exec($ch );
curl_close( $ch );
//echo $result . "\n\n";

}


}
