<?php
//%sE7RbhU+,g$[.BT
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            "password" => "bail|required|confirmed|min:8|max:30",
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

        $user = User::create($validatedData);

        $accessToken = $user->createToken("authToken", [$validatedData["user_scope"]])->accessToken;
        
        $return = [
            "status" => 1,
            "user" => $user, 
            "access_token" => $accessToken
        ];
        return response()->json($return, 200);
        /*
        return response();
        */
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


        $return = [
            "status" => 1,
            "user" => auth()->user(), 
            "access_token" => $accessToken
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
        "audio_name" => "bail|required|max:25",
        "audio_description" => "bail|required|max:100",
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

    //return back()->with('success','File has been uploaded.');
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
        "video_name" => "bail|required|max:25",
        "video_description" => "bail|required|max:100",
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

    $img_ext = "notice_sanctum" . ".jpg";

    $request->file('notice_image')->move($img_path, $img_ext);

    $notice = Notice::find(1);

    if($notice != null && $notice->notice_id == 1){
        $notice->notice_image = "/uploads/images/" . $img_ext;
        $notice->user_id = auth()->user()->user_id;
        $notice->save();
    } else {
        $notice = new Notice();
        $notice->notice_image = "/uploads/images/" . $img_ext;
        $notice->user_id = auth()->user()->user_id;
        $notice->save();
    }


    //return back()->with('success','File has been uploaded.');
    return response(["status" => "success", "message" => "Notice updated successsfully."]);

}

public function get_notices(Request $request)
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
    ->simplePaginate(50);

    return response(["status" => "success", "message" => "Operation successful", "data" => $notices]);
}



}
