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
use App\Payment_History;
use DateTime;
use DB;

class MonthComissionController extends Controller
{
    public function index(){
        $profile = User::findProfile();

        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $regimes = Regime::pluck('name','id');
        $users = DB::select('call agentesCP(?)',[$date->format('Y-m-d')]);

        $perm = Permission::permView($profile,21);
        $perm_btn =Permission::permBtns($profile,21);

        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.monthcomission.monthcomission', compact('users','perm_btn','regimes'));
        }
    }

    public function ReturnData()
    {
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $users = DB::select('call agentesCP(?)',[$date->format('Y-m-d')]);

        return $users;
    }

    public function GetInfo($id)
    {
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $clients = DB::select('call clientesCP(?,?)',[$date->format('Y-m-d'),$id]);
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$id)->first();
        return response()->json(['status'=>true, "regime"=>$regime, "data"=>$clients]);
    }

    public function ExportPDF($id,$month,$year,$TC,$regime,$dlls)
    {
        $b_amount = 0;
        $IVA = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        // setlocale(LC_TIME, 'es_ES.UTF-8');
        // $monthName = date('F', mktime(0, 0, 0, $month, 10));
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
            ->where('users.id',$id)->whereNull('users.deleted_at')->first();
        $nucs = DB::table('Nuc')->select("Nuc.id as id")
            ->where("month_flag","=","8")
            ->where('fk_agent',$id)
            ->get();
        $clients = DB::table('Client')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS clName'),'Nuc.id as nucid')
            ->join('Nuc',"fk_client","=","Client.id")
            ->where("month_flag","=","8")
            ->where('Nuc.fk_agent',$id)->get();
        $clientNames = "";

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

        $validNucs = array();
        $validNames = array();
        // $mounts = array();
        array_push($validNucs,0);
        $cnnames = "";
        $reg = Regime::where('id',$regime)->first();
        // dd($reg);
        foreach ($nucs as $nuc)
        {

            $value = $this->calculo($nuc->id,$monthless,$yearless,$TC,$reg,$dlls);
            // dd($value);
            // array_push($mounts,$value["gross_amount"]);
            $b_amount += $value["gross_amount"];
            $IVA += $value["iva_amount"];
            $ret_isr += $value["ret_isr"];
            $ret_iva += $value["ret_iva"];
            $n_amount += $value["n_amount"];
            if($value["gross_amount"] != 0) array_push($validNucs,$nuc->id);
            // dd($clientNames);
        }
        // dd($mounts);
        // dd($b_amount,$IVA,$ret_isr,$ret_iva,$n_amount);
        // dd(array_search(24, $validNucs));
        // dd($validNucs);
        foreach ($clients as $client)
        {
            if(array_search($client->nucid, $validNucs) != false)
            {
                array_push($validNames,$client->clName);
            }
        }
        $validNames = array_unique($validNames);
        foreach ($validNames as $name)
        {
            $clientNames = $clientNames.$name."<br>";
        }
        // dd($cnnames);
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
        $pdf->save(public_path("comition_files/rec_invoice/").'REC_'.$id."_".$month."_".$year.'.pdf');
        return $pdf->download($months[intval($month)]."_".$year."_".$userName->name.'.pdf');
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

        $regime = Regime::where('id',$request->regime)->first();

        $values = $this->calculo($request->id,$monthless,$request->year,$request->TC,$regime,$request->dlls);
        // dd(number_format($iva_amount,2,'.',''));
        return response()->json(['status'=>true, "b_amount"=>number_format($values["b_amount"],2,'.',','),'dll_conv'=>number_format($values["dll_conv"],2,'.',','),'usd_invest'=>number_format($values["usd_invest1"],2,'.',','),
        'gross_amount'=>number_format($values["gross_amount"],2,'.',','), 'iva_amount'=>number_format($values["iva_amount"],2,'.',','), 'ret_isr'=>number_format($values["ret_isr"],2,'.',','),
        'ret_iva'=>number_format($values["ret_iva"],2,'.',','), 'n_amount'=>number_format($values["n_amount"],2,'.',',')]);
    }

    public function calculo($id, $month, $year, $TC, $regime, $dlls)
    {
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

        if(intval($nuc->cut_date) > 15)
        {
            // dd("entre a mayor que 15");
            // consultas para corte al día 30
            $data = DB::table('Month_fund')->select("*")
            ->join('Nuc','Nuc.id','=','Month_fund.fk_nuc')
            ->where('fk_nuc',$id)->whereMonth('apply_date',$month)
            ->whereYear('apply_date',$year)->whereNull('Month_fund.deleted_at')->get();
            if($data->isEmpty())
            {
                $fecha = $year.'/'.$month.'/01';
                $data = DB::table('Month_fund')->select('*')
                ->join('Nuc','Nuc.id','=','fk_nuc')
                ->where('fk_nuc',$id)->where('apply_date','<',$fecha)
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
            }
            else
            {
                // cálculo para mes con movimientos corte día 30
                $balance = 0;
                // dd($data);
                if(count($data) == 1)
                {
                    $data = $data[0];
                    $day = explode("-", $data->apply_date);
                    $day = intval($day[2]);
                    if($day == 1)
                    {
                        $b_amount = $data->new_balance;
                    }
                    else if($day == 30 || $day == 31)
                    {
                        $b_amount = (29*$data->prev_balance + $data->new_balance)/30;
                    }
                    else
                    {
                        $b_amount = ($day*$data->prev_balance + (30-$day)*$data->new_balance)/30;
                    }
                    // dd($b_amount);
                }
                else
                {
                    $movs = array();
                    foreach ($data as $movimiento)
                    {
                        $day = explode("-", $movimiento->apply_date);
                        $day = intval($day[2]);
                        $mov = array($day, $movimiento->prev_balance, $movimiento->new_balance);
                        array_push($movs,$mov);
                    }
                    // dd($movs);
                    for($x = 0; $x <= count($movs); $x++)
                    {
                        if($x == 0)
                        {
                            $b_amount = $movs[$x][0]*$movs[$x][1];
                        }
                        else if($x == count($movs))
                        {
                            $b_amount += (30-$movs[$x-1][0])*$movs[$x-1][2];
                        }
                        else
                        {
                            $b_amount += ($movs[$x][0]-$movs[$x-1][0])*$movs[$x][1];
                        }
                    }
                    $b_amount /= 30;
                    // dd($b_amount);
                }
            }
        }
        else
        {
            // dd("entre a menor que 15");
            $data = DB::table('Month_fund')->select("*")
            ->join('Nuc','Nuc.id','=','Month_fund.fk_nuc')
            ->where('fk_nuc',$id)
            ->whereBetween('apply_date', [$year."-".(intval($month)-1)."-15", $year."-".(intval($month))."-15"])
            ->whereNull('Month_fund.deleted_at')->get();
            if($data->isEmpty())
            {
                $fecha = $year.'/'.(intval($month)-1).'/15';
                $data = DB::table('Month_fund')->select('*')
                ->join('Nuc','Nuc.id','=','fk_nuc')
                ->where('fk_nuc',$id)->where('apply_date','<',$fecha)
                ->whereNull('Month_fund.deleted_at')
                ->orderByRaw('Month_fund.id DESC')->first();
                if($data == null)
                {
                    $b_amount = 0;
                }
                else
                {
                    $b_amount = $data->new_balance;
                }
            }
            else
            {
                // cálculo para mes con movimientos corte día 15
                $balance = 0;
                // dd(count($data));
                if(count($data) == 1)
                {
                    $data = $data[0];
                    $day = explode("-", $data->apply_date);
                    $month = intval($day[1]);
                    $day = intval($day[2]);
                    if($day == 15)
                    {
                        if($month == ($month - 1))
                        {
                            $b_amount = $data->new_balance;
                        }
                        else if($month == $month)
                        {
                            $b_amount = (29*$data->prev_balance + $data->new_balance)/30;
                        }
                    }
                    else if($day > 15)
                    {
                        $b_amount = (($day-15)*$data->prev_balance + (45-$day)*$data->new_balance)/30;

                    }
                    else if($day < 15)
                    {
                        $b_amount = ((15+$day)*$data->prev_balance + (15-$day)*$data->new_balance)/30;
                    }
                    // dd($b_amount);
                }
                else
                {
                    $movs = array();
                    foreach ($data as $movimiento)// los días menores a 15 se les debe sumar 30
                    {
                        $day = explode("-", $movimiento->apply_date);
                        $month = intval($day[1]);
                        $day = intval($day[2]);
                        if($day > 15)
                        {
                            $mov = array($day-14, $movimiento->prev_balance, $movimiento->new_balance);
                        }
                        else
                        {
                            $mov = array($day+16, $movimiento->prev_balance, $movimiento->new_balance);
                        }
                        array_push($movs,$mov);
                    }
                    // dd($movs);
                    for($x = 0; $x <= count($movs); $x++)
                    {
                        if($x == 0)// calcular el numero de dias restandole 15
                        {
                            $b_amount = $movs[$x][0]*$movs[$x][1];
                        }
                        else if($x == count($movs))// calcular a 45 en lugar de 30
                        {
                            $b_amount += (30-$movs[$x-1][0])*$movs[$x-1][2];
                        }
                        else
                        {
                            $b_amount += ($movs[$x][0]-$movs[$x-1][0])*$movs[$x][1];
                        }
                    }
                    $b_amount /= 30;
                    // dd($b_amount);
                }
            }
        }


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
        $usd_invest1 = $usd_invest*$dlls; //se multiplica por 10 el resultado obtenido

        $gross_amount = $usd_invest1 * $TC; //monto bruto

        $iva_amount = $gross_amount*$regime->iva/100; // iva del monto bruto

        // dd($regime);
        $ret_isr = $gross_amount*$regime->ret_isr/100;

        $ret_iva = $iva_amount*$regime->ret_iva/100; //retencion de iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$b_amount,'dll_conv'=>$dll_conv,'usd_invest1'=>$usd_invest1,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount);

        return($values);
    }

    public function update(Request $request)
    {
        $client = User::where('id',$request->id)->update(['fk_regime'=>$request->regime]);
        return response()->json(['status'=>true, 'message'=>"Régimen Actualizado"]);
    }

    public function updateDlls(Request $request)
    {
        $client = User::where('id',$request->id)->update(['dlls'=>$request->dlls]);
        return response()->json(['status'=>true, 'message'=>"Comisión Actualizada"]);
    }

    public function setStatDate(Request $request)
    {
        // dd($request->all());
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        $status = User::where('id',$request->id)->first();
        $userName = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where('id',$request->id)->first();
        $history = Payment_History::where('fk_agent',$request->id)->where('curr_month',$request->month)->where('curr_year',$request->year)->first();
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
            $status->invoice_flag = $request->date;
            $history->invoice_date = $request->date;
            $history->invoice_doc = 'REC_'.$request->id."_".$request->month."_".$request->year.'.pdf';
        }
        else
        {
            $status->pay_flag = $request->date;
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
        $status->save();

        $users = $this->ReturnData();

        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);

        $date->modify('-1 months');
        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->modify('-1 months');
        $coms = DB::select('call comition(?,?,?,?,?)',[$date->format('Y-m-d'),intval($date1->format('m')),intval($date1->format('Y')),intval($date2->format('m')),intval($date2->format('Y'))]);
        // dd($coms);
        return response()->json(['status'=>true, "message"=>"Fecha aplicada", "users" => $users, "coms" => $coms]);
    }

    public function setNullDate(Request $request)
    {
        $status = User::where('id',$request->id)->first();
        if(intval($request->flagtype) == "1")
        {
            $status->invoice_flag = null;
        }
        else
        {
            $status->pay_flag = null;
        }
        $status->save();

        $users = $this->ReturnData();

        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->modify('-1 months');
        $coms = DB::select('call comition(?,?,?,?,?)',[$date->format('Y-m-d'),intval($date1->format('m')),intval($date1->format('Y')),intval($date2->format('m')),intval($date2->format('Y'))]);

        return response()->json(['status'=>true, "message"=>"Fecha cancelada", "users" => $users, "coms" => $coms]);
    }
}
