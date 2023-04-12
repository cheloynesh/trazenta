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
use Carbon\Carbon;
use DB;
use DateTime;

class OpeningController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        // $nucs = DB::table('Nuc')
        // ->select('Nuc.id as id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"nuc",'Status.id as statId','Status.name as estatus','color')
        // ->join('Client',"Client.id","=","Nuc.fk_client")
        // ->join('Status',"Nuc.estatus","=","Status.id")
        // ->whereNull('Client.deleted_at')
        // ->get();
        $agents = User::select('id', DB::raw('CONCAT(name," ",firstname) AS name'))->orderBy('name')->where("fk_profile","12")->pluck('name','id');
        $currencies = Currency::pluck('name','id');
        $insurances = Insurance::orderBy('name')->pluck('name','id');
        $paymentForms = Paymentform::pluck('name','id');
        $applications = Application::pluck('name','id');
        // $clients = DB::table('Nuc')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        // ->join('Client',"Client.id","=","Nuc.fk_client")
        // ->whereNull('Client.deleted_at')
        // ->pluck('name','id');
        $perm = Permission::permView($profile,31);
        $perm_btn =Permission::permBtns($profile,31);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","19")
        ->pluck('name','id');
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('process.opening.opening', compact('agents','perm_btn','cmbStatus','currencies','insurances','paymentForms','applications'));
        }
    }
}
