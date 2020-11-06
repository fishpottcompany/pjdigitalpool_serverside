<?php
//%sE7RbhU+,g$[.BT
namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use App\Models\v1\Audio;
use App\Mail\ResetcodeMail;
use App\Models\v1\Resetcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        "audio_name" => "bail|required|max:18",
        "audio_description" => "bail|required|max:100",
        "audio_image" => "bail|required",
        "audio_mp3" => "bail|required",
        "user_pin" => "bail|required|min:4|max:8",
    ]);

    if(!$request->hasFile('audio_image')) {
        return response(["status" => "fail", "message" => "Image not found"]);
    }

    if(!$request->hasFile('audio_mp3')) {
        return response(["status" => "fail", "message" => "Audio not found"]);
    }

    if(!$request->file('audio_image')->isValid()) {
        return response(["status" => "fail", "message" => "Image not valid"]);
    }

    if(!$request->file('audio_mp3')->isValid()) {
        return response(["status" => "fail", "message" => "Audio not valid"]);
    }

    $img_path = public_path() . '/uploads/images/';
    $audio_path = public_path() . '/uploads/audios/';

    $img_ext = date("Y-m-d-H-i-s") . ".png";
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
    ->simplePaginate(50);

    return response(["status" => "success", "message" => "Operation successful", "data" => $audios]);
}


}
