<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthlyComission;
use App\Nuc;
use App\Status;
use App\MonthFund;
use DateTime;
use DB;

class FstMonthComissionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $users = DB::table('users')->select('users.id',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")," ",IFNULL(users.lastname, "")) AS name'))
            ->join('Nuc',"fk_agent","=","users.id")
            ->join('Client',"fk_client","=","Client.id")
            ->where("month_flag","<","8")
            ->groupBy("name")
            ->whereNull('users.deleted_at')->get();
        $perm = Permission::permView($profile,22);
        $perm_btn =Permission::permBtns($profile,22);
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.fstmonthcomission.fstmonthcomission', compact('users','perm_btn'));
        }
    }
    public function GetInfo($id)
    {
        $clients = DB::table('Nuc')->select("Nuc.id as idNuc","nuc", DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS client_name'))
            ->join('Client',"Client.id","=","fk_client")
            ->where('Nuc.fk_agent',$id)
            ->where("month_flag","<","8")
            ->get();
        $regime = DB::table('users')->select('regime')->where('id',$id)->first();
        return response()->json(['status'=>true, "regime"=>$regime->regime, "data"=>$clients]);
    }
    public function ExportPDF($id,$month,$year,$TC,$regime){


        // dd($id,$month,$year,$TC);
        $b_amount = 0;
        $IVA = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        // setlocale(LC_TIME, 'es_ES.UTF-8');
        // $monthName = date('F', mktime(0, 0, 0, $month, 10));
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $clients = DB::table('Client')->select(DB::raw('CONCAT(Client.name," ",Client.firstname," ",Client.lastname) AS name'),"Nuc.fk_agent")
            ->join('Nuc',"fk_client","=","Client.id")
            ->where("month_flag","<","8")
            ->groupBy("name")
            ->where('Nuc.id',$id)->get();
        $clientNames = "";
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
            ->where('users.id',$clients[0]->fk_agent)->whereNull('users.deleted_at')->first();

        $value5 = $this->calculo($id,$month,$year,$TC,10,$regime);
        $value1 = $this->calculo($id,$month,$year,$TC,35,$regime);
        // dd($value5,$value1);
        $b_amount += $value5["gross_amount"]*5 + $value1["gross_amount"];
        $IVA += $value5["iva_amount"]*5 + $value1["iva_amount"];
        $ret_isr += $value5["ret_isr"]*5 + $value1["ret_isr"];
        $ret_iva += $value5["ret_iva"]*5 + $value1["ret_iva"];
        $n_amount += $value5["n_amount"]*5 + $value1["n_amount"];
            // dd($clientNames);
        // dd($b_amount,$IVA,$ret_isr,$ret_iva,$n_amount);

        foreach ($clients as $client)
        {
            $clientNames = $clientNames.$client->name."<br>";
            // dd($clientNames);
        }
        // dd($clientNames);
        if(intval($month) == 1)
        {
            $monthless = 12;
            $yearless = $year;
            $yearless -= 1;
        }
        else
        {
            $monthless = intval($month) - 1;
            $yearless = $year;
        }
        // dd($clientNames);
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
                                    <h2>'.$userName->name.'</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Clientes:</strong><br>
                                        '.$clientNames.'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Fecha de pago:</strong><br>
                                        '.$months[intval($month)]." ".$year.'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Corte a:</strong><br>
                                        '.$months[$monthless]." ".$yearless.'
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
        return $pdf->download($months[intval($month)]."_".$year."_".$userName->name.'.pdf');
    }

    public function ExportPDFAll($id,$TC)
    {
        // dd($id,$TC);
        $b_amount = 0;
        $IVA = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        // setlocale(LC_TIME, 'es_ES.UTF-8');
        // $monthName = date('F', mktime(0, 0, 0, $month, 10));
        $movement = MonthFund::where('id',$id)->first();
        $nuc = Nuc::where('id',$movement->fk_nuc)->first();
        $client = DB::table('Client')->select('*',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'))
            ->where('id',$nuc->fk_client)->first();
        $userName = DB::table('users')->select('*',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS usname'))
            ->where('users.id',$client->fk_agent)->whereNull('users.deleted_at')->first();
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $clientNames = $client->cname;

        // dd($userName);
        $apertura = MonthFund::where('fk_nuc',$movement->fk_nuc)->where('type','Apertura')->orderBy('apply_date','desc')->first();
        $date1 = new DateTime($apertura->apply_date);
        $date2 = new DateTime($movement->apply_date);
        $date1->modify('+6 month');
        $diff = $date1->diff($date2);

        $value = $this->calculoExtra($movement->amount,$TC,10,$userName->regime,$nuc);
        // dd($value5,$value1);
        $days = $diff->d/31;
        // dd($value["gross_amount"]*$diff->m, $value["gross_amount"]*$days);
        $b_amount += $value["gross_amount"]*$diff->m + $value["gross_amount"]*$days;
        $IVA += $value["iva_amount"]*$diff->m + $value["iva_amount"]*$days;
        $ret_isr += $value["ret_isr"]*$diff->m + $value["ret_isr"]*$days;
        $ret_iva += $value["ret_iva"]*$diff->m + $value["ret_iva"]*$days;
        $n_amount += $value["n_amount"]*$diff->m + $value["n_amount"]*$days;
        // foreach ($nucs as $nuc)
        // {
        //     $value = $this->calculo($nuc->id,$month,$year,$TC,10,$regime);
        //     // dd($value);
        //     $b_amount += $value["gross_amount"];
        //     $IVA += $value["iva_amount"];
        //     $ret_isr += $value["ret_isr"];
        //     $ret_iva += $value["ret_iva"];
        //     $n_amount += $value["n_amount"];
        //     // dd($clientNames);
        // }
        // if(intval($month) == 1)
        // {
        //     $monthless = 12;
        //     $yearless = $year;
        //     $yearless -= 1;
        // }
        // else
        // {
        //     $monthless = intval($month) - 1;
        //     $yearless = $year;
        // }
        // dd($clientNames);
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
                                    <h2>'.$userName->usname.'</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Clientes:</strong><br>
                                        '.$clientNames.'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Fecha de Incremento:</strong><br>
                                        '.$months[intval($date2->format('m'))]." ".$date2->format('Y').'
                                    </address>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <address>
                                        <strong>Correspondiente a:</strong><br>
                                        '.$diff->m." meses y ".$diff->d.' días restantes al adelanto de los 6 meses.'.'
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
        return $pdf->download($months[intval($date2->format('m'))]."_".$date2->format('Y')."_".$userName->name.'.pdf');
    }


    public function GetInfoComition(Request $request)
    {
        $values = $this->calculo($request->id,$request->month,$request->year,$request->TC,10,$request->regime);
        // dd($request->id);
        // dd(number_format($iva_amount,2,'.',''));
        return response()->json(['status'=>true, "b_amount"=>number_format($values["b_amount"],2,'.',','),'dll_conv'=>number_format($values["dll_conv"],2,'.',','),'usd_invest'=>number_format($values["usd_invest1"],2,'.',','),
        'gross_amount'=>number_format($values["gross_amount"],2,'.',','), 'iva_amount'=>number_format($values["iva_amount"],2,'.',','), 'ret_isr'=>number_format($values["ret_isr"],2,'.',','),
        'ret_iva'=>number_format($values["ret_iva"],2,'.',','), 'n_amount'=>number_format($values["n_amount"],2,'.',',')]);
    }

    public function calculo($id, $month, $year, $TC, $dllMult, $regime)
    {
        // dd($regime);
        // dd($request->all());
        $b_amount=0;//Saldo cierre de mes
        $dll_conv=0;//conversion a usd
        $usd_invest=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)
        $usd_invest1=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto

        // condicion para determinar fecha de corte
        $nuc = DB::table('Nuc')->select("cut_date","currency")->where('id',$id)->first();
        // dd($request->year."-".(intval($request->month)-1)."-15");
        // dd($cut_date->cut_date);
        $data = DB::table('Month_fund')->select('*')
            ->join('Nuc','Nuc.id','=','fk_nuc')
            ->where('fk_nuc',$id)
            ->where('type','Apertura')
            ->whereNull('Month_fund.deleted_at')
            ->orderByRaw('Month_fund.id DESC')->first();

        if($data == NULL)
        {
            $b_amount = 0;
        }
        else
        {
            $b_amount = $data->new_balance;
        }
        // dd($b_amount);

        // $data = DB::table('Month_fund')->select('*')
        // ->join('Nuc','Nuc.id','=','fk_nuc')
        // ->where('Month_fund.fk_nuc',$request->id)
        // ->orderByRaw('Month_fund.id DESC')->first();

        if($nuc->currency == "MXN")
        {
            $dll_conv = $b_amount / $TC; //si es en pesos, ponemos valor en usd

        }else{
            $dll_conv = $b_amount; //si es en dolares, se queda igual

        }
        $usd_invest = $dll_conv/5000; //por cada 5000 sobre el monto invertido
        $usd_invest1 = $usd_invest*$dllMult; //se multiplica por 10 el resultado obtenido
        // dd($usd_invest);

        $gross_amount = $usd_invest1 * $TC; //monto bruto

        $iva_amount = $gross_amount * .16; // iva del monto bruto

        if($regime == 1)
        $ret_isr = $gross_amount *.10; //isr del monto bruto
        else
        $ret_isr = $gross_amount *.0125;

        $ret_iva = 2*$iva_amount; //retencion de iva
        $ret_iva = $ret_iva/3; //retencion del iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$b_amount,'dll_conv'=>$dll_conv,'usd_invest1'=>$usd_invest1,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount);
        // dd($values);
        // dd($values);

        return($values);
    }

    public function update(Request $request)
    {
        $client = User::where('id',$request->id)->update(['regime'=>$request->regime]);
        return response()->json(['status'=>true, 'message'=>"Régimen Actualizado"]);
    }

    public function calculoExtra($amount, $TC, $dllMult, $regime, $nuc)
    {
        // dd($regime);
        $b_amount=$amount;//Saldo cierre de mes
        $dll_conv=0;//conversion a usd
        $usd_invest=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)
        $usd_invest1=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto


        if($nuc->currency == "MXN")
        {
            $dll_conv = $b_amount / $TC; //si es en pesos, ponemos valor en usd

        }else{
            $dll_conv = $b_amount; //si es en dolares, se queda igual

        }

        $usd_invest = $dll_conv/5000; //por cada 5000 sobre el monto invertido
        $usd_invest1 = $usd_invest*$dllMult; //se multiplica por 10 el resultado obtenido

        $gross_amount = $usd_invest1 * $TC; //monto bruto

        $iva_amount = $gross_amount * .16; // iva del monto bruto

        if($regime == 0)
            $ret_isr = $gross_amount *.10; //isr del monto bruto
        else
            $ret_isr = $gross_amount *.0125;

        $ret_iva = 2*$iva_amount; //retencion de iva
        $ret_iva = $ret_iva/3; //retencion del iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$b_amount,'dll_conv'=>$dll_conv,'usd_invest1'=>$usd_invest1,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount);
        // dd($values);

        return($values);
    }
    public function GetInfoAugments($id)
    {
        $movimientos = DB::table('Month_fund')->select("*","Month_fund.id as id",DB::raw('IFNULL(auth_date, "-") as auth'))->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$id)->where('type',"Abono")->whereNull('Month_fund.deleted_at')->get();
        return response()->json(['status'=>true, "data"=>$movimientos]);
    }
}

