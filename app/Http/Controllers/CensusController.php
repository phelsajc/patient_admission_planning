<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\UserActions;
use Illuminate\Support\Facades\Auth;
use App\Model\BedCapacity;
use DateTime;

class CensusController extends Controller
{
    public function __construct()
    {
        $this->middleware('JWT');
    }

    public function getStations()
    {
        $data = DB::connection('pgsql')->select("select * from bed_capacity order by id");
        $returnStn = array();
        foreach ($data as $key => $value) {
            $getBizboxDetail = self::getBizboxData($value->station);
            $arr = array();
            $arr['station'] = $value->station;
            $arr['licensed'] = $value->licensed;
            $arr['target'] = $value->target;
            $arr['manpower'] = $value->manpower;
            $arr['functional'] = $value->functional;
            $arr['occupied'] = $getBizboxDetail['admissionPerStrn'];
            $arr['mgh'] = $getBizboxDetail['mghPerStrn'];
            $arr['reservation'] = $value->reservation;
            $arr['er'] = $value->er;
            $arr['available'] = $value->available;
            $arr['reserved'] = $value->reserved;
            $arr['ratio'] = $value->ratio;
            $returnStn[] = $arr;
        }
        $datasets["data"] = $returnStn;
        return response()->json($datasets);
    }

    public function getBizboxData($station)
    {
        $admissionPerStrn = DB::connection('bizbox')->select("SELECT t3.description, COUNT(1) AS census 
        FROM pspatregisters AS t1
        INNER JOIN psadmissions AS t2
        on t1.pk_pspatregisters = t2.fk_pspatregisters
        LEFT JOIN mscnrstation AS t3
        on t2.fk_mscnrstation = t3.pk_mscnrstation
        WHERE t1.pattrantype = 'I' and t1.registrystatus NOT IN ('X','D')
        and t3.description =  '$station'
        GROUP BY t3.description
                        ");
        $mghPerStrn = DB::connection('bizbox')->select("SELECT t3.description, COUNT(1) AS census 
        FROM pspatregisters AS t1
        INNER JOIN psadmissions AS t2
        on t1.pk_pspatregisters = t2.fk_pspatregisters
        LEFT JOIN mscnrstation AS t3
        on t2.fk_mscnrstation = t3.pk_mscnrstation
        WHERE t1.pattrantype = 'I' and t1.registrystatus IN ('M')
        and t3.description =  '$station'
        GROUP BY t3.description
                                        ");

        $getAdmissionPerStrn = 0;
        foreach ($admissionPerStrn as $key => $value) {
            $getAdmissionPerStrn = $value->census;
        }
        $getMghPerStrn = 0;
        foreach ($mghPerStrn as $key => $value) {
            $getMghPerStrn = $value->census;
        }
        return array(
            'admissionPerStrn' => array(
                'census' => $getAdmissionPerStrn,
                'stn' => $station,
            ),
            'mghPerStrn' => array(
                'census' => $getMghPerStrn,
                'stn' => $station,
            ),
        );
    }

    public function UpdateInfo(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $check = BedCapacity::where(['station' => $request->stn])->first();
        if ($check) {
            
            $a = new UserActions;
            $a->idno = Auth::user()->username;
            $a->actions = Auth::user()->username." updated ".$request->stn." reservation from ".$check->reservation." to ".$request->reservation." and er from ".$check->er." to ".$request->er;
            $a->ipaddress = $request->ip();
            $a->date_attempt = date("Y-m-d H:i:s");
            $a->save();

            
            $available = $check->functional-$request->occupied+$request->mgh-$request->reservation-$request->er;
            BedCapacity::where(['id'=>$check->id])->update([
                'occupied'=>$request->occupied,
                'mgh'=>$request->mgh,
                'reservation'=>$request->reservation,
                'er'=>$request->er,
                'available'=>$available,
                ]
            );
            return true;
        }
    }


}
