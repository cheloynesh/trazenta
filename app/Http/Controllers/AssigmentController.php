<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Permission;
use App\User;
use DB;

class AssigmentController extends Controller
{
    public function index(){
        $users = User::get();
        // $clients = Client::whereNull("fk_agent")->pluck('name','id');
        $clients = DB::table('Client')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'), 'id')
        ->whereNull("fk_agent")->whereNull('deleted_at')->pluck('name','id');
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
            return view('admin.assigment.assigment', compact('clients','perm_btn','users'));
        }
    }

    public function Viewclients($id){
        // dd($id);
        $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$clients]);
    }

    public function updateClient(Request $request){
        // dd($request->all());
        $client = Client::where('id',$request->client)->first();
        // dd($client);
        $client->fk_agent = $request->id;
        $client->save();
        return response()->json(['status'=>true, "message"=>"Cliente asignado"]);
    }

    public function destroy($id)
    {
        $client = Client::where('id',$id)->first();
        // dd($client);
        $client->fk_agent = NULL;
        $client->save();
        return response()->json(['status'=>true, "message"=>"cliente eliminado"]);

    }
}
