<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Resetcode;
use Illuminate\Http\Request;

class ResetcodeController extends Controller
{
    public function generate_resetcode()
    {
        return rand(1000000,9999999);
    }

    public function save_resetcode($user_type, $user_id, $thisresetcode)
    {
        $resetcode = new Resetcode();
        $resetcode->user_type = $user_type; 
        $resetcode->user_id = $user_id;
        $resetcode->resetcode = $thisresetcode;
        $resetcode->used = false;
        $resetcode->save();

    }

    public function update_resetcode($thisresetcode_id, $user_type, $user_id, $thisresetcode, $used_status){

        $resetcode = Resetcode::find($thisresetcode_id);
        $resetcode->resetcode_id = $thisresetcode_id; 
        $resetcode->user_type = $user_type; 
        $resetcode->user_id = $user_id;
        $resetcode->resetcode = $thisresetcode;
        $resetcode->used = $used_status;
        $resetcode->save();
    }
}
