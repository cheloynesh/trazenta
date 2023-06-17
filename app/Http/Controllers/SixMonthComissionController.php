<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthlyComission;
use App\Nuc;
use App\Status;
use App\SixMonth_fund;
use DB;

class SixMonthComissionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $users = DB::table('users')->select('SixMonth_fund.id as nucid',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")) AS usname'),
            DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")) AS clname'),'paid','nuc','pay_date')
            ->join('SixMonth_fund',"SixMonth_fund.fk_agent","=","users.id")
            ->join('Client',"SixMonth_fund.fk_client","=","Client.id")
            ->join('Coupon','fk_nuc','SixMonth_fund.id')
            ->where('number',1)
            ->where('paid',0)
            ->whereNull('SixMonth_fund.deleted_at')
            ->whereNull('Coupon.deleted_at')
            ->whereNull('users.deleted_at')->get();
        $perm = Permission::permView($profile,29);
        $perm_btn =Permission::permBtns($profile,29);
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.sixmonthcomission.sixmonthcomission', compact('users','perm_btn'));
        }
    }
    public function GetInfo($id)
    {
        $clients = DB::table('SixMonth_fund')->select("SixMonth_fund.id as idNuc","nuc", DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS client_name'))
        ->join('Client',"Client.id","=","fk_client")
        ->where('fk_agent',$id)
        ->whereNull('Client.deleted_at')
        ->get();
        $regime = DB::table('users')->select('regime')->where('id',$id)->first();
        return response()->json(['status'=>true, "regime"=>$regime->regime, "data"=>$clients]);
    }

    public function ReturnData($paid)
    {
        if($paid == 1) $paid = '%';
        $users = DB::table('users')->select('SixMonth_fund.id as nucid',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")) AS usname'),
            DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")) AS clname'),'paid','nuc','pay_date')
            ->join('SixMonth_fund',"SixMonth_fund.fk_agent","=","users.id")
            ->join('Client',"SixMonth_fund.fk_client","=","Client.id")
            ->join('Coupon','fk_nuc','SixMonth_fund.id')
            ->where('number',1)
            ->where('paid','like',$paid)
            ->whereNull('SixMonth_fund.deleted_at')
            ->whereNull('Coupon.deleted_at')
            ->whereNull('users.deleted_at')->get();
        return $users;
    }

    public function GetComitions($paid)
    {
        $users = $this->ReturnData($paid);

        return response()->json(['status'=>true, "users" => $users]);
    }
    // public function GetInfoMonth($id,$month,$year)
    // {
    //     $movimientos = DB::table('Month_fund')->select("*")->where('fk_nuc',$id)->whereMonth('apply_date',$month)->whereYear('apply_date',$year)->whereNull('deleted_at')->get();
    //     // dd($movimientos);
    //     return response()->json(['status'=>true, "data"=>$movimientos]);
    // }

    // public function GetInfoLast($id,$month,$year)
    // {
    //     $fecha = $year.'/'.$month.'/01';
    //     // dd($fecha);
    //     $movements = DB::table('Month_fund')->select('*')->where('fk_nuc',$id)->where('apply_date','<',$fecha)
    //     ->orderByRaw('id DESC')->first();
    //     // dd($movements);
    //     return response()->json(['status'=>true, "data"=>$movements]);
    // }

    public function ExportPDF($id,$month,$year,$comition,$regime){

        // dd($id,$month,$year,$TC);
        $b_amount = 0;
        $IVA = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        // setlocale(LC_TIME, 'es_ES.UTF-8');
        // $monthName = date('F', mktime(0, 0, 0, $month, 10));
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $nucs = DB::table('SixMonth_fund')->select("nuc",DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS clName'),
        DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")," ",IFNULL(users.lastname, "")) AS usrName'))
            ->join('Client',"Client.id","=","fk_client")
            ->join('users',"users.id","=","SixMonth_fund.fk_agent")
            ->where('SixMonth_fund.id',$id)
            ->first();

        $value = $this->calculo($id,$comition);
        $b_amount += $value["gross_amount"];
        $IVA += $value["iva_amount"];
        $ret_isr += $value["ret_isr"];
        $ret_iva += $value["ret_iva"];
        $n_amount += $value["n_amount"];
        // dd($clientNames);
        // dd($monthless);
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML('
        <div class="container">
            <div class="row">
                <!-- BEGIN INVOICE -->
                <div class="col-xs-12">
                    <div class="grid invoice">
                        <div class="grid-body">
                            <div class="invoice-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="../public/img/logo.png" alt="" height="180">
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2>'.$nucs->usrName.'</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Cliente:</strong><br>
                                        '.$nucs->clName.'
                                    </address>
                                </div>
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Obligación:</strong><br>
                                        '.$nucs->nuc.'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Fecha de pago:</strong><br>
                                        '.$months[intval($month)]." ".$year.'
                                    </address>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Totales</h3>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td><strong>Monto bruto</strong><br>
                                                <td class="text-center">$'.number_format($b_amount,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>IVA</strong><br>
                                                <td class="text-center">$'.number_format($IVA,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>RET ISR</strong><br>
                                                <td class="text-center">$'.number_format($ret_isr,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>RET IVA</strong><br>
                                                <td class="text-center">$'.number_format($ret_iva,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Monto Neto</strong><br>
                                                <td class="text-center">$'.number_format($n_amount,2,'.',',').'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END INVOICE -->
            </div>
        </div>
        <style>
            body{margin-top:20px;
            background:#eee;
            }

            .invoice {
                padding: 30px;
            }

            .invoice h2 {
                margin-top: 0px;
                line-height: 0.8em;
            }

            .invoice .small {
                font-weight: 300;
            }

            .invoice hr {
                margin-top: 10px;
                border-color: #ddd;
            }

            .invoice .table tr.line {
                border-bottom: 1px solid #ccc;
            }

            .invoice .table td {
                border: none;
            }

            .invoice .identity {
                margin-top: 10px;
                font-size: 1.1em;
                font-weight: 300;
            }

            .invoice .identity strong {
                font-weight: 600;
            }

            .invoice-title{
                text-align: center;
            }

            .grid {
                position: relative;
                width: 100%;
                background: #fff;
                color: #666666;
                border-radius: 2px;
                margin-bottom: 25px;
                box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
        ');
        return $pdf->download($months[intval($month)]."_".$year."_".$nucs->usrName.'_'.$nucs->nuc.'.pdf');
    }

    public function ExportPDFAll($id,$month,$year,$comition,$regime){

        // dd($id,$month,$year,$TC);
        $b_amount = 0;
        $IVA = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        $ids = explode("-",$id);
        $validNames = array();
        $obligs = "";
        $clientNames = "";
        // setlocale(LC_TIME, 'es_ES.UTF-8');
        // $monthName = date('F', mktime(0, 0, 0, $month, 10));
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $nucs = DB::table('SixMonth_fund')->select("nuc",DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS clName'),
        DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")," ",IFNULL(users.lastname, "")) AS usrName'))
            ->join('Client',"Client.id","=","fk_client")
            ->join('users',"users.id","=","SixMonth_fund.fk_agent")
            ->whereIn('SixMonth_fund.id',$ids)
            ->get();

        foreach ($nucs as $nuc)
        {
            $obligs = $obligs.$nuc->nuc."<br>";
            if(array_search($nuc->clName, $validNames) == false)
            {
                array_push($validNames,$nuc->clName);
                // $clientNames = $clientNames.$nuc->clName."<br>";
            }
        }

        $validNames = array_unique($validNames);
        foreach ($validNames as $name)
        {
            $clientNames = $clientNames.$name."<br>";
        }
        // dd($clientNames);

        foreach($ids as $id)
        {
            $value = $this->calculo($id,$comition);
            $b_amount += $value["gross_amount"];
            $IVA += $value["iva_amount"];
            $ret_isr += $value["ret_isr"];
            $ret_iva += $value["ret_iva"];
            $n_amount += $value["n_amount"];
        }
        // dd($clientNames);
        // dd($nucs[0]->usrName);
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML('
        <div class="container">
            <div class="row">
                <!-- BEGIN INVOICE -->
                <div class="col-xs-12">
                    <div class="grid invoice">
                        <div class="grid-body">
                            <div class="invoice-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="../public/img/logo.png" alt="" height="180">
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2>'.$nucs[0]->usrName.'</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Cliente:</strong><br>
                                        '.$clientNames.'
                                    </address>
                                </div>
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Obligación:</strong><br>
                                        '.$obligs.'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Fecha de pago:</strong><br>
                                        '.$months[intval($month)]." ".$year.'
                                    </address>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Totales</h3>
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td><strong>Monto bruto</strong><br>
                                                <td class="text-center">$'.number_format($b_amount,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>IVA</strong><br>
                                                <td class="text-center">$'.number_format($IVA,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>RET ISR</strong><br>
                                                <td class="text-center">$'.number_format($ret_isr,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>RET IVA</strong><br>
                                                <td class="text-center">$'.number_format($ret_iva,2,'.',',').'</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Monto Neto</strong><br>
                                                <td class="text-center">$'.number_format($n_amount,2,'.',',').'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END INVOICE -->
            </div>
        </div>
        <style>
            body{margin-top:20px;
            background:#eee;
            }

            .invoice {
                padding: 30px;
            }

            .invoice h2 {
                margin-top: 0px;
                line-height: 0.8em;
            }

            .invoice .small {
                font-weight: 300;
            }

            .invoice hr {
                margin-top: 10px;
                border-color: #ddd;
            }

            .invoice .table tr.line {
                border-bottom: 1px solid #ccc;
            }

            .invoice .table td {
                border: none;
            }

            .invoice .identity {
                margin-top: 10px;
                font-size: 1.1em;
                font-weight: 300;
            }

            .invoice .identity strong {
                font-weight: 600;
            }

            .invoice-title{
                text-align: center;
            }

            .grid {
                position: relative;
                width: 100%;
                background: #fff;
                color: #666666;
                border-radius: 2px;
                margin-bottom: 25px;
                box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
        ');
        return $pdf->download($months[intval($month)]."_".$year."_".$nucs[0]->usrName.'_LP.pdf');
    }

    public function GetInfoComition(Request $request)
    {
        // dd($request->all());
        $monthless = 0;
        if(intval($request->month) == 1)
        {
            $monthless = 12;
            $request->year -= 1;
        }
        else
            $monthless = intval($request->month) - 1;

        $values = $this->calculo($request->id,$request->comition);
        // dd(number_format($iva_amount,2,'.',''));
        return response()->json(['status'=>true, "b_amount"=>$values["b_amount"], 'gross_amount'=>$values["gross_amount"], 'iva_amount'=>$values["iva_amount"],
        'ret_isr'=>$values["ret_isr"], 'ret_iva'=>$values["ret_iva"], 'n_amount'=>$values["n_amount"],'paid'=>$values["pay"],'regime'=>$values["regime"],'idUser'=>$values["idUser"]]);
    }

    public function calculo($id, $comition)
    {
        $div_amount=0;//Saldo cierre de mes

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto

        // condicion para determinar fecha de corte
        $nuc = DB::table('SixMonth_fund')->select("amount","currency","paid","regime","fk_agent")->join('users','fk_agent','=','users.id')->where('SixMonth_fund.id',$id)->first();

        if($nuc->currency == "MXN")
        {
            $div_amount = $nuc->amount / 500000;
        }
        else
        {
            $div_amount = $nuc->amount / 25000;
        }

        $gross_amount = $comition * $div_amount;

        $iva_amount = $gross_amount * .16;

        // dd($regime);
        if($nuc->regime == 0)
            $ret_isr = $gross_amount *.10; //isr del monto bruto
        else
            $ret_isr = $gross_amount *.0125;

        $ret_iva = 2*$iva_amount; //retencion de iva
        $ret_iva = $ret_iva/3; //retencion del iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$nuc->amount,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount, 'pay'=>$nuc->paid,'regime'=>$nuc->regime,'idUser'=>$nuc->fk_agent);

        return($values);
    }

    public function update(Request $request)
    {
        $client = User::where('id',$request->id)->update(['regime'=>$request->regime]);
        return response()->json(['status'=>true, 'message'=>"Régimen Actualizado"]);
    }
    public function SetPayment(Request $request)
    {
        $nuc = SixMonth_fund::where('id',$request->id)->update(['paid'=>$request->pay]);

        $users = $this->ReturnData($request->active);

        return response()->json(['status'=>true, 'message'=>"Pago Actualizado", "users" => $users]);
    }
    public function GetInfoAgents($id, Request $request)
    {
        $agents = SixMonth_fund::select('fk_agent')->whereIn('id',$request->ids)->get();
        $arrayAgents = array();
        $arrayAgentsUnique = array();

        $balanceAll = 0;
        $b_amountAll = 0;
        $ivaAll = 0;
        $ret_isrAll = 0;
        $ret_ivaAll = 0;
        $n_amountAll = 0;
        $regime = 0;

        foreach($agents as $agent)
        {
            array_push($arrayAgents, $agent->fk_agent);
        }
        $arrayAgentsUnique = array_unique($arrayAgents);

        if(count($arrayAgentsUnique) == 1)
        {
            foreach($request->ids as $id)
            {
                $values = $this->calculo($id,$request->comition);

                $balanceAll += $values["b_amount"];
                $b_amountAll += $values["gross_amount"];
                $ivaAll += $values["iva_amount"];
                $ret_isrAll += $values["ret_isr"];
                $ret_ivaAll += $values["ret_iva"];
                $n_amountAll += $values["n_amount"];
            }
            $regime = $values["regime"];
        }
        // dd($arrayAgents);
        return response()->json(['status'=>true, 'agents'=>$arrayAgentsUnique, "b_amount"=>$balanceAll, 'gross_amount'=>$b_amountAll, 'iva_amount'=>$ivaAll,
        'ret_isr'=>$ret_isrAll, 'ret_iva'=>$ret_ivaAll, 'n_amount'=>$n_amountAll,'paid'=>$regime,'regime'=>$regime]);
    }
    public function SetPaymentAll(Request $request)
    {
        foreach($request->ids as $id)
        {
            $nuc = SixMonth_fund::where('id',$id)->update(['paid'=>$request->pay]);
        }

        $users = $this->ReturnData($request->active);

        return response()->json(['status'=>true, 'message'=>"Pago Actualizado", "users" => $users]);
    }
}
