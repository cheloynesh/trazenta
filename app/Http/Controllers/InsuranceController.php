<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Insurance;
use App\User;
use App\Permission;
use App\Profile;
use App\Regime;
use DB;

class InsuranceController extends Controller
{
    public function index(){
        $insurances = Insurance::get();
        $profile = User::findProfile();
        $perm = Permission::permView($profile,25);
        $perm_btn =Permission::permBtns($profile,25);
        $regimes = Regime::pluck('name','id');
        $type = array(0=>"APORTACIONES",1=>"CAPORTA",2=>"OBCREDA",3=>"OBCREDA DE OCCIDENTE",4=>"OBDENA",""=>"-");
        // dd($regimes);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.insurance.insurances', compact('insurances','perm_btn','regimes','type'));
        }
    }

    public function GetInfo($id)
    {
        $insurance = Insurance::where('id',$id)->first();
        return response()->json(['status'=>true, "data"=>$insurance]);
    }

    public function store(Request $request)
    {
        $insurance = new Insurance;
        $insurance->name = $request->name;
        $insurance->fund_type = $request->fund_type;
        $insurance->fund_curr = $request->fund_curr;
        $insurance->yield = $request->yield;
        $insurance->yield_net = $request->yield_net;
        $insurance->active_fund = $request->active_fund;
        $insurance->type = $request->type;
        $insurance->save();
        $insurance = Insurance::get();
        return response()->json(["status"=>true, "message"=>"Fondo creado", "funds"=>$insurance]);
    }

    public function update(Request $request, $id)
    {
        $insurance = Insurance::where('id',$request->id)->update(['name'=>$request->name,'fund_type'=>$request->fund_type,
        'fund_curr'=>$request->fund_curr,'yield'=>$request->yield,'yield_net'=>$request->yield_net,'active_fund'=>$request->active_fund,'type'=>$request->type]);
        $insurance = Insurance::get();
        return response()->json(['status'=>true, 'message'=>"Fondo actualizado", "funds"=>$insurance]);
    }

    public function destroy($id)
    {
        $insurance = Insurance::find($id);
        $insurance->delete();
        $insurance = Insurance::get();
        return response()->json(['status'=>true, "message"=>"Fondo eliminado", "funds"=>$insurance]);
    }
}
