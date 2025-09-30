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

class PaidComitionController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,34);
        $perm = Permission::permView($profile,34);
        date_default_timezone_set('America/Mexico_City');
        $date = new DateTime();
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('reports.paidcomition.paidcomition', compact('perm_btn'));
        }
    }

    public function GetInfo($start,$end)
    {
        $paidcomition = DB::select('call paidcomition(?,?)',[$start,$end]);

        return response()->json(['status'=>true, "data"=>$paidcomition]);
    }
}
