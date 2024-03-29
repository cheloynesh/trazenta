<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Permission;
use App\User;
use App\Nuc;
use App\SixMonth_fund;
use DB;

class AssigmentController extends Controller
{
    public function index()
    {
        $users = User::where("fk_profile","12")->get();
        // $clients = Client::whereNull("fk_agent")->pluck('name','id');
        $clients = DB::table('Client')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'), 'id')
        ->whereNull("fk_agent")->whereNull('deleted_at')->pluck('name','id');
        $agents = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->where("fk_profile","12")
        ->orderBy('name')->whereNull('deleted_at')->pluck('name','id');
        // dd($clients);
        // if($clients->isEmpty())
        //     dd("vacio");
        // else
        //     dd("lleno");
        // $client = Client::whereNull("fk_agent")->get();
        // dd($clients);
        $profile = User::findProfile();
        $perm = Permission::permView($profile,20);
        $perm_btn =Permission::permBtns($profile,20);
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.assigment.assigment', compact('clients','perm_btn','users','agents'));
        }
    }

    public function Viewclients($id)
    {
        // dd($id);
        $clients = DB::select('call asignaciones(?)',[$id]);
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$clients]);
    }

    public function ViewNonAssigned($id)
    {
        // dd($id);
        $clients = DB::select('call noAsignados()');
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$clients]);
    }

    public function updateClient(Request $request)
    {
        if($request->fund == "CP")
        {
            $nuc = Nuc::where('id',$request->nuc)->update(['fk_agent'=>$request->agent]);
        }
        else
        {
            $nucEdit = SixMonth_fund::where('id',$request->nuc)->update(['fk_agent'=>$request->agent]);
        }

        $clients = DB::select('call noAsignados()');

        return response()->json(['status'=>true, "message"=>"Contrato Asignado", "data"=>$clients]);
    }

    public function destroy($id, Request $request)
    {
        if($request->fund == "CP")
        {
            $nuc = Nuc::where('id',$id)->update(['fk_agent'=>null]);
        }
        else
        {
            $nucEdit = SixMonth_fund::where('id',$id)->update(['fk_agent'=>null]);
        }

        $clients = DB::select('call asignaciones(?)',[$request->idAgent]);

        return response()->json(['status'=>true, "message"=>"Asignación Removida", "data"=>$clients]);

    }
}
