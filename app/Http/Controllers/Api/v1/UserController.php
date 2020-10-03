<?php
//%sE7RbhU+,g$[.BT
namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ResetcodeMail;
use App\Models\v1\Resetcode;
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

        $validatedData["user_scope"] = "get_dashboard";
        $validatedData["password"] = bcrypt($request->password);
        $validatedData["user_flagged"] = false;

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
        $log_controller = new LogController();

        $login_data = $request->validate([
            "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15",
            "password" => "required"
        ]);

        if (!auth()->attempt($login_data)) {
            $log_controller->save_log("member", $request->user_phone_number, "Login|User", "Login failed");
            return response(["status" => 0, "message" => "Invalid Credentials"]);
        }

        if (auth()->user()->user_flagged) {
            $log_controller->save_log("member", $request->user_phone_number, "Login|User", "Login failed because user is flagged");
            return response(["status" => 0, "message" => "Account access restricted"]);
        }

        $accessToken = auth()->user()->createToken("authToken", [auth()->user()->user_scope])->accessToken;

        $log_controller->save_log("member", $request->user_phone_number, "Login|User", "Login successful");


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
        $log_controller = new LogController();
        $resetcode_controller = new ResetcodeController();

        $request->validate([
            "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15"
        ]);

        $user = User::where('user_phone_number', $request->user_phone_number)->first();

        
        if(!isset($user->user_id)) {
            $log_controller->save_log("member", $request->user_phone_number, "ResetPassword|User", "Operation failed because user was not found");
            return response(["status" => 0, "message" => "Account not found"]);
        }

        if($user->user_flagged) {
            $log_controller->save_log("member", $request->user_phone_number, "ResetPassword|User", "Operation failed because user is flagged");
            return response(["status" => 0, "message" => "Account access restricted"]);
        }

        $resetcode = $resetcode_controller->generate_resetcode();

        $email_data = array(
            'reset_code' => $resetcode,
            'time' => date("F j, Y, g:i a")
        );

        $resetcode_controller->save_resetcode("member", $user->user_id, strval($resetcode));

        Mail::to($user->user_email)->send(new ResetcodeMail($email_data));

        $log_controller->save_log("member", $request->user_phone_number, "ResetPassword|User", "Reset code sent for password reset");

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
    $log_controller = new LogController();
    $resetcode_controller = new ResetcodeController();

    $request->validate([
        "user_phone_number" => "bail|required|regex:/^\+\d{1,3}[0-9]{9}/|min:10|max:15",
        "resetcode" => "bail|required|max:7",
        "password" => "bail|required|confirmed|min:8|max:30"
    ]);

    $user = User::where('user_phone_number', $request->user_phone_number)->first();

    if(!isset($user->user_id)) {
        $log_controller->save_log("member", $request->user_phone_number, "ResetPassword|User", "Operation failed because user was not found");
        return response(["status" => 0, "message" => "Account not found"]);
    }

    if($user->user_flagged) {
        $log_controller->save_log("member", $request->user_phone_number, "ResetPassword|User", "Operation failed because user is flagged");
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






}
