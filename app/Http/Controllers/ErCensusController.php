<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\Census;
use App\Model\BedCapacity;

class ErCensusController extends Controller
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
                    if ($value2->station == "NURSERY"||$value2->station == "PEDIA WARD") {

                        $arr2['bassinets'] = 0;
                    }else{
                        
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
                         if($value2->station=="STATION 11"){
                            //$bedCapacity = $value->totalstn - $count_nb_with_c;
                            $nb = $value->totalstn - $count_nb_with_c;
                            $occupany_rate =  ($value->totalstn / $bedCapacity) * 100;
                            $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '').'%';
                            $arr2['occupanyRate'] = $perOccupanyRate;
                            $getTotalOccupancyRate+=(float)$perOccupanyRate;
                            $arr2['getformula'] =  $value->totalstn .'- '.$count_nb_with_c.' ='.$bedCapacity.' '.$value->totalstn.' /'. $bedCapacity;
                            $arr2['occupiedBeds_2'] = $nb;//$bedCapacity;
                            $getBedCap = $bedCapacity ;
                           $totalRegularBed+=$bedCapacity;
                        } else{
                            //$bedCapacity = $bedCapacity - $count_nb_with_c;
                            $bedCapacity = $value->totalstn - $count_nb_with_c;
                            $occupany_rate =  ($value->totalstn / $bedCapacity) * 100;
                            $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '').'%';
                            $arr2['occupanyRate'] = $perOccupanyRate;
                            $getTotalOccupancyRate+=(float)$perOccupanyRate;
                            $arr2['getformula'] =  $value->totalstn .'- '.$count_nb_with_c.' ='.$bedCapacity.' '.$value->totalstn.' /'. $bedCapacity;
                            $arr2['occupiedBeds_2'] = $bedCapacity;
                            $getBedCap = $bedCapacity ;
                            $totalRegularBed+=$bedCapacity;
                        }
                    } else {
                        $occupany_rate =  ($value->totalstn / $getCapacity->capacity) * 100;
                        $perOccupanyRate = number_format((float)$occupany_rate, 2, '.', '') . '%';
                        $arr2['occupanyRate'] = $perOccupanyRate;
                        $getTotalOccupancyRate += (float)$perOccupanyRate;

                        $arr2['occupiedBeds_2'] = $value->totalstn;
                        $getBedCap = $bedCapacity ;
                        $totalRegularBed+=$value->totalstn;
                    }

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

    public function queryAcquity(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        $length = 10;
        $start = $request->start?$request->start:0;
        $val = $request->searchTerm2;
        $dd=1;
        if($val!=''||$start>0){   
            

            $data =  DB::connection('bizbox')->select("SELECT	dbo.udf_GetFullName(reg.FK_emdPatients) AS PatientName,						
            erdatetime,					
            (SELECT MIN(occupystartdate) FROM psAdmRooms AS adRm WHERE FK_psPatRegisters = reg.PK_psPatRegisters) AS AdmissionDate, 					
            CASE WHEN ISNULL((SELECT COUNT(1) FROM psAdmissions AS adm
            where adm.FK_psPatRegisters = reg.PK_psPatRegisters),0) > 0 THEN 'ER To IP' ELSE 'ER Only' END AS AdmissionStatus	,
        reg.dischdate,
        reg.FK_emdPatients,
                            CAST(reg.registrystatus as TEXT) As Status,
                            a.bedno,
                            CAST(g.description  as TEXT) as NurseStation,
						reg.registrydate
    FROM psEmergencies AS em							
    LEFT JOIN psPatRegisters AS reg							
        on em.FK_psPatRegisters = reg.PK_psPatRegisters
        left join psAdmissions a on a.FK_emdPatients = reg.FK_emdPatients
        left join mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
    WHERE reg.registrystatus <> 'X'							
        AND reg.registrydate BETWEEN '$request->frdt' and '$request->todt'						
        AND reg.cancelflag = 0 order by reg.registrydate  OFFSET $start FETCH NEXT $length ROWS ONLY");
            
            
            $count =  DB::connection('pgsql')->select("SELECT	dbo.udf_GetFullName(reg.FK_emdPatients) AS PatientName,						
            erdatetime,					
            (SELECT MIN(occupystartdate) FROM psAdmRooms AS adRm WHERE FK_psPatRegisters = reg.PK_psPatRegisters) AS AdmissionDate, 					
            CASE WHEN ISNULL((SELECT COUNT(1) FROM psAdmissions AS adm
            where adm.FK_psPatRegisters = reg.PK_psPatRegisters),0) > 0 THEN 'ER To IP' ELSE 'ER Only' END AS AdmissionStatus	,
        reg.dischdate,
        reg.FK_emdPatients,
                            CAST(reg.registrystatus as TEXT) As Status,
                            a.bedno,
                            CAST(g.description  as TEXT) as NurseStation,
						reg.registrydate
    FROM psEmergencies AS em							
    LEFT JOIN psPatRegisters AS reg							
        on em.FK_psPatRegisters = reg.PK_psPatRegisters
        left join psAdmissions a on a.FK_emdPatients = reg.FK_emdPatients
        left join mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
    WHERE reg.registrystatus <> 'X'							
        AND reg.registrydate BETWEEN '$request->frdt' and '$request->todt'						
        AND reg.cancelflag = 0	 order by reg.registrydate");
        }else{
            
            $data =  DB::connection('pgsql')->select("SELECT	dbo.udf_GetFullName(reg.FK_emdPatients) AS PatientName,						
            erdatetime,					
            (SELECT MIN(occupystartdate) FROM psAdmRooms AS adRm WHERE FK_psPatRegisters = reg.PK_psPatRegisters) AS AdmissionDate, 					
            CASE WHEN ISNULL((SELECT COUNT(1) FROM psAdmissions AS adm
            where adm.FK_psPatRegisters = reg.PK_psPatRegisters),0) > 0 THEN 'ER To IP' ELSE 'ER Only' END AS AdmissionStatus	,
        reg.dischdate,
        reg.FK_emdPatients,
                            CAST(reg.registrystatus as TEXT) As Status,
                            a.bedno,
                            CAST(g.description  as TEXT) as NurseStation,
						reg.registrydate
    FROM psEmergencies AS em							
    LEFT JOIN psPatRegisters AS reg							
        on em.FK_psPatRegisters = reg.PK_psPatRegisters
        left join psAdmissions a on a.FK_emdPatients = reg.FK_emdPatients
        left join mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
    WHERE reg.registrystatus <> 'X'							
        AND reg.registrydate BETWEEN '$request->frdt' and '$request->todt'						
        AND reg.cancelflag = 0	order by reg.registrydate FETCH NEXT $length ROWS ONLY");

            $count =  DB::connection('pgsql')->select("SELECT	dbo.udf_GetFullName(reg.FK_emdPatients) AS PatientName,						
            erdatetime,					
            (SELECT MIN(occupystartdate) FROM psAdmRooms AS adRm WHERE FK_psPatRegisters = reg.PK_psPatRegisters) AS AdmissionDate, 					
            CASE WHEN ISNULL((SELECT COUNT(1) FROM psAdmissions AS adm
            where adm.FK_psPatRegisters = reg.PK_psPatRegisters),0) > 0 THEN 'ER To IP' ELSE 'ER Only' END AS AdmissionStatus	,
        reg.dischdate,
        reg.FK_emdPatients,
                            CAST(reg.registrystatus as TEXT) As Status,
                            a.bedno,
                            CAST(g.description  as TEXT) as NurseStation,
                        reg.registrydate
    FROM psEmergencies AS em							
    LEFT JOIN psPatRegisters AS reg							
        on em.FK_psPatRegisters = reg.PK_psPatRegisters
        left join psAdmissions a on a.FK_emdPatients = reg.FK_emdPatients
        left join mscNrstation g on g.PK_mscNrstation = a.FK_mscNrstation
    WHERE reg.registrystatus <> 'X'							
        AND reg.registrydate BETWEEN '$request->frdt' and '$request->todt'						
        AND reg.cancelflag = 0	");
        }
        
        $count_all_record =  DB::connection('pgsql')->select("select count(*) as count from patients_1 where cast(registrydate as date) >= '".date("Y-m-d")."'");

        $data_array = array();

        foreach ($data as $key => $value) {
            $arr = array();
            $checkDetails = Diagnosis::where(['ps_patregisgter'=>$value->pk_pspatregisters])->first();

            
            $check_medicines = Prescriptions_m::where(['pspat'=>$value->pk_pspatregisters])->get();
            $arr['patientname'] =  $value->patientname;
            /* if($value->attending_phy){
                $physicians = DB::connection('bizbox_uk')->select("select dbo.udf_ConcatAllPatientsDoctor($value->pk_pspatregisters) as d"); 
                $arr['attending_phy'] =  $physicians[0]->d;
            }else{
                $arr['attending_phy'] =  "";
            } */
            $arr['attending_phy'] =  "";
            $arr['chiefcomplaint'] =  $value->chiefcomplaint;
            $arr['pk_pspatregisters'] =  $value->pk_pspatregisters;
            $arr['patientid'] =  $value->patientid;
            $arr['sex'] =  $value->sex;
            $arr['hasdetails'] =  $checkDetails?true:false;
            $arr['cnt_meds'] =  sizeof($check_medicines);
            $arr['dctr'] =  sizeof($check_medicines)>0?$check_medicines[0]->created_by:0;
            

            $data_array[] = $arr;
        }
        $page = sizeof($count)/$length;
        $getDecimal =  explode(".",$page);
        $page_count = round(sizeof($count)/$length);
        if(sizeof($getDecimal)==2){            
            if($getDecimal[1]<5){
                $page_count = $getDecimal[0] + 1;
            }
        }
        $datasets = array(["data"=>$data_array,"count"=>$page_count,"showing"=>"Showing ".(($start+10)-9)." to ".($start+10>$count_all_record[0]->count?$count_all_record[0]->count:$start+10)." of ".$count_all_record[0]->count, "patient"=>$data_array]);
        return response()->json($datasets);
    }
}
