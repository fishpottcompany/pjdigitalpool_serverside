<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Resetcode extends Model
{
    use Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'resetcode_id';



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resetcode_id',
        'user_type', 
        'user_id',
        'resetcode',
        'used',
        'created_at',
        'updated_at',
    ];
}
