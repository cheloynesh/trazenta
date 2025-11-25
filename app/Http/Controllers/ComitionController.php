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
use App\Mail\AgentMail;
use App\Mail\AgentMailPay;
use Illuminate\Support\Facades\Mail;
use DateTime;
use DB;

class ComitionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $curr_date = new DateTime();;
        $curr_date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->modify('-1 months');
        // dd($date2,$date1,$date);
        $users = DB::select('call comition(?,?,?,?,?,?)',[$date->format('Y-m-d'),intval($date->format('m')),intval($date->format('Y')),intval($date2->format('m')),intval($date2->format('Y')),$curr_date->format('Y-m-d')]);
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
        $regime = DB::table('users')->select('fk_regime','dlls','fst_month','five_month')->where('id',$id)->first();
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
            $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
            $date2->modify('-1 months');
            $pp = DB::select('call fstGeneralComitions(?,?,?)',[$id,intval($date2->format('m')),intval($date2->format('Y'))]);
        }

        if(intval($contpa != 0))
        {
            date_default_timezone_set('America/Mexico_City');
            $date2 = new DateTime();
            $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
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

    public function GetInfoMailing($type)
    {
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $users = $type == 1 ? DB::select('call previewRecp(?)',[$date->format('Y-m-d')]) : DB::select('call previewPay(?)',[$date->format('Y-m-d')]);
        // dd($users);
        return response()->json(['status'=>true, "data"=>$users]);
    }

    public function GetInfoDocs($id,$fund,$type)
    {
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $aux = array();
        switch($fund)
        {
            case 1:
                $data = $type == 1 ? DB::select('call previewRecRecp(?,?)',[$id,$date->format('Y-m-d')]) : DB::select('call previewRecPay(?,?)',[$id,$date->format('Y-m-d')]);
                foreach($data as $dat)
                {
                    array_push($aux, $type == 1 ? "/comition_files/rec_invoice/".$dat->invoice_doc : "/comition_files/rec_pay/".$dat->pay_doc);
                }
                break;
            case 2:
                $data = $type == 1 ? DB::select('call previewFstRecp(?,?)',[$id,$date->format('Y-m-d')]) : DB::select('call previewFstPay(?,?)',[$id,$date->format('Y-m-d')]);
                foreach($data as $dat)
                {
                    array_push($aux, $type == 1 ? "/comition_files/fst_invoice/".$dat->fst_invoice_doc : "/comition_files/fst_pay/".$dat->fst_pay_doc);
                }
                break;
            case 3:
                $data = $type == 1 ? DB::select('call previewAdRecp(?,?)',[$id,$date->format('Y-m-d')]) : DB::select('call previewAdPay(?,?)',[$id,$date->format('Y-m-d')]);
                foreach($data as $dat)
                {
                    array_push($aux, $type == 1 ? "/comition_files/mov_invoice/".$dat->mov_invoice_doc : "/comition_files/mov_pay/".$dat->mov_pay_doc);
                }
                break;
            case 4:
                $data = $type == 1 ? DB::select('call previewLpRecp(?,?)',[$id,$date->format('Y-m-d')]) : DB::select('call previewLpPay(?,?)',[$id,$date->format('Y-m-d')]);
                foreach($data as $dat)
                {
                    array_push($aux, $type == 1 ? "/comition_files/lp_invoice/".$dat->lp_invoice_doc : "/comition_files/lp_pay/".$dat->lp_pay_doc);
                }
                break;
        }
        // dd($aux);
        return response()->json(['status'=>true, "data"=>$aux]);
    }

    public function setStatDate(Request $request)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = Nuc::where('id',$request->id)->first();
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$status->fk_agent)->first();
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
        $date2->modify('-1 months');
        $pp = DB::select('call fstGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "pp" => $pp]);
    }

    public function setStatDateMoves(Request $request)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = MonthFund::where('id',$request->id)->first();
        $date2 = new DateTime($status->apply_date);
        $nuc = Nuc::where('id',$status->fk_nuc)->first();
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$nuc->fk_agent)->first();
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
            $status->mov_invoice_doc = null;
        }
        else
        {
            $status->mov_pay = null;
            $status->mov_pay_doc = null;
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
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = SixMonth_fund::where('id',$request->id)->first();
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$status->fk_agent)->first();
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
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
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
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "lp" => $lp]);
    }

    public function setStatDateMultLP(Request $request)
    {
        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
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

        $lp = DB::select('call lpGeneralComitions(?,?,?)',[$request->idUser,intval($date2->format('m')),intval($date2->format('Y'))]);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "lp" => $lp]);
    }

    public function sendMailing(Request $request)
    {
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);

        if($request->type == 1)
        {
            // dd($request->ids);
            if($request->ids == null)
            {
                return response()->json(['status'=>true, "message"=>"Ningun usuario seleccionado"]);
            }
            else
            {
                foreach($request->ids as $id)
                {
                    $usr = User::where("id",$id)->first();
                    Mail::to($usr->email)->bcc('comisiones@trazenta.in')->send(new AgentMail($id,$date->format('Y-m-d')));
                }
            }
        }
        else
        {
            if($request->ids == null)
            {
                return response()->json(['status'=>true, "message"=>"Ningun usuario seleccionado"]);
            }
            else
            {
                foreach($request->ids as $id)
                {
                    $usr = User::where("id",$id)->first();
                    Mail::to($usr->email)->bcc('comisiones@trazenta.in')->send(new AgentMailPay($usr->uid,$date->format('Y-m-d')));
                }
            }
        }
        return response()->json(['status'=>true, "message"=>"Correos enviados"]);
    }

    public function updateFstPays(Request $request)
    {
        // dd("entre");
        $user = User::where('id',$request->idUser)->update(['fst_month'=>$request->fst_month,'five_month'=>$request->five_month]);

        return response()->json(['status'=>true, 'message'=>"Usuario Actualizado"]);
    }
}
