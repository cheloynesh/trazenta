<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\Nuc;
use App\Status;
use App\Currency;
use App\Insurance;
use App\Paymentform;
use App\Application;
use App\Charge;
use App\Opening;
use App\SixMonth_fund;
use App\Coupon;
use App\Charge_Moves;
use App\ExchangeRate;
use App\Regime;
use Carbon\Carbon;
use DB;
use DateTime;

class ComitioncalcController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $insurancesCP = Insurance::whereIn('fund_type',['CP','MP'])->where('active_fund','1')->orderBy('name')->get();
        $insurancesLP = Insurance::where('fund_type','LP')->where('active_fund','1')->orderBy('name')->get();
        $perm = Permission::permView($profile,45);
        $perm_btn =Permission::permBtns($profile,45);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","45")
        ->pluck('name','id');
        $user = User::user_id();

        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('tools.comitioncalc.comitioncalc', compact('profile','perm_btn','cmbStatus','insurancesCP','insurancesLP'));
        }
    }

    public function GetinfoFund($id)
    {
        $opening = Insurance::where('id',$id)->first();
        // dd($opening);
        return response()->json(['status'=>true, "data"=>$opening]);
    }

    public function GetCPCalc($id, Request $request)
    {
        $user = User::findUser();
        $TC = ExchangeRate::latest('id')->first();
        $regime = Regime::where('id',$user->fk_regime)->first();
        $b_amountFst = 0;
        $b_amountRec = 0;

        $value5 = $this->calculo($request->opening,$request->curr,$TC->amount,$user->five_month,$regime);
        $value1 = $this->calculo($request->opening,$request->curr,$TC->amount,$user->fst_month,$regime);

        $value = $this->calculo($request->opening,$request->curr,$TC->amount,$user->dlls,$regime);

        $b_amountFst += $value5["gross_amount"]*5 + $value1["gross_amount"];
        $b_amountRec += $value["gross_amount"];

        return response()->json(['status'=>true, "b_amountFst"=>$b_amountFst, "b_amountRec"=>$b_amountRec]);
    }

    public function GetLPCalc($id, Request $request)
    {
        $user = User::findUser();
        $TC = ExchangeRate::latest('id')->first();
        $regime = Regime::where('id',$user->fk_regime)->first();
        $b_amountLP = 0;

        $value = $this->calculoLP($request->opening,$request->curr,$regime,12500);
        $b_amountLP += $value["gross_amount"];
        // dd($b_amountLP);

        return response()->json(['status'=>true, "b_amountLP"=>$b_amountLP]);
    }

    public function calculo($b_amount, $currency, $TC, $dllMult, $regime)
    {
        $dll_conv=0;//conversion a usd
        $usd_invest=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)
        $usd_invest1=0;//para cada 5,00 usd sobre el monto invertido(esto multiplicar x10)

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto

        if($currency == "MXN")
        {
            $dll_conv = $b_amount / $TC; //si es en pesos, ponemos valor en usd

        }else{
            $dll_conv = $b_amount; //si es en dolares, se queda igual

        }
        $usd_invest = $dll_conv/5000; //por cada 5000 sobre el monto invertido
        $usd_invest1 = $usd_invest*$dllMult; //se multiplica por 10 el resultado obtenido
        // dd($usd_invest);

        $gross_amount = $usd_invest1 * $TC; //monto bruto

        $iva_amount = $gross_amount*$regime->iva/100; // iva del monto bruto

        // dd($regime);
        $ret_isr = $gross_amount*$regime->ret_isr/100;

        $ret_iva = $iva_amount*$regime->ret_iva/100; //retencion de iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$b_amount,'dll_conv'=>$dll_conv,'usd_invest1'=>$usd_invest1,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount);
        // dd($values);
        // dd($values);

        return($values);
    }

    public function calculoLP($amount, $currency, $regime, $comition)
    {
        $div_amount=0;//Saldo cierre de mes

        $gross_amount=0;//Monto bruto = usd_invest * $request->TC

        $iva_amount=0;//iva del monto bruto
        $ret_isr=0; //retencion del isr 10% monto bruto
        $ret_iva=0;//retencion de iva son 2 3ras partes del iva de montro bruto 2(IVA)/3
        $n_amount=0;//monto neto

        if($currency == "MXN")
        {
            $div_amount = $amount / 500000;
        }
        else
        {
            $div_amount = $amount / 25000;
        }

        $gross_amount = $comition * $div_amount;

        $iva_amount = $gross_amount*$regime->iva/100; // iva del monto bruto

        // dd($regime);
        $ret_isr = $gross_amount*$regime->ret_isr/100;

        $ret_iva = $iva_amount*$regime->ret_iva/100; //retencion de iva

        $n_amount= ($gross_amount + $iva_amount) - ($ret_isr + $ret_iva); //Monto neto

        $values = array("b_amount"=>$amount,
        'gross_amount'=>$gross_amount, 'iva_amount'=>$iva_amount, 'ret_isr'=>$ret_isr,
        'ret_iva'=>$ret_iva, 'n_amount'=>$n_amount);

        return($values);
    }
}
