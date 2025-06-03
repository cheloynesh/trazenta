<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;
use App\Profile;
use App\Regime;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Controllers\PdfClass;
use DB;
use DateTime;


class LeaderController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $perm = Permission::permView($profile,37);
        $perm_btn =Permission::permBtns($profile,37);
        $leaders = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();
        $non_assinged = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull("fk_leader")->whereNull('deleted_at')->get();
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.leader.leader', compact('perm_btn','leaders'));
        }
    }

    public function GetInfoAll($id)
    {
        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "data"=>$leader]);
    }

    public function ViewNonLeader($id)
    {
        // dd($id);
        $non_leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",0)->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$non_leader]);
    }

    public function Dessign(Request $request)
    {
        $nuc = User::where('id',$request->id)->update(['team_leader'=>1]);
        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();
        $non_leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",0)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "noleader"=>$non_leader, "leader"=>$leader]);
    }

    public function DeleteLeader(Request $request)
    {
        $nuc = User::where('id',$request->id)->update(['team_leader'=>0]);

        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "leader"=>$leader]);
    }

    public function ViewNonAssigned($id)
    {
        // dd($id);
        $non_assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull('fk_leader')->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$non_assigned]);
    }

    public function ViewAssigned($id)
    {
        // dd($id);
        $assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where('fk_leader',$id)->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$assigned]);
    }

    public function Assign(Request $request)
    {
        $nuc = User::where('id',$request->agent)->update(['fk_leader'=>$request->id]);

        $non_assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull('fk_leader')->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "data"=>$non_assigned]);
    }

    public function DeleteAgent(Request $request)
    {
        $nuc = User::where('id',$request->agent)->update(['fk_leader'=>null]);

        $assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where('fk_leader',$request->id)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "data"=>$assigned]);
    }

    public function GetInfoComition(Request $request)
    {
        $b_amount = 0;
        $dll_conv = 0;
        $usd_invest = 0;
        $usd_invest1 = 0;
        $gross_amount = 0;
        $iva_amount = 0;
        $ret_isr = 0;
        $ret_iva = 0;
        $n_amount = 0;
        $seventy = 0;
        $thirty = 0;

        $nucs = DB::table('Nuc')->select("Nuc.id as id",'month_flag','nuc')
            ->where("month_flag",">=","1")
            ->where('fk_agent',$request->id)
            ->whereNull('deleted_at')
            ->get();
        // dd($nucs);
        $user = DB::table('users')->select('fk_regime')->where('users.id',$request->id)->whereNull('users.deleted_at')->first();
        $reg = Regime::where('id',$user->fk_regime)->first();

        if(intval($request->month) == 1)
        {
            $monthless = 12;
            $yearless = $request->year;
            $yearless -= 1;
        }
        else
        {
            $monthless = intval($request->month) - 1;
            $yearless = $request->year;
        }

        // dd($reg);
        foreach ($nucs as $nuc)
        {
            if($nuc->month_flag == 1) $value = $this->calculo($nuc->id,$monthless,$yearless,$request->TC,$reg,15);
            else $value = $this->calculo($nuc->id,$monthless,$yearless,$request->TC,$reg,13);
            $b_amount += $value["b_amount"];
            $dll_conv += $value["dll_conv"];
            $usd_invest += $value["usd_invest"];
            $usd_invest1 += $value["usd_invest1"];
            $gross_amount += $value["gross_amount"];
            $iva_amount += $value["iva_amount"];
            $ret_isr += $value["ret_isr"];
            $ret_iva += $value["ret_iva"];
            $n_amount += $value["n_amount"];
        }
        $seventy = $usd_invest1*.70;
        $thirty = $usd_invest1*.30;

        return response()->json(['status'=>true, 'b_amount'=>number_format($b_amount,2,'.',','), 'dll_conv'=>number_format($dll_conv,2,'.',','),
        'usd_invest'=>number_format($usd_invest,2,'.',','), 'usd_invest1'=>number_format($usd_invest1,2,'.',','), 'seventy'=>number_format($seventy,2,'.',','),
        'thirty'=>number_format($thirty,2,'.',',')]);
    }

    public function calculo($id, $month, $year, $TC, $regime,$dlls)
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
        $nuc = DB::table('Nuc')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),"cut_date","currency","nuc")
            ->join('Client','Nuc.fk_client','=','Client.id')->where('Nuc.id',$id)->first();
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

        $ret_isr = $gross_amount*$regime->ret_isr/100;

        $ret_iva = $iva_amount*$regime->ret_iva/100; //retencion de iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$b_amount,'dll_conv'=>$dll_conv,'usd_invest'=>$usd_invest,
        'usd_invest1'=>$usd_invest1, 'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount,
        'ret_isr'=>$ret_isr, 'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount,'cname'=>$nuc->cname,'nuc'=>$nuc->nuc);

        return($values);
    }

    public function calculoLP($id,$year,$leaderId)
    {
        $div_amount=0;//Saldo cierre de mes

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto

        // condicion para determinar fecha de corte

        $nuc = DB::table('users')->select(DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")) AS usname'),
            DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")) AS clname'),'nuc','pay_date','deposit_date',"SixMonth_fund.amount","currency","fk_insurance","users.id as uid")
            ->join('SixMonth_fund',"SixMonth_fund.fk_agent","=","users.id")
            ->join('Client',"SixMonth_fund.fk_client","=","Client.id")
            ->join('Coupon','fk_nuc','SixMonth_fund.id')
            ->where('number',1)
            ->whereNull('SixMonth_fund.deleted_at')
            ->whereNull('Coupon.deleted_at')
            ->where('SixMonth_fund.id',$id)->first();

        date_default_timezone_set('America/Mexico_City');

        $date1 = new DateTime($nuc->deposit_date);
        $date2 = new DateTime("2023-04-01");

        if($nuc->currency == "MXN")
        {
            $div_amount = $nuc->amount / 500000;
        }
        else
        {
            $div_amount = $nuc->amount / 25000;
        }

        if($leaderId == $nuc->uid)
        {
            if($year == 1) $gross_amount = 17000 * $div_amount;
            else $gross_amount = 8500 * $div_amount;
        }
        else
        {

            if($year == 1) $gross_amount = 1500 * $div_amount;
            else $gross_amount = 2500 * $div_amount;
        //     }
        //     else
        //     {
        //         if($year == 1) $gross_amount = 1500 * $div_amount;
        //         else $gross_amount = 2500 * $div_amount;
        //     }
        }

        $values = array('gross_amount'=>$gross_amount, 'usname'=>$nuc->usname, 'clname'=>$nuc->clname, 'nuc'=>$nuc->nuc,
        'pay_date'=>$nuc->pay_date, 'amount'=>$nuc->amount, 'currency'=>$nuc->currency);

        return($values);
    }

    public function GetPDF($id,$year,$month,$TC,$fst_yr,$scnd_yr)
    {
        $months = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        set_time_limit(1000);
        $usd_invest1 = 0;
        $leader_usd_invest1 = 0;
        $leader_b_amount = 0;

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

        $user = DB::table('users')->select('fk_regime')->where('users.id',$id)->whereNull('users.deleted_at')->first();
        $reg = Regime::where('id',$user->fk_regime)->first();

        $nucs = DB::table('Nuc')->select("Nuc.id as id",'month_flag','nuc')
            ->join('users','Nuc.fk_agent','=','users.id')
            ->where("month_flag",">=","1")
            ->where('fk_leader',$id)
            ->whereNull('Nuc.deleted_at')
            ->orderBy('nuc')
            ->get();

        $validNucs1 = array();
        array_push($validNucs1,0);

        foreach ($nucs as $nuc)
        {
            if($nuc->month_flag == 1) $value = $this->calculo($nuc->id,$monthless,$yearless,$TC,$reg,15);
            else $value = $this->calculo($nuc->id,$monthless,$yearless,$TC,$reg,13);
            $usd_invest1 += $value["usd_invest1"];
            if($value["usd_invest1"] != 0) array_push($validNucs1,array("cname" => $value["cname"],"nuc" => $value["nuc"], "usd_invest1" => $value["usd_invest1"], "ten" => $value["usd_invest1"]*.9, "b_amount" => $value["b_amount"]));
        }
        $thirty = $usd_invest1*.30;

        $nucsLeader = DB::table('Nuc')->select("Nuc.id as id",'month_flag','nuc')
            ->where("month_flag",">=","1")
            ->where('fk_agent',$id)
            ->whereNull('deleted_at')
            ->get();

        $validNucs = array();

        foreach ($nucsLeader as $nuc)
        {
            if($nuc->month_flag == 1) $value = $this->calculo($nuc->id,$monthless,$yearless,$TC,$reg,50);
            else $value = $this->calculo($nuc->id,$monthless,$yearless,$TC,$reg,23);
            $leader_usd_invest1 += $value["usd_invest1"];
            $leader_b_amount += $value["b_amount"];
            if($value["usd_invest1"] != 0) array_push($validNucs,array("cname" => $value["cname"],"nuc" => $value["nuc"], "usd_invest1" => $value["usd_invest1"], "ten" => $value["usd_invest1"]*.9, "b_amount" => $value["b_amount"]));
        }

        $lpTotal = 0;
        $nucs_fst = explode("-",$fst_yr);
        $fstNucsLP = array();
        foreach ($nucs_fst as $nuc)
        {
            if($nuc != 0)
            {
                $fst = $this->calculoLP($nuc,1,$id);
                array_push($fstNucsLP,$fst);
                $lpTotal += $fst["gross_amount"];
            }
        }

        $nucs_scnd = explode("-",$scnd_yr);
        $scndNucsLP = array();
        foreach ($nucs_scnd as $nuc)
        {
            if($nuc != 0)
            {
                $scnd = $this->calculoLP($nuc,2,$id);
                array_push($scndNucsLP,$scnd);
                $lpTotal += $scnd["gross_amount"];
            }
        }
        // dd($fstNucsLP);

        // dd($thirty,$leader_usd_invest1,$thirty+$leader_usd_invest1,$fstNucsLP);
        $pdf = new VicPDF();
        $pdf->PrintPDF($months[intval($monthless)]." ".$yearless,$thirty+$leader_usd_invest1,$TC,$validNucs,$fstNucsLP,$scndNucsLP,$lpTotal);
        $pdf->Output('D',"Factura.pdf");
        return;
    }

    public function GetSixMonth($id,$yr)
    {
        if($yr == 1)
        {
            $users = DB::table('users')->select('SixMonth_fund.id as nucid',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")) AS usname'),
                DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")) AS clname'),'nuc','fst_yr','scnd_yr')
                ->join('SixMonth_fund',"SixMonth_fund.fk_agent","=","users.id")
                ->join('Client',"SixMonth_fund.fk_client","=","Client.id")
                ->whereNull('fst_yr')
                ->whereNull('SixMonth_fund.deleted_at')
                ->whereNull('users.deleted_at')->get();
        }
        else
        {
            $users = DB::table('users')->select('SixMonth_fund.id as nucid',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(users.firstname, "")) AS usname'),
                DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")) AS clname'),'nuc','fst_yr','scnd_yr')
                ->join('SixMonth_fund',"SixMonth_fund.fk_agent","=","users.id")
                ->join('Client',"SixMonth_fund.fk_client","=","Client.id")
                ->whereNull('scnd_yr')
                ->whereNull('SixMonth_fund.deleted_at')
                ->whereNull('users.deleted_at')->get();
        }
        return response()->json(['status'=>true, "message"=>"Actualizado", "data"=>$users]);
    }
}
