<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserActions extends Model
{
    protected $table = "user_actions";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'idno',
        'actions',
        'ipaddress',
        'date_attempt',
    ];
}
