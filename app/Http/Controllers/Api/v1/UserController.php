<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\v1\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    $validatedData["user_scope"] = "register-user";
    $validatedData["password"] = bcrypt($request->password);
    $validatedData["user_flagged"] = false;

    $user = User::create($validatedData);

    $accessToken = $user->createToken("authToken", [$validatedData["user_scope"]])->accessToken;

    return response(["user" => $user, "access_token" => $accessToken]);
}


}
