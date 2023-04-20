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
use App\Opening;
use Carbon\Carbon;
use DB;
use DateTime;

class OpeningController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $agents = User::select('id', DB::raw('CONCAT(name," ",firstname) AS name'))->orderBy('name')->where("fk_profile","12")->pluck('name','id');
        $currencies = Currency::pluck('name','id');
        $insurances = Insurance::orderBy('name')->pluck('name','id');
        $paymentForms = Paymentform::pluck('name','id');
        $applications = Application::pluck('name','id');
        $perm = Permission::permView($profile,31);
        $perm_btn =Permission::permBtns($profile,31);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","31")
        ->pluck('name','id');
        $user = User::user_id();

        if($profile == 12)
        {
            $openings = DB::table("Status")
                ->select('fund_type','Opening.id as oid','Opening.name as cname','Opening.*', DB::raw('CONCAT(users.name," ",users.firstname) AS agent'),'Insurance.name as insurance','Status.*','Status.id as statid')
                ->join('Opening','Opening.fk_status','=','Status.id')
                ->join('Insurance','Insurance.id','=','fk_insurance')
                ->join('users','users.id','=','fk_agent')
                ->where('fk_agent',$user)
                ->whereNull('Opening.deleted_at')
                ->get();
        }
        else
        {
            $openings = DB::table("Status")
                ->select('fund_type','Opening.id as oid','Opening.name as cname','Opening.*', DB::raw('CONCAT(users.name," ",users.firstname) AS agent'),'Insurance.name as insurance','Status.*','Status.id as statid')
                ->join('Opening','Opening.fk_status','=','Status.id')
                ->join('Insurance','Insurance.id','=','fk_insurance')
                ->join('users','users.id','=','fk_agent')
                ->whereNull('Opening.deleted_at')
                ->get();
        }
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('process.opening.opening', compact('profile','agents','perm_btn','cmbStatus','currencies','insurances','paymentForms','applications','openings'));
        }
    }

    public function GetInfo($id)
    {
        // dd($id);
        $opening = Opening::where('id',$id)->first();
        return response()->json(['status'=>true, "data"=>$opening]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $opening = new Opening;
        $opening->fk_agent = $request->fk_agent;
        $opening->name = $request->name;
        $opening->firstname = $request->firstname;
        $opening->lastname = $request->lastname;
        $opening->rfc = $request->rfc;
        $opening->fk_insurance = $request->fk_insurance;
        $opening->fk_currency = $request->fk_currency;
        $opening->fk_application = $request->fk_application;
        $opening->fk_payment_form = $request->fk_payment_form;
        $opening->domicile = $request->domicile;
        $opening->amount = $request->amount;
        $opening->nuc = $request->nuc;
        $opening->save();

        return response()->json(["status"=>true, "message"=>"Apertura Creada"]);
    }

    public function update(Request $request, $id)
    {
        $opening = Opening::where('id',$request->id)->update(['fk_agent'=>$request->fk_agent,'name'=>$request->name,'firstname'=>$request->firstname,'lastname'=>$request->lastname,
        'rfc'=>$request->rfc,'fk_insurance'=>$request->fk_insurance,'fk_currency'=>$request->fk_currency,'fk_application'=>$request->fk_application,
        'fk_payment_form'=>$request->fk_payment_form,'domicile'=>$request->domicile,'amount'=>$request->amount,'nuc'=>$request->nuc]);
        return response()->json(['status'=>true, 'message'=>"Apertura Actualizada"]);
    }

    public function destroy($id)
    {
        $opening = Opening::find($id);
        $opening->delete();
        return response()->json(['status'=>true, "message"=>"Apertura eliminada"]);
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
        $status->agent_status = $request->agent_status;
        $status->office_status = $request->office_status;
        $status->finestra_status = $request->finestra_status;
        $status->save();

        return response()->json(['status'=>true, "message"=>"Estatus Actualizado"]);
    }
}
