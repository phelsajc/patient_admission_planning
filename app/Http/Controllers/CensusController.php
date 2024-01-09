<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\Acuity;
use Illuminate\Support\Facades\Auth;
use App\Model\BedCapacity;
use DateTime;

class CensusController extends Controller
{
    public function __construct()
    {
        $this->middleware('JWT');
    }

    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $length = 10;
        $start = $request->start ? $request->start : 0;
        $station = $request->stns;
        $stnArrToStr = '';
        if (sizeof($request->stns) == 1 && $request->stns[0] == 'All') {
            $station = DB::connection('pgsql')->select("select station from census group by station");
            foreach ($station as $key => $valueStn) {
                $stnArrToStr .= "'" . $valueStn->station . "',";
            }
        } else {
            foreach ($station as $key => $valueStn) {
                $stnArrToStr .= "'" . $valueStn . "',";
            }
        }
        //$stnArrToStr = str_replace("NURSERY","NICU",$stnArrToStr);
        /*  return response()->json($stnArrToStr); */
        $fdate = date_format(date_create($request->fdate), 'Y-m-d');
        $tdate = date_format(date_create($request->tdate), 'Y-m-d');
        $sql_q = substr($stnArrToStr, 0, -1);
        if (count($station) != 0 || ($fdate != '' && $tdate != '')) {
            //$data = DB::connection('pgsql')->select("SELECT count(station) as totalstn,station,created_dt from census where station in ($sql_q) and  date(created_dt) between '$fdate' and '$tdate' group by station,created_dt LIMIT $length offset $start");
            $data = DB::connection('pgsql')->select("SELECT station from census where station in ($sql_q)  group by station");
        } else {
            $data = DB::connection('pgsql')->select("SELECT count(station) as totalstn,station,created_dt from census group by station,created_dt LIMIT $length");
        }

        $data_array = array();
        $arr3 = array();
        $totalRegularBed = 0;
        $totalRegularBed_arr = array();
        foreach ($data as $key => $value2) {
            /* $data_query2 = DB::connection('pgsql')->select("SELECT count(station) as totalstn,station,created_dt from census where station = '$value2->station' and  
            date(created_dt) between '$fdate' and '$tdate' group by station,created_dt LIMIT $length offset $start");    */
            $keyword = $value2->station;
            $data_query2 = DB::connection('pgsql')->select("SELECT count(station) as totalstn,station,created_dt,STRING_AGG(cast (newbornstatus as text), '|') AS nb_list 
             from census where station = '$keyword' and  
            date(created_dt) between '$fdate' and '$tdate' /* and station not in ('NURSERY','PEDIA WARD')  */
            /* newbornstatus != 'c' */  group by station,created_dt order by created_dt asc LIMIT $length offset $start ");
            $data_array2 = array();
            $arr = array();
            $arr2 = array();
            $getTotalOccupancyRate = 0;
            foreach ($data_query2 as $key => $value) {
                $explode_nb_list = explode("|", $value->nb_list);
                if ($keyword == "NURSERY") {
                    $keyword =  "NICU";
                }
                $count_nb_with_c = 0;

                foreach ($explode_nb_list as $bnkey => $nbvalue) {
                    if ($nbvalue == "C") {
                        $count_nb_with_c++;
                    }
                }

                $getCapacity = BedCapacity::where(['station' => $keyword])->first();
                if ($getCapacity != null) {

                    $arr2['date'] =  date_format(date_create($value->created_dt), 'F d Y');
                    $arr['station'] =  $value2->station;
                    //$arr2['station'] =  $value->station;
                    $arr2['bedCapacity'] = $getCapacity->capacity;
                    $arr2['nb'] = $explode_nb_list;

                    //set 0 for nursery 
                    //set 0 for pedia ward although its always 0 atm.
                    if ($value2->station == "NURSERY" || $value2->station == "PEDIA WARD") {

                        $arr2['bassinets'] = 0;
                    } else {

                        $arr2['bassinets'] = $count_nb_with_c;
                    }

                    // $arr2['bassinets'] = $count_nb_with_c;
                    $arr2['occupiedBeds'] = $value->totalstn;
                    $arr2['regularBed'] = $value->totalstn;

                    $bedCapacity =  $getCapacity->capacity;
                    //$arr2['bedCapacity'] = $bedCapacity;
                    $getBedCap = 0;
                    //less the C here
                    //if($count_nb_with_c>=1&&($value2->station!="NURSERY"||$value2->station!="PEDIA WARD")){
                    // if($count_nb_with_c>=1&&$value2->station!="NURSERY"&&$value2->station!="PEDIA WARD"){
                    if ($count_nb_with_c >= 1 && $value2->station != "NURSERY" && $value2->station != "PEDIA WARD") {
                        if ($value2->station == "STATION 11") {
                            //$bedCapacity = $value->totalstn - $count_nb_with_c;
                            $nb = $value->totalstn - $count_nb_with_c;
                            $occupany_rate =  ($value->totalstn / $bedCapacity) * 100;
                            $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '') . '%';
                            $arr2['occupanyRate'] = $perOccupanyRate;
                            $getTotalOccupancyRate += (float)$perOccupanyRate;
                            $arr2['getformula'] =  $value->totalstn . '- ' . $count_nb_with_c . ' =' . $bedCapacity . ' ' . $value->totalstn . ' /' . $bedCapacity;
                            $arr2['occupiedBeds_2'] = $nb; //$bedCapacity;
                            $getBedCap = $bedCapacity;
                            //$totalRegularBed += $bedCapacity;
                        } else {
                            //$bedCapacity = $bedCapacity - $count_nb_with_c;
                            $bedCapacity = $value->totalstn - $count_nb_with_c;
                            $occupany_rate =  ($value->totalstn / $bedCapacity) * 100;
                            $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '') . '%';
                            $arr2['occupanyRate'] = $perOccupanyRate;
                            $getTotalOccupancyRate += (float)$perOccupanyRate;
                            $arr2['getformula'] =  $value->totalstn . '- ' . $count_nb_with_c . ' =' . $bedCapacity . ' ' . $value->totalstn . ' /' . $bedCapacity;
                            $arr2['occupiedBeds_2'] = $bedCapacity;
                            $getBedCap = $bedCapacity;
                            //$totalRegularBed += $bedCapacity;
                        }
                    } else {
                        $occupany_rate =  ($value->totalstn / $getCapacity->capacity) * 100;
                        $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '') . '%';
                        $arr2['occupanyRate'] = $perOccupanyRate;
                        $getTotalOccupancyRate += (float)$perOccupanyRate;

                        $arr2['occupiedBeds_2'] = $value->totalstn;
                        $getBedCap = $bedCapacity;
                        //$totalRegularBed += $value->totalstn;
                    }
                    $totalRegularBed += $value->totalstn;
                    $totalRegularBed_arr[] =  $value->totalstn . '-' . $value2->station;
                    //$totalRegularBed+=$value->totalstn;

                    //$totalRegularBed=$totalRegularBed+$bedCapacity;

                    $subQuery = DB::connection('pgsql')->select("SELECT created_dt,STRING_AGG(cast (registrydate as text), '|') AS reg_dt_list,station 
                    from census  where station = '$value2->station'
                        and date(created_dt) between '$fdate' and '$tdate'
                        group by 1,3");

                    $getAlos = array();
                    $getAlos3 = array();
                    $getAlosDT = array();
                    $countAlos = 0;
                    foreach ($subQuery as $xkey => $xvalue) {
                        $explode_dt = explode("|", $xvalue->reg_dt_list);
                        foreach ($explode_dt as $skey => $svalue) {
                            if (date_format(date_create($xvalue->created_dt), 'Y-m-d') == date_format(date_create($value->created_dt), 'Y-m-d')) {
                                $now = time();
                                $your_date = strtotime($svalue);
                                $datediff = $now - $your_date;
                                $c = round($datediff / (60 * 60 * 24));
                                $countAlos += $c;
                                $getAlos[] =  $countAlos;
                                $getAlos3[] =  $c;
                                $getAlosDT[] =  $svalue;
                            }
                        }
                    }
                    $formula = $countAlos / $value->totalstn;
                    $arr2['alos'] = number_format((float)$formula, 2, '.', '');
                    $arr2['formula'] = $countAlos;
                    $arr2['alos2'] = $getAlos;
                    $arr2['alos3'] = $getAlos3;
                    $arr2['alosDT'] = $getAlosDT;
                    $arr2['now'] = date('m/d/Y', time());


                    $data_array2[] = $arr2;



                    $arr['station_detail'] = $data_array2;
                }
            }

            //$arr['total'] =  $arr2;
            if ($arr) {
                $arr2['occupanyRate'] = '9999%';
                $GrandTotal =  $getTotalOccupancyRate / sizeof($data_query2);
                $arr['total'] =  number_format((float)$GrandTotal, 2, '.', '') . '%'; //'9999%';//$arr3;
                $data_array[] = $arr;
            }
            /* 
                $arr = array();
                $data_array2 = array();
                $arr2 = array();
                $arr['date'] =  date_format(date_create($value->created_dt), 'F d Y');
                $arr2['date'] =  date_format(date_create($value->created_dt), 'F d Y');
                
                $arr['station'] =  $value->station;
                $arr2['station'] =  $value->station;

                $getCapacity = BedCapacity::where(['station' => $value->station])->first();
                $arr['bedCapacity'] = $getCapacity->capacity;
                $arr2['bedCapacity'] = $getCapacity->capacity;

                $arr['occupiedBeds'] = $value->totalstn;
                $arr2['occupiedBeds'] = $value->totalstn;

                $occupany_rate =  ($value->totalstn / $getCapacity->capacity) * 100;
                $arr['occupanyRate'] = number_format((float)$occupany_rate, 2, '.', '');
                $arr2['occupanyRate'] = number_format((float)$occupany_rate, 2, '.', '');
                $subQuery = DB::connection('pgsql')->select("SELECT created_dt,STRING_AGG(cast (registrydate as text), '|') AS reg_dt_list,station 
                from census  where station = '$value->station'
                and date(created_dt) between '$fdate' and '$tdate'
                group by 1,3");

                $getAlos = array();
                $countAlos = 0;
                foreach ($subQuery as $xkey => $xvalue) {
                    $explode_dt = explode("|", $xvalue->reg_dt_list);
                    foreach ($explode_dt as $skey => $svalue) {
                        if (date_format(date_create($xvalue->created_dt), 'Y-m-d') == date_format(date_create($value->created_dt), 'Y-m-d')) {
                            $now = time();
                            $your_date = strtotime($svalue);
                            $datediff = $now - $your_date;
                            $c = round($datediff / (60 * 60 * 24));
                            $countAlos += $c;
                            $getAlos[] =  $countAlos;
                        }
                    }
                }
                $formula = $countAlos / $value->totalstn;
                $arr['alos'] = number_format((float)$formula, 2, '.', '');
                $arr2['alos'] = number_format((float)$formula, 2, '.', '');
                $arr['formula'] = $countAlos;
                $arr2['formula'] = $countAlos;
                $arr['alos2'] = $getAlos;
                $arr2['alos2'] = $getAlos;
                $data_array2[] = $arr2;
                $arr['station_detail'] = $data_array2;
                $data_array[] = $arr; 
            */
        }
        $datasets["stns"] = sizeof($request->stns);
        $datasets["data"] = $data_array;
        $datasets["totalRegularBed"] = $totalRegularBed;
        $datasets["totalRegularBed_arr"] = $totalRegularBed_arr;
        $datasets["stnArrToStr"] = $stnArrToStr;
        $datasets["Q1"] = "SELECT station from census where station in ($sql_q)  group by station";
        $datasets["Q2"] = "SELECT count(station) as totalstn,station,created_dt from census group by station,created_dt LIMIT $length";
        return response()->json($datasets);
    }

    public function getSDtations()
    {
        $data = DB::connection('pgsql')->select("select station from census group by station");
        $returnStn = array();
        foreach ($data as $key => $value) {
            $arr = array();
            $arr['station'] =  $value->station;
            $returnStn[] = $arr;
        }
        $datasets["data"] = $returnStn;
        return response()->json($datasets);
    }

    public function getErList(Request $request)
    {

        $fdate = date_format(date_create($request->fdate), 'Y-m-d') . ' 00:00:00';
        $tdate = date_format(date_create($request->tdate), 'Y-m-d') . ' 23:59:29';
        $data = DB::connection('bizbox')->select("SELECT	dbo.udf_GetFullName(reg.FK_emdPatients) AS PatientName,						
                    erdatetime,					
                    (SELECT MIN(occupystartdate) FROM psAdmRooms AS adRm WHERE FK_psPatRegisters = reg.PK_psPatRegisters) AS AdmissionDate, 					
                    CASE WHEN ISNULL((SELECT COUNT(1) FROM psAdmissions AS adm
                    where adm.FK_psPatRegisters = reg.PK_psPatRegisters),0) > 0 THEN 'ER To IP' ELSE 'ER Only' END AS AdmissionStatus	,
                reg.dischdate,
                reg.FK_emdPatients,
                                    CAST(reg.registrystatus as TEXT) As Status,
                                  --  a.bedno,
                                  --  CAST(g.description  as TEXT) as NurseStation,
                                    --b.registrydate,
                                    reg.registrydate,
                                    FK_psrooms as RoomNo,
			bedno,
			g.description as Station,
			reg.PK_psPatRegisters
            FROM psEmergencies AS em							
            LEFT JOIN psPatRegisters AS reg							
                on em.FK_psPatRegisters = reg.PK_psPatRegisters
        LEFT JOIN psAdmissions a on reg.PK_psPatRegisters = a.FK_psPatRegisters
        LEFT JOIN mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
               -- left join psAdmissions a on a.FK_emdPatients = reg.FK_emdPatients
               -- left join mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
                --left join psPatRegisters b on b.PK_psPatRegisters = a.FK_psPatRegisters
            WHERE reg.registrystatus <> 'X'							
                AND reg.registrydate BETWEEN '$fdate' and '$tdate'						
                AND reg.cancelflag = 0	order by reg.registrydate desc
        ");
        $data_array = array();
        foreach ($data as $key => $value) {
            $arr = array();
            $arr['patient'] = $value->PatientName;
            $arr['erdt'] = $value->erdatetime ? date_format(date_create($value->erdatetime), 'M d,Y g:i a') : '';
            $arr['admissiondt'] =  $value->AdmissionDate ? date_format(date_create($value->AdmissionDate), 'M d,Y g:i a') : '';
            $arr['admissionstatus'] = $value->AdmissionStatus;
            $arr['dischdt'] = $value->dischdate ? date_format(date_create($value->dischdate), 'M d,Y g:i a') : '';
            $arr['patientid'] = $value->FK_emdPatients;
            $arr['status'] = $value->Status;
            $arr['stn'] = $value->Station;
            $arr['registrydt'] = $value->registrydate ? date_format(date_create($value->registrydate), 'M d,Y g:i a') : '';;
            $arr['bedno'] = $value->bedno;
            $arr['roomno'] = $value->RoomNo;
            $arr['pspatregister'] = $value->PK_psPatRegisters;            
            $data_array[] = $arr;
        }
        $datasets["data"] = $data_array;
        return response()->json($datasets);
    }

    public function storeAcuityInfo(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $check = Acuity::where(['pspatregister'=>$request['pxdetail']['pspatregister']])->first();
        if(!$check){
            $admissiondt = new DateTime($request['pxdetail']['registrydt']);//new DateTime('Oct 09,2023 7:22 pm');
            $dischdt = new DateTime($request['pxdetail']['dischdt']);//new DateTime('Nov 14,2023 10:10 pm');
            $erdt = new DateTime($request['pxdetail']['erdt']);//new DateTime('Nov 14,2023 10:10 pm');
            //$diff = $erdt->diff($admissiondt);
            if($request['pxdetail']['admissionstatus']=='ER To IP'){
                $diff = $erdt->diff($admissiondt);
            }else{
                $diff = $erdt->diff($dischdt);
            }
    
            $hours = $diff->h;
            $hours = $hours + ($diff->days * 24);
    
            $c = new Acuity;
            $c->acuity = $request->acuity;
            $c->roomno = $request['pxdetail']['roomno'];
            $c->status = $request['pxdetail']['admissionstatus'];
            $c->patientid = $request['pxdetail']['patientid'];
            $c->pspatregister = $request['pxdetail']['pspatregister'];
            $c->name = $request['pxdetail']['patient'];
            $c->station = $request['pxdetail']['stn'];
            $c->registrydate = date_format(date_create($request['pxdetail']['registrydt']), 'Y-m-d H:i:s');
            $c->created_dt = date('Y-m-d H:i:s');
            $c->erdt = date_format(date_create($request['pxdetail']['erdt']), 'Y-m-d H:i:s');
            $c->dschdt = date_format(date_create($request['pxdetail']['dischdt']), 'Y-m-d H:i:s');
            $c->hrs = $hours;
            $c->created_by = Auth::user()->id;
            $c->save();
            return response()->json(true);
        }else{
            return response()->json(false);
        }        
    }

    
    public function getEeReport(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        /* $length = 10;
        $start = $request->start ? $request->start : 0; */
        $station = $request->stns;
        $stnArrToStr = '';
        if (sizeof($request->stns) == 1 && $request->stns[0] == 'All') {
            $station = DB::connection('pgsql')->select("select station from census group by station");
            foreach ($station as $key => $valueStn) {
                $stnArrToStr .= "'" . $valueStn->station . "',";
            }
        } else {
            foreach ($station as $key => $valueStn) {
                $stnArrToStr .= "'" . $valueStn . "',";
            }
        }
        //$stnArrToStr = str_replace("NURSERY","NICU",$stnArrToStr);
        /*  return response()->json($stnArrToStr); */
        $fdate = date_format(date_create($request->fdate), 'Y-m-d');
        $tdate = date_format(date_create($request->tdate), 'Y-m-d');
        $sql_q = substr($stnArrToStr, 0, -1);
        if($request->acquity!=0){
            $getAquity =$request->acquity;
        }else{
            $a = 1;
            $b = 2;
            $c = 3;
            $d = 4;
            $getAquity = $a.','.$b.','.$c.','.$d;
        }

        if ($request->adm_frm_date!=''&&$request->adm_to_date!='') {
            $data = DB::connection('pgsql')->select("SELECT * from patient_with_acuity where registrydate between '$request->adm_frm_date' and '$request->adm_to_date' and
            name ilike '%$request->patient%' and
            acuity in ($getAquity) and 
            station in ($sql_q)");
        }

        
        if ($request->dsh_frm_date!=''&&$request->dsh_to_date!='') {
            $data = DB::connection('pgsql')->select("SELECT * from patient_with_acuity where dschdt between '$request->dsh_frm_date' and '$request->dsh_to_date' and
            name ilike '%$request->patient%' and
            acuity in ($getAquity) and 
            station in ($sql_q)");
        }

        
        if ($request->er_frm_date!=''&&$request->er_to_date!='') {
            $data = DB::connection('pgsql')->select("SELECT * from patient_with_acuity where erdt between '$request->er_frm_date' and '$request->er_to_date' and
            name ilike '%$request->patient%' and
            acuity in ($getAquity) and 
            station in ($sql_q)");
        }

        $data_array = array();
       
        foreach ($data as $key => $value2) {
            $arr = array();
            $arr["pid"] = $value2->patientid;
            $arr["name"] = $value2->name;
            $arr["er"] = $value2->erdt            ;
            $arr["adm"] = $value2->registrydate;
            $arr["dsh"] = $value2->dschdt;
            $arr["hrs"] = $value2->hrs;
            $arr["status"] = $value2->status;
            $arr["rm"] = $value2->roomno;
            $arr["stn"] = $value2->station;
            $arr["acuity"] = $value2->acuity;
            $data_array[]=$arr;
        }
        
        $datasets["data"] = $data_array;
        $datasets["sql"] =  "SELECT * from patient_with_acuity where registrydate between '$request->adm_frm_date' and '$request->adm_to_date' and
        name ilike '%$request->patient%' and
        acuity in ($getAquity) and 
        station in ($sql_q)";
        return response()->json($datasets);
    }
}
