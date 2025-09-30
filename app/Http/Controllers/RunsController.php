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
use Carbon\Carbon;
use DB;
use DateTime;

class RunsController extends Controller
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
            return view('tools.runs.runs', compact('profile','perm_btn','cmbStatus','insurancesCP','insurancesLP'));
        }
    }

    public function GetinfoFund($type,$curr)
    {
        $opening = Insurance::where('fund_curr',$curr)->where('fund_type',$type)->where('active_fund',1)->first();
        // dd($opening);
        return response()->json(['status'=>true, "data"=>$opening]);
    }
}
