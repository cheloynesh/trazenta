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
use App\Payment_History;
use DateTime;
use DB;

class DelayComitionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        // dd($date2,$date1,$date);
        $users = DB::select('call delayComition(?,?)',[$date2->format('Y-m-d'), $date->format('Y-m-d')]);
        // dd($users);
        // dd($users);
        $perm = Permission::permView($profile,41);
        $perm_btn =Permission::permBtns($profile,41);
        // dd($perm_btn);
        $regimes = Regime::pluck('name','id');
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('comitions.delayComition.delayComition', compact('users','perm_btn','regimes'));
        }
    }

    public function GetInfo($id,$invoice,$contpp,$contpa,$lpnopay)
    {
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$id)->first();
        // dd($id);
        $rec = 0;
        $pp = 0;
        $pa = 0;
        $lp = 0;

        if(intval($invoice) != 0)
        {
            date_default_timezone_set('America/Mexico_City');
            $date = new DateTime();
            $date->setDate($date->format('Y'), $date->format('m'), 1);
            $rec = DB::select('call delayCurr(?,?)',[$date->format('Y-m-d'),$id]);
        }

        if(intval($contpp) != 0)
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
            $date2->modify('-1 months');
            $pp = DB::select('call delayFst(?,?)',[$date2->format('Y-m-d'),$id]);
        }

        if(intval($contpa != 0))
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
            $date2->modify('-1 months');
            $pa = DB::select('call delayInc(?)',[$id]);
        }

        if(intval($lpnopay != 0))
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
            $lp = DB::select('call delayLP(?,?)',[$date2->format('Y-m-d'),$id]);
        }
        return response()->json(['status'=>true, "regime"=>$regime, "rec"=>$rec, "pp"=>$pp, "pa"=>$pa, "lp"=>$lp]);
    }

    public function GetPDFAuth($id,$type,$authT)
    {
        // dd($id,$type,$authT);
        if(intval($authT) == 1)
        {
            $mov = Payment_History::where('id',$id)->first();
            if($type == 1)
            {
                $doc = $mov->invoice_doc;
                $route = "/comition_files/rec_invoice/";
            }
            else if($type == 2)
            {
                $doc = $mov->pay_doc;
                $route = "/comition_files/rec_pay/";
            }
        }
        else if($authT == 2)
        {
            $mov = Nuc::where('id',$id)->first();
            if($type == 1)
            {
                $doc = $mov->fst_invoice_doc;
                $route = "/comition_files/fst_invoice/";
            }
            else if($type == 2)
            {
                $doc = $mov->fst_pay_doc;
                $route = "/comition_files/fst_pay/";
            }
        }
        else if($authT == 3)
        {
            $mov = MonthFund::where('id',$id)->first();
            if($type == 1)
            {
                $doc = $mov->mov_invoice_doc;
                $route = "/comition_files/mov_invoice/";
            }
            else if($type == 2)
            {
                $doc = $mov->mov_pay_doc;
                $route = "/comition_files/mov_pay/";
            }
        }
        else if($authT == 4)
        {
            $mov = SixMonth_fund::where('id',$id)->first();
            if($type == 1)
            {
                $doc = $mov->lp_invoice_doc;
                $route = "/comition_files/lp_invoice/";
            }
            else if($type == 2)
            {
                $doc = $mov->lp_pay_doc;
                $route = "/comition_files/lp_pay/";
            }
        }
        return response()->json(['status'=>true, "doc"=>$doc, "route"=>$route, "mov"=>$mov]);
    }

    public function setStatDateRec(Request $request)
    {
        // dd($request->all());
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $history = Payment_History::where('fk_agent',$request->idUser)->where('curr_month',$request->month)->where('curr_year',$request->year)->first();
        if($history == null)
        {
            $history = new Payment_History;
            $history->fk_agent = $request->id;
            $history->curr_month = $request->month;
            $history->curr_year = $request->year;
            date_default_timezone_set('America/Mexico_City');
            $currdate = new DateTime();
            $currdate->setDate($request->year, $request->month, 1);
            $history->curr_date = $currdate;
        }
        if(intval($request->flagtype) == "1")
        {
            $history->invoice_date = $request->date;
            $history->invoice_doc = 'REC_'.$request->id."_".$request->month."_".$request->year.'.pdf';
        }
        else
        {
            $history->pay_date = $request->date;
            if($request->hasFile("pay_doc"))
            {
                $imagen = $request->file("pay_doc");
                $nombreimagen = 'REC_'.$request->id."_".$request->month."_".$request->year.'.pdf';
                $ruta = public_path("comition_files/rec_pay/");
                $history->pay_doc = $nombreimagen;

                copy($imagen->getRealPath(),$ruta.$nombreimagen);
            }
        }
        $history->save();

        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $rec = DB::select('call delayCurr(?,?)',[$date->format('Y-m-d'),$history->fk_agent]);
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$history->fk_agent)->first();

        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "rec" => $rec, "regime"=>$regime]);
    }

    public function setNullDateRec(Request $request)
    {
        $history = Payment_History::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $history->invoice_date = null;
            $history->invoice_doc = null;
        }
        else
        {
            $history->pay_date = null;
            $history->pay_doc = null;
        }
        $history->save();

        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $rec = DB::select('call delayCurr(?,?)',[$date->format('Y-m-d'),$history->fk_agent]);
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$history->fk_agent)->first();

        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "rec" => $rec, "regime"=>$regime]);
    }

    public function setStatDate(Request $request)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = Nuc::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->fst_invoice = $request->date;
            $status->fst_invoice_doc = 'FST_'.$request->id."_".$request->month."_".$request->year.'.pdf';
        }
        else
        {
            $status->fst_pay = $request->date;
            if($request->hasFile("pay_doc"))
            {
                $imagen = $request->file("pay_doc");
                $nombreimagen = 'FST_'.$request->id."_".$request->month."_".$request->year.'.pdf';
                $ruta = public_path("comition_files/fst_pay/");
                $status->fst_pay_doc = $nombreimagen;

                copy($imagen->getRealPath(),$ruta.$nombreimagen);
            }
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $date2->modify('-1 months');
        $pp = DB::select('call delayFst(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "pp" => $pp]);
    }

    public function setNullDate(Request $request)
    {
        $status = Nuc::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->fst_invoice = null;
            $status->fst_invoice_doc = null;
        }
        else
        {
            $status->fst_pay = null;
            $status->fst_pay_doc = null;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $date2->modify('-1 months');
        $pp = DB::select('call delayFst(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "pp" => $pp]);
    }

    public function setStatDateMoves(Request $request)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = MonthFund::where('id',$request->id)->first();
        $date2 = new DateTime($status->apply_date);
        $nuc = Nuc::where('id',$status->fk_nuc)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->mov_invoice = $request->date;
            $status->mov_invoice_doc = 'INC_'.$request->id."_".$request->month."_".$request->year.'.pdf';
        }
        else
        {
            $status->mov_pay = $request->date;
            if($request->hasFile("pay_doc"))
            {
                $imagen = $request->file("pay_doc");
                $nombreimagen = 'INC_'.$request->id."_".$request->month."_".$request->year.'.pdf';
                $ruta = public_path("comition_files/mov_pay/");
                $status->mov_pay_doc = $nombreimagen;

                copy($imagen->getRealPath(),$ruta.$nombreimagen);
            }
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $date2->modify('-1 months');
        $pa = DB::select('call delayInc(?)',[$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "pa" => $pa]);
    }

    public function setNullDateMoves(Request $request)
    {
        $status = MonthFund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->mov_invoice = null;
            $status->mov_invoice_doc = null;
        }
        else
        {
            $status->mov_pay = null;
            $status->mov_pay_doc = null;
        }
        $status->save();


        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "pa" => $pa]);
    }

    public function setStatDateLP(Request $request)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = SixMonth_fund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->lp_invoice = $request->date;
            $status->lp_invoice_doc = 'LP_'.$request->id."_".$request->month."_".$request->year.'.pdf';
        }
        else
        {
            $status->lp_pay = $request->date;
            $status->paid = 1;
            if($request->hasFile("pay_doc"))
            {
                $imagen = $request->file("pay_doc");
                $nombreimagen = 'LP_'.$request->id."_".$request->month."_".$request->year.'.pdf';
                $ruta = public_path("comition_files/lp_pay/");
                $status->lp_pay_doc = $nombreimagen;

                copy($imagen->getRealPath(),$ruta.$nombreimagen);
            }
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $lp = DB::select('call delayLP(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "lp" => $lp]);
    }

    public function setNullDateLP(Request $request)
    {
        $status = SixMonth_fund::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->lp_invoice = null;
            $status->lp_invoice_doc = null;
        }
        else
        {
            $status->lp_pay = null;
            $status->lp_pay_doc = null;
        }
        $status->save();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $lp = DB::select('call delayLP(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "lp" => $lp]);
    }

    public function setStatDateMultLP(Request $request)
    {
        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $lp = DB::select('call delayLP(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $docname = "";
        foreach($lp as $nuc)
        {
            $docname = $docname.$nuc->idNuc."-";
        }
        $docname = substr($docname, 0, -1);
        $docname = 'LP_'.$docname."_".$request->month."_".$request->year.'.pdf';

        foreach($lp as $nuc)
        {
            $status = SixMonth_fund::where('id',$nuc->idNuc)->first();
            $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$status->fk_agent)->first();
            $status->lp_invoice = $request->date;
            $status->lp_invoice_doc = $docname;
            $status->save();
        }

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        $lp = DB::select('call delayLP(?,?)',[$date2->format('Y-m-d'),$request->idUser]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "lp" => $lp]);
    }
}
