<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BedCapacityCensus extends Model
{
    protected $table = "bed_capacity";
    protected $connection = 'rmci_census_monitoring';

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'capacity',
        'sation',
    ];
}
