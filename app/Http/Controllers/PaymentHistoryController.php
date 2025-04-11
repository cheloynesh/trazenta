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
use App\Payment_History;
use DateTime;
use DB;

class PaymentHistoryController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        $date->setDate($date->format('Y'), $date->format('m'), 1);
        $date->modify('-1 months');
        $date2 = new DateTime();
        $date2->setDate($date2->format('Y'), $date2->format('m'), 1);
        // dd($date2,$date1,$date);
        // $users = DB::select('call historyComition()');
        // dd($users);
        // dd($users);
        $perm = Permission::permView($profile,42);
        $perm_btn =Permission::permBtns($profile,42);
        // dd($perm_btn);
        $regimes = Regime::pluck('name','id');
        $user = User::user_id();
        // dd($clients);

        if($profile == 12)
        {
            $users = DB::select('call historyComition(?)',[$user]);
        }
        else
        {
            $users = DB::select('call historyComition("%")');
        }

        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('comitions.history.history', compact('users','perm_btn','regimes','profile'));
        }
    }

    public function GetInfo($id,$invoice,$contpp,$contpa,$lpnopay)
    {
        $regime = DB::table('users')->select('fk_regime','dlls')->where('id',$id)->first();
        $profile = User::findProfile();

        // dd($id);
        $rec = 0;
        $pp = 0;
        $pa = 0;
        $lp = 0;

        if(intval($invoice) != 0)
        {
            $rec = DB::select('call historyCurr(?)',[$id]);
        }

        if(intval($contpp) != 0)
        {
            $pp = DB::select('call historyFst(?)',[$id]);
        }

        if(intval($contpa != 0))
        {
            $pa = DB::select('call historyInc(?)',[$id]);
        }

        if(intval($lpnopay != 0))
        {
            $lp = DB::select('call historyLP(?)',[$id]);
        }

        return response()->json(['status'=>true, "regime"=>$regime, "rec"=>$rec, "pp"=>$pp, "pa"=>$pa, "lp"=>$lp, "profile"=>$profile]);
    }
}
