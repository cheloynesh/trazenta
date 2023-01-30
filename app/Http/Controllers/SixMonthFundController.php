<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthFund;
use App\Nuc;
use App\Status;
use App\Exports\ExportFund;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MovesImport;
use Carbon\Carbon;
use DB;

class SixMonthFundController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $nucs = DB::table('SixMonth_fund')
        ->select('*',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),'nuc','SixMonth_fund.id as id')
        ->join('Client',"Client.id","=","fk_client")
        ->get();
        $clients = DB::table('Nuc')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->join('Client',"Client.id","=","Nuc.fk_client")
        ->pluck('name','id');
        $perm = Permission::permView($profile,24);
        $perm_btn =Permission::permBtns($profile,24);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","23")
        ->pluck('name','id');
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.sixmonthfund.sixmonthfund', compact('nucs','perm_btn','cmbStatus','clients'));
        }
    }
    public function GetInfo($id)
    {
        $nuc = DB::table('SixMonth_fund')->select("*")->where('id',$id)->whereNull('deleted_at')->first();
        return response()->json(['status'=>true, "data"=>$nuc]);
    }
}
