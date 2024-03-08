<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;
use App\Nuc;
use App\Status;
use App\SixMonth_fund;
use App\Service;
use App\ServiceType;
use App\Coupon;
use Carbon\Carbon;
use DB;
use DateTime;

class ServicesController extends Controller
{
    public function index(){
        $services_types = ServiceType::pluck('name','id');
        $profile = User::findProfile();
        $perm = Permission::permView($profile,36);
        $perm_btn =Permission::permBtns($profile,36);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","36")
        ->pluck('name','id');
        $cmbStatusInt = Status::select('id','name')
        ->where("fk_section","31")
        ->pluck('name','id');
        $user = User::user_id();

        if($profile == 12)
        {
            $services = DB::select('call serviciosAgente(?)',[$user]);
        }
        else
        {
            $services = DB::select('call servicios()');
        }
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('process.services.services', compact('profile','perm_btn','services_types','cmbStatus','cmbStatusInt','services'));
        }
    }

    public function ReturnData($profile)
    {
        $user = User::user_id();
        if($profile == 12)
        {
            $services = DB::select('call serviciosAgente(?)',[$user]);
        }
        else
        {
            $services = DB::select('call servicios()');
        }
        return $services;
    }

    public function GetFunds($type)
    {
        if($type == "CP")
        {
            $nuc = DB::table('Nuc')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
            'nuc','Nuc.id as nucid')
                ->join('Client','Nuc.fk_client','=','Client.id')
                ->whereNull('Nuc.deleted_at')->get();
        }
        else
        {
            $nuc = DB::table('SixMonth_fund')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
            'nuc','SixMonth_fund.id as nucid')
                ->join('Client','SixMonth_fund.fk_client','=','Client.id')
                ->whereNull('SixMonth_fund.deleted_at')->get();
        }
        return response()->json(['status'=>true, "data"=>$nuc]);
    }

    public function GetInfo($id)
    {
        $service = Service::where('id',$id)->first();
        // dd($service);
        if($service->fund == "CP")
        {
            $serv = DB::table('Services')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
            'nuc','fk_nuc','Services.*')
                ->join('Nuc','Services.fk_nuc','=','Nuc.id')
                ->join('Client','Nuc.fk_client','=','Client.id')
                ->where('Services.id',$id)
                ->whereNull('Services.deleted_at')->first();
                // dd($serv);
        }
        else
        {
            $serv = DB::table('Services')->select(DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(Client.firstname, "")," ",IFNULL(Client.lastname, "")) AS cname'),
            'nuc','fk_nuc','Services.*')
                ->join('SixMonth_fund','Services.fk_nuc','=','SixMonth_fund.id')
                ->join('Client','SixMonth_fund.fk_client','=','Client.id')
                ->where('Services.id',$id)
                ->whereNull('SixMonth_fund.deleted_at')->first();
        }
        return response()->json(['status'=>true, "data"=>$serv]);
    }

    public function GetInfoStatus($id)
    {
        $service = Service::where('id',$id)->first();
        $profile = User::findProfile();
        return response()->json(['status'=>true, "data"=>$service, "profile" => $profile]);
    }

    public function store(Request $request)
    {
        $service = new Service;
        $service->fund = $request->fund;
        $service->fk_nuc = $request->fk_nuc;
        $service->fk_service_type = $request->fk_service_type;
        $service->type = $request->type;
        $service->folio = $request->folio;
        $service->amount = $request->amount;
        $service->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $services = $this->ReturnData($profile);

        return response()->json(["status"=>true, "message"=>"Servicio Creado", "services" => $services, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::where('id',$request->id)->update(['fund'=>$request->fund,'fk_nuc'=>$request->fk_nuc,'fk_service_type'=>$request->fk_service_type,
        'type'=>$request->type,'delivered'=>$request->delivered,'amount'=>$request->amount,'folio'=>$request->folio]);

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $services = $this->ReturnData($profile);

        return response()->json(['status'=>true, 'message'=>"Servicio Actualizado", "services" => $services, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function updateStatus(Request $request)
    {
        $status = Service::where('id',$request->id)->first();
        // dd($status);
        $status->fk_status = $request->fk_status;
        $status->paytime = $request->paytime;
        $status->attend_date = $request->attend_date;
        $status->pay_date = $request->pay_date;
        $status->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $services = $this->ReturnData($profile);

        return response()->json(['status'=>true, "message"=>"Estatus Actualizado", "services" => $services, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function updateStatusInt(Request $request)
    {
        $status = Service::where('id',$request->id)->first();
        // dd($status);
        $status->intern_status = $request->intern_status;
        $status->pick_status = $request->pick_status;
        $status->limit_status = $request->limit_status;
        $status->agent_status = $request->agent_status;
        $status->office_status = $request->office_status;
        $status->finestra_status = $request->finestra_status;
        $status->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $services = $this->ReturnData($profile);

        return response()->json(['status'=>true, "message"=>"Estatus Actualizado", "services" => $services, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        $service->delete();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $services = $this->ReturnData($profile);

        return response()->json(['status'=>true, "message"=>"Servicio eliminado", "services" => $services, "profile" => $profile, "permission" => $perm_btn]);
    }
}
