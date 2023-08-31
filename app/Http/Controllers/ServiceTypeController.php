<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ServiceType;
use App\Permission;
use App\User;

class ServiceTypeController extends Controller
{
    public function index ()
    {
        $services = ServiceType::get();
        $profile = User::findProfile();
        $perm = Permission::permView($profile,35);
        $perm_btn =Permission::permBtns($profile,35);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.service_type.service_type', compact('services','perm_btn'));
        }
    }
    // cambiar modelo de seguro a Servicio
    public function GetInfo($id)
    {
        $services = ServiceType::where('id',$id)->first();
        return response()->json(['status'=>true, "data"=>$services]);
    }

    public function store(Request $request)
    {
        $services = new ServiceType;
        $services->name = $request->name;
        $services->save();
        return response()->json(["status"=>true, "message"=>"Servicio creado"]);
    }

    public function update(Request $request, $id)
    {
        $services = ServiceType::where('id',$request->id)->update(['name'=>$request->name]);
        return response()->json(['status'=>true, 'message'=>"Servicio actualizado"]);
    }

    public function destroy($id)
    {
        $services = ServiceType::find($id);
        $services->delete();
        return response()->json(['status'=>true, "message"=>"Servicio eliminado"]);
    }
}
