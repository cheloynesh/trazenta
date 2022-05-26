<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Prueba;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MovesImport;

class PruebasController extends Controller
{
    public function index(){
        $profiles = Prueba::get();
        $prof = Prueba::pluck('name','id');
        return view('admin.pruebas.prueba', compact('profiles','prof'));
    }

    //--------------------------------------------- excel -----------------------------------------------------
    public function import(Request $request)
    {
        $file = $request->file('file');
        // $file = $request->file;
        $imp = new MovesImport();
        // dd($request);
        // Excel::import($imp, $file);
        $array = ($imp)->toArray($file);
        dd($array);

    }
    //---------------------------------------------------------------------------------------------------------

    public function GetInfo($id)
    {
        $profile = Prueba::where('id',$id)->first();
        // dd($profile);
        return response()->json(['status'=>true, "data"=>$profile]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $profile = new Prueba;
        $profile->name = $request->name;
        $profile->save();
        return response()->json(["status"=>true, "message"=>"Perfil Creado"]);
    }

    public function update(Request $request, $id)
    {
        $profile = Prueba::where('id',$request->id)->update(['name'=>$request->name]);
        return response()->json(['status'=>true, 'message'=>"Perfil Actualizado"]);
    }

    public function destroy($id)
    {
        $profile = Prueba::find($id);
        $profile->delete();
        return response()->json(['status'=>true, "message"=>"Perfil eliminado"]);
    }
}
