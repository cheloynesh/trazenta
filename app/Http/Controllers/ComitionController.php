<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthlyComission;
use App\Nuc;
use App\Status;
use App\Regime;
use App\MonthFund;
use App\SixMonth_fund;
use DateTime;
use DB;

class ComitionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->modify('-1 months');
        // dd($date2->format('Y'),$date2->format('m'));
        $users = DB::select('call comition(?,?,?,?,?)',[$date->format('Y-m-d'),intval($date1->format('m')),intval($date1->format('Y')),intval($date2->format('m')),intval($date2->format('Y'))]);
        // dd($users);
        // dd($users);
        $perm = Permission::permView($profile,40);
        $perm_btn =Permission::permBtns($profile,40);
        // dd($perm_btn);
        $regimes = Regime::pluck('name','id');
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('comitions.comition.comition', compact('users','perm_btn','regimes'));
        }
    }

    public function UpdateRegime(Request $request)
    {
        $client = User::where('id',$request->id)->update(['fk_regime'=>$request->regime]);
        return response()->json(['status'=>true, 'message'=>"Régimen Actualizado"]);
    }

    public function GetInfo($id,$invoice,$contpp,$contpa,$lpnopay)
    {
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$id)->first();
        // dd($id);
        $rec = 0;
        $pp = 0;
        $pa = 0;
        $lp = 0;

        if($invoice != "NA")
        {
            date_default_timezone_set('America/Mexico_City');
            $date = new DateTime();
            $date->setDate($date->format('Y'), $date->format('m'), 1);
            $date->modify('-1 months');
            $rec = DB::select('call clientesCP(?,?)',[$date->format('Y-m-d'),$id]);
        }

        if(intval($contpp) != 0)
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->modify('-1 months');
            $pp = DB::select('call fstGeneralComitions(?,?,?)',[$id,intval($date2->format('m')),intval($date2->format('Y'))]);
        }

        if(intval($contpa != 0))
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->modify('-1 months');
            $pa = DB::select('call incGeneralComitions(?,?,?)',[$id,intval($date2->format('m')),intval($date2->format('Y'))]);
        }

        if(intval($lpnopay != 0))
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $lp = DB::select('call lpGeneralComitions(?,?,?)',[$id,intval($date2->format('m')),intval($date2->format('Y'))]);
        }
        return response()->json(['status'=>true, "regime"=>$regime, "rec"=>$rec, "pp"=>$pp, "pa"=>$pa, "lp"=>$lp]);
    }

    public function setStatDate(Request $request)
    {
        $status = Nuc::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->fst_invoice = $request->date;
        }
        else
        {
            $status->fst_pay = $request->date;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->modify('-1 months');
        $pp = DB::select('call fstGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "pp" => $pp]);
    }

    public function setNullDate(Request $request)
    {
        $status = Nuc::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->fst_invoice = null;
        }
        else
        {
            $status->fst_pay = null;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->modify('-1 months');
        $pp = DB::select('call fstGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "pp" => $pp]);
    }

    public function setStatDateMoves(Request $request)
    {
        $status = MonthFund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->mov_invoice = $request->date;
        }
        else
        {
            $status->mov_pay = $request->date;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->modify('-1 months');
        $pa = DB::select('call incGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "pa" => $pa]);
    }

    public function setNullDateMoves(Request $request)
    {
        $status = MonthFund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->mov_invoice = null;
        }
        else
        {
            $status->mov_pay = null;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->modify('-1 months');
        $pa = DB::select('call incGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "pa" => $pa]);
    }

    public function setStatDateLP(Request $request)
    {
        $status = SixMonth_fund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->lp_invoice = $request->date;
        }
        else
        {
            $status->lp_pay = $request->date;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "lp" => $lp]);
    }

    public function setNullDateLP(Request $request)
    {
        $status = SixMonth_fund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->lp_invoice = null;
        }
        else
        {
            $status->lp_pay = null;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "lp" => $lp]);
    }

    public function setStatDateMultLP(Request $request)
    {
        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);

        foreach($lp as $nuc)
        {
            $status = SixMonth_fund::where('id',$nuc->idNuc)->first();
            $status->lp_invoice = $request->date;
            $status->save();
        }

        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "lp" => $lp]);
    }
}
