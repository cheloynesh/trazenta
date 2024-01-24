<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;
use App\Profile;
use App\Regime;
use DB;


class RegimeController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $regimes = Regime::get();
        $perm = Permission::permView($profile,38);
        $perm_btn =Permission::permBtns($profile,38);
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.regime.regime', compact('perm_btn','regimes'));
        }
    }

    public function GetInfo($id)
    {
        $regime = Regime::where('id',$id)->first();
        return response()->json(['status'=>true, "data"=>$regime]);
    }

    public function store(Request $request)
    {
        $regime = new Regime;
        $regime->name = $request->name;
        $regime->iva = $request->iva;
        $regime->ret_iva = $request->ret_iva;
        $regime->ret_isr = $request->ret_isr;
        $regime->save();
        return response()->json(["status"=>true, "message"=>"Régimen creado"]);
    }

    public function update(Request $request, $id)
    {
        $regime = Regime::where('id',$request->id)->update(['name'=>$request->name,'iva'=>$request->iva,'ret_isr'=>$request->ret_isr,'ret_iva'=>$request->ret_iva]);
        return response()->json(['status'=>true, 'message'=>"Régimen actualizado"]);
    }

    public function destroy($id)
    {
        $regime = Regime::find($id);
        $regime->delete();
        return response()->json(['status'=>true, "message"=>"Régimen eliminado"]);
    }
}
