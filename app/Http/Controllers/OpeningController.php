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
use Carbon\Carbon;
use DB;
use DateTime;

class OpeningController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $agents = User::select('id', DB::raw('CONCAT(name," ",firstname) AS name'))->orderBy('name')->where("fk_profile","12")->pluck('name','id');
        $currencies = Currency::pluck('name','id');
        $insurances = Insurance::orderBy('name')->get();
        $paymentForms = Paymentform::pluck('name','id');
        $applications = Application::pluck('name','id');
        $charges = Charge::pluck('name','id');
        $clients = Client::get();
        $perm = Permission::permView($profile,31);
        $perm_btn =Permission::permBtns($profile,31);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","31")
        ->pluck('name','id');
        $cmbStatusMF = Status::select('id','name')
        ->where("fk_section","19")
        ->pluck('name','id');
        $user = User::user_id();

        if($profile == 12)
        {
            $openings = DB::select('call aperturasAgente(?)',[$user]);
        }
        else
        {
            $openings = DB::select('call aperturas()');
        }
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('process.opening.opening', compact('profile','agents','perm_btn','cmbStatus','cmbStatusMF','currencies','insurances','paymentForms','applications','openings','clients','charges'));
        }
    }

    public function ReturnData($profile)
    {
        $user = User::user_id();
        if($profile == 12)
        {
            $openings = DB::select('call aperturasAgente(?)',[$user]);
        }
        else
        {
            $openings = DB::select('call aperturas()');
        }
        return $openings;
    }

    public function GetInfo($id)
    {
        // dd($id);
        $opening = Opening::where('id',$id)->first();
        $insurance = Insurance::where('id',$opening->fk_insurance)->first();

        if($insurance->fund_type == "CP")
        {
            $insurances = Insurance::where('fund_type','CP')->get();
            $opening = DB::table('Opening')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
                'Opening.fk_agent','fk_application','fk_payment_form','Opening.fk_insurance','nuc','currency','estatus','Client.id as clid','Opening.id as opid','fund_type','fk_charge','yield','yield_usd')
                ->join('Client','Opening.fk_client','=','Client.id')
                ->join('Insurance','Opening.fk_insurance','=','Insurance.id')
                ->join('Nuc','Opening.fk_nuc','=','Nuc.id')
                ->where('Opening.id',$id)->first();
        }
        else
        {
            $insurances = Insurance::where('fund_type','LP')->get();
            $opening = DB::table('Opening')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
                'Opening.fk_agent','fk_application','fk_payment_form','Opening.fk_insurance','nuc','currency','deposit_date','amount','Client.id as clid','Opening.id as opid','fund_type','fk_charge','yield','yield_usd')
                ->join('Client','Opening.fk_client','=','Client.id')
                ->join('Insurance','Opening.fk_insurance','=','Insurance.id')
                ->join('SixMonth_fund','Opening.fk_nuc','=','SixMonth_fund.id')
                ->where('Opening.id',$id)->first();
        }

        // dd($opening,$insurances);
        return response()->json(['status'=>true, "data"=>$opening, "insurances"=>$insurances]);
    }

    public function GetinfoFund($id)
    {
        // dd($id);
        $opening = Insurance::where('id',$id)->first();
        return response()->json(['status'=>true, "data"=>$opening]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $fk_client = $request->fk_client;
        if($fk_client == 0)
        {
            $client = new Client;
            $client->name = $request->name;
            $client->firstname = $request->firstname;
            $client->lastname = $request->lastname;
            $client->birth_date = $request->birth_date;
            $client->rfc = $request->rfc;
            $client->curp = $request->curp;
            $client->cellphone = $request->cellphone;
            $client->email = $request->email;
            $client->domicile = $request->domicile;
            // $client->fk_agent = $request->fk_agent;
            $client->save();
            $fk_client = $client->id;
        }

        if($request->fund_type == "CP")
        {
            $nuc = new Nuc;
            $nuc->nuc = $request->nuc;
            $nuc->currency = $request->selectCurrency;
            $nuc->fk_client = $fk_client;
            $nuc->estatus = $request->estatus;
            $nuc->fk_application = $request->fk_application;
            $nuc->fk_payment_form = $request->fk_payment_form;
            $nuc->fk_charge = $request->fk_charge;
            $nuc->fk_insurance = $request->fk_insurance;
            $nuc->fk_agent = $request->fk_agent;
            $nuc->save();
            $fk_nuc = $nuc->id;
        }
        else
        {
            $deposit_date = new DateTime($request->deposit_date);
            $initial_date = new DateTime($deposit_date->format('Y')."-".$deposit_date->format('m')."-01");
            $initial = clone $initial_date;
            $initialflag = 0;
            $initialdiff = 0;

            if(intval($deposit_date->format('d')) <= 10)
            {
                $initial_date->modify('+2 month');
                $initialflag = 0;
                $initialdiff = $deposit_date->diff($initial)->days;
            }
            else
            {
                $initial_date->modify('+3 month');
                $initialflag = 1;
                $initial->modify('+1 month');
                $initialdiff = $deposit_date->diff($initial)->days;
            }

            $end_date = clone $deposit_date;
            $end_date->modify('+2 year');

            $nuc = new SixMonth_fund;
            $nuc->nuc = $request->nuc;
            $nuc->currency = $request->selectCurrency;
            $nuc->amount = $request->amount;
            $nuc->fk_client = $fk_client;
            $nuc->deposit_date = $deposit_date;
            $nuc->initial_date = $initial_date;
            $nuc->end_date = $end_date;
            $nuc->fk_application = $request->fk_application;
            $nuc->fk_payment_form = $request->fk_payment_form;
            $nuc->fk_charge = $request->fk_charge;
            $nuc->fk_insurance = $request->fk_insurance;
            $nuc->fk_agent = $request->fk_agent;
            $nuc->save();
            $fk_nuc = $nuc->id;

            $date1 = clone $initial_date;
            $date2= clone $deposit_date;
            $number = 1;
            for($cont = 0; $cont < 22; $cont += 2)
            {
                $coupon = new Coupon;
                $coupon->number = $number;
                if($cont != 0) $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                if($request->currency == "MXN")
                {
                    $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield/100))/360);
                }
                else
                {
                    $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield_usd/100))/360);
                }
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $nuc->id;
                $coupon->save();
                $number++;
                $date2= clone $date1;
            }
            $coupon = new Coupon;
            $coupon->number = $number;
            $date1->modify('+2 month');
            $diff = $date1->diff($date2)->days;
            if($request->currency == "MXN")
            {
                $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield/100))/360);
                if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($request->yield/100))/360);
                else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($request->yield/100))/360);
            }
            else
            {
                $coupon->amount = intval($diff) * (($nuc->amount * ($request->yield_usd/100))/360);
                if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($request->yield_usd/100))/360);
                else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($request->yield_usd/100))/360);
            }
            // dd($coupon->amount);
            $coupon->pay_date = $date1;
            $coupon->fk_nuc = $nuc->id;
            $coupon->save();
        }

        $opening = new Opening;
        $opening->fk_agent = $request->fk_agent;
        $opening->fk_client = $fk_client;
        $opening->fk_nuc = $fk_nuc;
        $opening->fk_insurance = $request->fk_insurance;
        $opening->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $openings = $this->ReturnData($profile);

        return response()->json(["status"=>true, "message"=>"Servicio Creado", "openings" => $openings, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $opening = Opening::where('id',$request->id)->first();
        $fk_client = $request->fk_client;
        if($fk_client == 0)
        {
            $client = new Client;
            $client->name = $request->name;
            $client->firstname = $request->firstname;
            $client->lastname = $request->lastname;
            $client->birth_date = $request->birth_date;
            $client->rfc = $request->rfc;
            $client->curp = $request->curp;
            $client->cellphone = $request->cellphone;
            $client->email = $request->email;
            $client->domicile = $request->domicile;
            // $client->fk_agent = $request->fk_agent;
            $client->save();
            $fk_client = $client->id;
        }
        // else
        // {
        //     $client = Client::where('id',$fk_client)->first();
        //     // dd($opening,$client);
        //     if($client->fk_agent != $opening->fk_agent)
        //     {
        //         $clnt = Client::where('id',$fk_client)->update(['fk_agent'=>$request->fk_agent]);
        //     }
        // }
        if($request->fund_type == "CP")
        {
            $nuc = Nuc::where('id',$opening->fk_nuc)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client,'fk_application'=>$request->fk_application,'fk_payment_form'=>$request->fk_payment_form,
            'fk_charge'=>$request->fk_charge,'fk_insurance'=>$request->fk_insurance,'currency'=>$request->currency,'fk_agent'=>$request->fk_agent]);
        }
        else
        {
            $deposit_date = new DateTime($request->deposit_date);
            $initial_date = new DateTime($deposit_date->format('Y')."-".$deposit_date->format('m')."-01");
            $initial = clone $initial_date;
            $initialflag = 0;
            $initialdiff = 0;

            if(intval($deposit_date->format('d')) <= 10)
            {
                $initial_date->modify('+2 month');
                $initialflag = 0;
                $initialdiff = $deposit_date->diff($initial)->days;
            }
            else
            {
                $initial_date->modify('+3 month');
                $initialflag = 1;
                $initial->modify('+1 month');
                $initialdiff = $deposit_date->diff($initial)->days;
            }

            $end_date = clone $deposit_date;
            $end_date->modify('+2 year');

            $nuc = DB::table('SixMonth_fund')->select('*')
            ->join('Insurance','Insurance.id','=','fk_insurance')
            ->where('SixMonth_fund.id',$opening->fk_nuc)->first();

            $nucEdit = SixMonth_fund::where('id',$opening->fk_nuc)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client, 'amount'=>$request->amount,'currency'=>$request->currency,
                'deposit_date'=>$deposit_date,'initial_date'=>$initial_date,'end_date'=>$end_date, 'fk_application'=>$request->fk_application,'fk_payment_form'=>$request->fk_payment_form,
                'fk_charge'=>$request->fk_charge,'fk_insurance'=>$request->fk_insurance,'fk_agent'=>$request->fk_agent]);

            if(floatval($request->amount) != floatval($nuc->amount) || $request->currency != $nuc->currency || $request->deposit_date != $nuc->deposit_date)
            {
                $coupons = Coupon::where("fk_nuc",$opening->fk_nuc)->get();
                foreach($coupons as $coup)
                {
                    $coup->delete();
                }

                $date1 = clone $initial_date;
                $date2= clone $deposit_date;
                $number = 1;
                for($cont = 0; $cont < 22; $cont += 2)
                {
                    $coupon = new Coupon;
                    $coupon->number = $number;
                    if($cont != 0) $date1->modify('+2 month');
                    $diff = $date1->diff($date2)->days;
                    if($request->currency == "MXN")
                    {
                        $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield/100))/360);
                    }
                    else
                    {
                        $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield_usd/100))/360);
                    }
                    $coupon->pay_date = $date1;
                    $coupon->fk_nuc = $opening->fk_nuc;
                    $coupon->save();
                    $number++;
                    $date2= clone $date1;
                }
                $coupon = new Coupon;
                $coupon->number = $number;
                $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                if($request->currency == "MXN")
                {
                    $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield/100))/360);
                    if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($request->amount * ($nuc->yield/100))/360);
                    else $coupon->amount -= intval($initialdiff) * (($request->amount * ($nuc->yield/100))/360);
                }
                else
                {
                    $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield_usd/100))/360);
                    if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($request->amount * ($nuc->yield_usd/100))/360);
                    else $coupon->amount -= intval($initialdiff) * (($request->amount * ($nuc->yield_usd/100))/360);
                }
                // dd($coupon->amount);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $opening->fk_nuc;
                $coupon->save();
            }
        }

        $opening = Opening::where('id',$request->id)->update(['fk_agent'=>$request->fk_agent,'fk_client'=>$request->fk_client,'fk_insurance'=>$request->fk_insurance]);

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $openings = $this->ReturnData($profile);

        return response()->json(['status'=>true, 'message'=>"Apertura Actualizada", "openings" => $openings, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function destroy($id)
    {
        $opening = DB::table('Opening')->join('Insurance','Opening.fk_insurance','=','Insurance.id')->where('Opening.id',$id)->first();

        if($opening->fund_type == "CP")
        {
            $fund = Nuc::find($opening->fk_nuc);
            $fund->delete();
        }
        else
        {
            $SixMonth_fund = SixMonth_fund::find($opening->fk_nuc);
            $SixMonth_fund->delete();
        }

        $opening = Opening::find($id);
        $opening->delete();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $openings = $this->ReturnData($profile);

        return response()->json(['status'=>true, "message"=>"Apertura eliminada", "openings" => $openings, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function GetinfoStatus($id)
    {
        $opening = Opening::where('id',$id)->first();
        // dd($initial->commentary);
        return response()->json(['status'=>true, "data"=>$opening]);
    }

    public function updateStatus(Request $request)
    {
        $status = Opening::where('id',$request->id)->first();
        // dd($status);
        $status->fk_status = $request->fk_status;
        $status->pick_status = $request->pick_status;
        $status->limit_status = $request->limit_status;
        $status->agent_status = $request->agent_status;
        $status->office_status = $request->office_status;
        $status->finestra_status = $request->finestra_status;
        $status->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $openings = $this->ReturnData($profile);

        return response()->json(['status'=>true, "message"=>"Estatus Actualizado", "openings" => $openings, "profile" => $profile, "permission" => $perm_btn]);
    }
}
