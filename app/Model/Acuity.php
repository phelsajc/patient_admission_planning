<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Acuity extends Model
{
    protected $table = "patient_with_acuity";

    protected $primaryKey = "id";
    
    public $timestamps = false;

    protected $fillable = [
        'id',
        'acuity',
        'roomno',
        'status',
        'patientid',
        'name',
        'station',
        'registrydate',
        'created_dt',
        'erdt',
        'dschdt',
        'hrs',
        'created_by',
    ];
}
