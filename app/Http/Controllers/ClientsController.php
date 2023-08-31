<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Permission;
use App\User;
use App\Nuc;
use App\Coupon;
use App\SixMonth_fund;
use App\Paymentform;
use App\Application;
use App\Insurance;
use App\Charge;
use DB;
use DateTime;

class ClientsController extends Controller
{
    public function index(){
        $clients = Client::get();
        $profile = User::findProfile();
        $perm = Permission::permView($profile,5);
        $perm_btn =Permission::permBtns($profile,5);
        $insurances = Insurance::orderBy('name')->get();
        $paymentForms = Paymentform::pluck('name','id');
        $charges = Charge::pluck('name','id');
        $applications = Application::pluck('name','id');
        $agents = User::select('id', DB::raw('CONCAT(name," ",firstname) AS name'))->orderBy('name')->where("fk_profile","12")->pluck('name','id');
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.client.clients', compact('clients','perm_btn','paymentForms','applications','insurances','charges','agents'));
        }
    }

    public function GetInfo($id)
    {
        $client = Client::where('id',$id)->first();
        // dd($user);
        return response()->json(['status'=>true, "data"=>$client]);

    }

    public function store(Request $request)
    {
        // dd($request->all());
        $client = new Client;
        $client->name = $request->name;
        $client->firstname = $request->firstname;
        $client->lastname = $request->lastname;
        $client->birth_date = $request->birth_date;
        $client->rfc = $request->rfc;
        $client->curp = $request->curp;
        // $client->gender = $request->gender;
        // $client->marital_status = $request->marital_status;
        // $client->street = $request->street;
        // $client->e_num = $request->e_num;
        // $client->i_num = $request->i_num;
        // $client->pc = $request->pc;
        // $client->suburb = $request->suburb;
        // $client->country = $request->country;
        // $client->state = $request->state;
        // $client->city = $request->city;
        $client->cellphone = $request->cellphone;
        $client->email = $request->email;
        $client->domicile = $request->domicile;
        $client->save();
        return response()->json(["status"=>true, "message"=>"Cliente Creada"]);
    }

    public function update(Request $request)
    {
        $client = Client::where('id',$request->id)
        ->update(['name'=>$request->name, 'firstname'=>$request->firstname,'lastname'=>$request->lastname,
            'birth_date'=>$request->birth_date, 'rfc'=>$request->rfc,'curp'=>$request->curp,
            'cellphone'=>$request->cellphone,'email'=>$request->email,'domicile'=>$request->domicile]);
            // 'gender'=>$request->gender, 'marital_status'=>$request->marital_status,'street'=>$request->street,
            // 'e_num'=>$request->e_num, 'i_num'=>$request->i_num,'pc'=>$request->pc,
            // 'suburb'=>$request->suburb, 'country'=>$request->country,'state'=>$request->state,
            // 'city'=>$request->city,
        return response()->json(['status'=>true, 'message'=>"Cliente Actualizado"]);

    }

    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();
        return response()->json(['status'=>true, "message"=>"cliente eliminado"]);

    }
    public function SaveNuc(Request $request)
    {
        $nuc = new Nuc;
        $nuc->nuc = $request->nuc;
        $nuc->currency = $request->selectCurrency;
        $nuc->fk_client = $request->fk_client;
        $nuc->estatus = $request->estatus;
        $nuc->fk_application = $request->fk_application;
        $nuc->fk_payment_form = $request->fk_payment_form;
        $nuc->fk_charge = $request->fk_charge;
        $nuc->fk_insurance = $request->fk_insurance;
        $nuc->fk_agent = $request->fk_agent;
        $nuc->save();
        return response()->json(["status"=>true, "message"=>"Nuc creado"]);
    }
    public function SaveNucSixMonth(Request $request)
    {
        // dd($request->all());
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
        $nuc->fk_client = $request->fk_client;
        $nuc->deposit_date = $deposit_date;
        $nuc->initial_date = $initial_date;
        $nuc->end_date = $end_date;
        $nuc->fk_application = $request->fk_application;
        $nuc->fk_payment_form = $request->fk_payment_form;
        $nuc->fk_charge = $request->fk_charge;
        $nuc->fk_insurance = $request->fk_insurance;
        $nuc->fk_agent = $request->fk_agent;
        $nuc->save();

        $date1 = clone $initial_date;
        $date2= clone $deposit_date;
        $number = 1;

        $insurance = Insurance::where('id',$nuc->fk_insurance)->first();

        for($cont = 0; $cont < 22; $cont += 2)
        {
            $coupon = new Coupon;
            $coupon->number = $number;
            if($cont != 0) $date1->modify('+2 month');
            $diff = $date1->diff($date2)->days;
            if($nuc->currency == "MXN")
            {
                $coupon->amount = intval($diff) * (($nuc->amount * ($insurance->yield/100))/360);
            }
            else
            {
                $coupon->amount = intval($diff) * (($nuc->amount * ($insurance->yield_usd/100))/360);
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
        if($nuc->currency == "MXN")
        {
            $coupon->amount = intval($diff) * (($nuc->amount * ($insurance->yield/100))/360);
            if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($insurance->yield/100))/360);
            else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($insurance->yield/100))/360);
        }
        else
        {
            $coupon->amount = intval($diff) * (($nuc->amount * ($insurance->yield_usd/100))/360);
            if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($insurance->yield_usd/100))/360);
            else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($insurance->yield_usd/100))/360);
        }
        // dd($coupon->amount);
        $coupon->pay_date = $date1;
        $coupon->fk_nuc = $nuc->id;
        $coupon->save();
        return response()->json(["status"=>true, "message"=>"Fondo creado"]);
        // $date1 = new DateTime("2023-11-01");
        // $date2 = new DateTime("2024-01-01");
        // $diff = $date1->diff($date2);
    }
}
