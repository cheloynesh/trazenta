<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthlyComission;
use App\Nuc;
use App\Status;
use App\Insurance;
use App\Exports\ExportBreakdown;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use DB;

class MoneyFlowController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,34);
        $perm = Permission::permView($profile,34);
        $insurances = Insurance::orderBy('name')->pluck('name','id');
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();

        $moneyflow = DB::select('call moneyflow(?,?,?,?)',[$date->format('Y'),'%','%','%']);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('reports.moneyflow.moneyflow', compact('perm_btn','moneyflow','insurances'));
        }
    }

    public function GetInfoFilters(Request $request)
    {
        $moneyflow = DB::select('call moneyflow(?,?,?,?)',[$request->year,$request->month,$request->quarter,$request->fund]);
        // dd($request->all());
        return response()->json(['status'=>true, "moneyflow"=>$moneyflow]);
    }

    public function ExportBreakdown($id,$month,$quarter,$year,$fund,$type)
    {
        $user = User::where('id',$id)->first();
        // dd($id);
        $nombre = "DESGLOSE_".(string)$user->name."_".$user->firstname.".xlsx";
        $sheet = new ExportBreakdown($id,$month,$quarter,$year,$fund,$type);
        return Excel::download($sheet,$nombre);
    }
}
