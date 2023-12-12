<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;
use App\Profile;
use DB;


class LeaderController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $perm = Permission::permView($profile,37);
        $perm_btn =Permission::permBtns($profile,37);
        $leaders = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();
        $non_assinged = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull("fk_leader")->whereNull('deleted_at')->get();
        // dd($perm_btn);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('admin.leader.leader', compact('perm_btn','leaders'));
        }
    }

    public function GetInfoAll($id)
    {
        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "data"=>$leader]);
    }

    public function ViewNonLeader($id)
    {
        // dd($id);
        $non_leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",0)->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$non_leader]);
    }

    public function Dessign(Request $request)
    {
        $nuc = User::where('id',$request->id)->update(['team_leader'=>1]);
        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();
        $non_leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",0)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "noleader"=>$non_leader, "leader"=>$leader]);
    }

    public function DeleteLeader(Request $request)
    {
        $nuc = User::where('id',$request->id)->update(['team_leader'=>0]);

        $leader = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where("team_leader",1)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "leader"=>$leader]);
    }

    public function ViewNonAssigned($id)
    {
        // dd($id);
        $non_assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull('fk_leader')->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$non_assigned]);
    }

    public function ViewAssigned($id)
    {
        // dd($id);
        $assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where('fk_leader',$id)->whereNull('deleted_at')->get();
        // $clients = Client::where('fk_agent',$id)->get();
        // dd($clients);
        return response()->json(['status'=>true, "data"=>$assigned]);
    }

    public function Assign(Request $request)
    {
        $nuc = User::where('id',$request->agent)->update(['fk_leader'=>$request->id]);

        $non_assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->whereNull('fk_leader')->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "data"=>$non_assigned]);
    }

    public function DeleteAgent(Request $request)
    {
        $nuc = User::where('id',$request->agent)->update(['fk_leader'=>null]);

        $assigned = DB::table('users')->select('id',DB::raw('CONCAT(IFNULL(name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"email")
            ->orderBy('name')->where('fk_leader',$request->id)->whereNull('deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Actualizado", "data"=>$assigned]);
    }
}
