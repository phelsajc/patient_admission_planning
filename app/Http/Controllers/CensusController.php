<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\UserActions;
use Illuminate\Support\Facades\Auth;
use App\Model\BedCapacity;
use App\Model\BedCapacityCensus;
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
        $datasets["data2"] = $this->daily_census();
        return response()->json($datasets);
    }

    public function daily_census(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        /* $current_date = date("d") ;
        $current_month = date("m") ; */
        $getYr = date_format(date_create($request->fdate), 'Y');
        $getDate = date_format(date_create($request->fdate), 'm');
        $YrDt = $getYr . "-" . $getDate;
        $current_year = date("Y");
        $dailyTarget = $this->getTargetCensus($getDate);//157
        //$simulatedActual = 183;
        $date = DateTime::createFromFormat("Y-n", $YrDt);
        $totalDailyTarget = 0;
        $d = cal_days_in_month(CAL_GREGORIAN, $getDate, $getYr);
        $datesArray = array();
        $totalDailyTarget = $d * $dailyTarget;
        $formula1 = $totalDailyTarget - $dailyTarget;
        $formula2 = $formula1;

        //$simulatedActual = $this->getDailyCensus($YrDt."-1",$YrDt."-1");//154;
        
        /* $actual_formula1 = $totalDailyTarget-$simulatedActual;
        $actual_formula2 = $actual_formula1; */
        $actual_formula2 = 0;
        for ($i = 1; $i <= $date->format("t"); $i++) {
            $simulatedActual = $this->getDailyCensus($YrDt . "-" . $i, $YrDt . "-" . $i); //154;
            $actual_formula1 = $totalDailyTarget - $simulatedActual;
            $totalDailyTarget = $actual_formula1;
            $actual_formula2 = $actual_formula1;
            //$storeActual= $actual_formula1;
            //$actual_formula2 = $actual_formula1-$simulatedActual;
            $d = $d - 1;
            $ac = $i == 1 && $simulatedActual > 0 ? $actual_formula1 : $actual_formula2;
            $nt = $d > 0 ? $ac / $d : 0;
            $arr = array();
            $arr['day'] = date('l', strtotime($current_year . "-" . $getDate . "-" . $i));
            $arr['date'] = $i;
            $arr['dateF'] = DateTime::createFromFormat("Y-n-d", $YrDt . "-$i")->format("Y-m-d");
            $arr['target'] = $dailyTarget;
            $arr['actual'] = $simulatedActual;
            $arr['new_target'] = $simulatedActual == 0 ? $dailyTarget : round($nt);
            $arr['req_daily_census'] = round($nt);
            $arr['target_balance'] = $i == 1 ? $formula1 : $formula2;
            $arr['actual_balance'] = $ac;
            $formula2 -= $dailyTarget;
            $actual_formula2 -= $simulatedActual;
            $datesArray[] = $arr;
        }
        $datasets["data"] = $datesArray;
        $datasets["getYr"] = $getYr;
        $datasets["getDate"] = $getDate;
        $datasets["fulldt"] = date_format(date_create($request->fdate), 'Y-m-d');
        $datasets["t"] = $date->format("t");
        $datasets["t"] = $date;

        //return $datesArray;//response()->json($month_arr);
        return response()->json($datasets);
    }

    public function getTargetCensus($month)
    {
        switch ($month) {
            case 1:
                return 153;
            case 2:
                return 134;
            case 3:
                return 147;
            case 4:
                return 149;
            case 5:
                return 153;
            case 6:
                return 166;
            case 7:
                return 155;
            case 8:
                return 169;
            case 9:
                return 170;
            case 10:
                return 155;
            case 11:
                return 169;
            default:
                return 166;
            //code block
        }
    }

    public function getDailyCensus($f, $t)
    {
        date_default_timezone_set('Asia/Manila');
        $length = 10;
        $start = 0;
        $fdate = date_format(date_create($f), 'Y-m-d');
        $tdate = date_format(date_create($t), 'Y-m-d');
        $stnArrToStr = '';

        //echo $fdate.' - '.$tdate.' | ';
        $station = DB::connection('rmci_census_monitoring')->select("select station from census group by station");
        foreach ($station as $key => $valueStn) {
            $stnArrToStr .= "'" . $valueStn->station . "',";
        }
        $sql_q = substr($stnArrToStr, 0, -1);
        $data = DB::connection('pgsql')->select("SELECT station from census where station in ($sql_q)  group by station");
        //$data = DB::connection('rmci_census_monitoring')->select("SELECT count(station) as totalstn,station,created_dt from census group by station,created_dt LIMIT $length");

        $totalRegularBed = 0;
        /* $totalRegularBed_arr = array(); */

        foreach ($data as $key => $value2) {
            $keyword = $value2->station;
            $data_query2 = DB::connection('rmci_census_monitoring')->select("SELECT count(station) as totalstn,station,created_dt,STRING_AGG(cast (newbornstatus as text), '|') AS nb_list 
             from census where station = '$keyword' and  
            date(created_dt) between '$fdate' and '$tdate' /* and station not in ('NURSERY','PEDIA WARD')  */
            /* newbornstatus != 'c' */  group by station,created_dt order by created_dt asc");
            foreach ($data_query2 as $key => $value) {
                $explode_nb_list = explode("|", $value->nb_list);
                if ($keyword == "NURSERY") {
                    $keyword = "NICU";
                }
                $getCapacity = BedCapacityCensus::where(['station' => $keyword])->first();
                if ($getCapacity != null) {
                    $totalRegularBed += $value->totalstn;
                }
            }

        }
        $datasets["totalRegularBed"] = $totalRegularBed;
        //$datasets["totalRegularBed_arr"] = $totalRegularBed_arr;
        return $totalRegularBed; //response()->json($datasets);
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
            $a->actions = Auth::user()->username . " updated " . $request->stn . " reservation from " . $check->reservation . " to " . $request->reservation . " and er from " . $check->er . " to " . $request->er;
            $a->ipaddress = $request->ip();
            $a->date_attempt = date("Y-m-d H:i:s");
            $a->save();


            $available = $check->functional - $request->occupied + $request->mgh - $request->reservation - $request->er;
            BedCapacity::where(['id' => $check->id])->update(
                [
                    'occupied' => $request->occupied,
                    'mgh' => $request->mgh,
                    'reservation' => $request->reservation,
                    'er' => $request->er,
                    'available' => $available,
                ]
            );
            return true;
        }
    }


}
