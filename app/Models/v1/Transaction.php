<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    
    use Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'transaction_ext_id', 
        'amount',
        'reference',
        'payment_type',
        'status',
        'status_description',
        'user_id',
        'payer_name',
        'payer_phone',
        'payer_email',
        'payer_country',
        'created_at',
        'updated_at',
    ];
}
