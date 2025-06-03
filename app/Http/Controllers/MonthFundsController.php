<?php

namespace App\Http\Controllers;
Use PhpOffice\PhpSpreadsheet\Shared\Date;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthFund;
use App\Nuc;
use App\Insurance;
use App\Status;
use App\Paymentform;
use App\Charge;
use App\Application;
use App\SixMonth_fund;
use App\Exports\ExportFund;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MovesImport;
use Carbon\Carbon;
use DB;
use DateTime;

class MonthFundsController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $clients = DB::table('Client')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->whereNull('Client.deleted_at')
        ->orderBy('name')->pluck('name','id');
        $agents = DB::table('users')->select('users.id',DB::raw('CONCAT(IFNULL(users.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->whereNull('users.deleted_at')
        ->orderBy('name')->pluck('name','id');
        $perm = Permission::permView($profile,19);
        $perm_btn =Permission::permBtns($profile,19);
        $paymentForms = Paymentform::pluck('name','id');
        $applications = Application::pluck('name','id');
        $charges = Charge::pluck('name','id');
        $insurances = Insurance::orderBy('name')->where('fund_type','CP')->get();
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","19")
        ->pluck('name','id');
        $user = User::user_id();

        if($profile == 12)
        {
            $nucs = DB::select('call fondocpAgente(?,?)',[1,$user]);
            // dd($user);
        }
        else
        {
            $nucs = DB::select('call fondocp(?)',[1]);
        }

        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.monthfund.monthfunds', compact('nucs','perm_btn','cmbStatus','clients','paymentForms','applications','insurances','charges','agents'));
        }
    }

    public function ReturnData($profile,$active)
    {
        if($active == 0) $active = '%';
        $user = User::user_id();
        if($profile == 12)
        {
            $nucs = DB::select('call fondocpAgente(?,?)',[$active,$user]);
        }
        else
        {
            $nucs = DB::select('call fondocp(?)',[$active]);
        }
        return $nucs;
    }

    public function GetInfo($id)
    {
        $movimientos = DB::table('Month_fund')->select("*","Month_fund.id as id",DB::raw('IFNULL(auth_date, "-") as auth'))->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$id)->whereNull('Month_fund.deleted_at')->get();
        return response()->json(['status'=>true, "data"=>$movimientos]);
    }

    public function GetInfoLast($id)
    {
        $movimientos = DB::table('Month_fund')->select("new_balance")->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$id)->orderby("apply_date","DESC")->orderby("Month_fund.id","DESC")->whereNull('Month_fund.deleted_at')->first();
        return response()->json(['status'=>true, "data"=>$movimientos]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $fund = new MonthFund;
        $fund->fk_nuc = $request->fk_nuc;
        $fund->type = $request->type;
        $fund->amount = $request->amount;
        $fund->prev_balance = $request->prev_balance;
        $fund->new_balance = $request->new_balance;
        $fund->apply_date = $request->apply_date;
        $day = explode("-", $request->apply_date);
        $day = intval($day[2]);
        // dd($day);
        $fund->save();

        if($request->type == "Apertura")
        {
            $nuc = Nuc::where('id', $request->fk_nuc)->first();
            if($day <= 15)
            {
                $nuc->cut_date = 15;
            }
            else
            {
                $nuc->cut_date = 30;
            }
            $nuc->save();
        }

        $movimientos = DB::table('Month_fund')->select("*","Month_fund.id as id",DB::raw('IFNULL(auth_date, "-") as auth'))->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$request->fk_nuc)->whereNull('Month_fund.deleted_at')->get();
        return response()->json(["status"=>true, "message"=>"Movimiento Registrado", "data"=>$movimientos]);
    }
    public function destroy($id)
    {
        $fund = MonthFund::find($id);
        // dd($fund);
        $fk_nuc = $fund->fk_nuc;
        $fund->delete();
        $movimientos = DB::table('Month_fund')->select("*","Month_fund.id as id",DB::raw('IFNULL(auth_date, "-") as auth'))->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$fk_nuc)->whereNull('Month_fund.deleted_at')->get();

        return response()->json(['status'=>true, "message"=>"Movimiento eliminado", "data"=>$movimientos]);

    }
    public function updateStatus(Request $request)
    {
        $status = Nuc::where('id',$request->id)->first();
        // dd($status);
        $status->estatus = $request->status;
        $status->save();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $nucs = $this->ReturnData($profile,$request->active);

        return response()->json(['status'=>true, "message"=>"Estatus Actualizado", "nucs" => $nucs, "profile" => $profile, "permission" => $perm_btn]);
    }
    public function updateAuth(Request $request)
    {
        $auth = MonthFund::where('id',$request->id)->first();
        // dd($status);
        $auth->auth_date = $request->auth;
        $auth->save();
        $movimientos = DB::table('Month_fund')->select("*","Month_fund.id as id",DB::raw('IFNULL(auth_date, "-") as auth'))->join('Nuc',"Nuc.id","=","fk_nuc")->where('fk_nuc',$auth->fk_nuc)->whereNull('deleted_at')->get();
        return response()->json(["status"=>true, "message"=>"Movimiento Autorizado", "data"=>$movimientos]);
    }
    public function GetNuc($id)
    {
        $nuc = Nuc::where('id',$id)->first();
        return response()->json(["status"=>true, "data"=>$nuc]);
    }
    public function update(Request $request, $id)
    {
        // $nuc = Nuc::where('id',$request->id)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client,'fk_agent'=>$request->fk_agent,'fk_application'=>$request->fk_application,'fk_payment_form'=>$request->fk_payment_form,'fk_charge'=>$request->fk_charge,'fk_insurance'=>$request->fk_insurance,'active_stat'=>$request->active_stat]);
        $nuc = Nuc::where('id',$request->id)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client,'fk_agent'=>$request->fk_agent,'fk_application'=>$request->fk_application,'fk_payment_form'=>$request->fk_payment_form,'fk_insurance'=>$request->fk_insurance,'active_stat'=>$request->active_stat]);

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $nucs = $this->ReturnData($profile,$request->active);

        return response()->json(['status'=>true, 'message'=>"Nuc Actualizado", "nucs" => $nucs, "profile" => $profile, "permission" => $perm_btn]);
    }
    public function ExportFunds($id)
    {
        // dd($id);
        $nuc = DB::table('Nuc')->select('nuc',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))->join('Client',"Nuc.fk_client","=","Client.id")->where('Nuc.id',$id)->first();
        $nombre = "NUC_".(string)$nuc->nuc."_".$nuc->name.".xlsx";
        return Excel::download(new ExportFund($id),$nombre);
    }
    public function import(Request $request)
    {
        set_time_limit(1000);
        $file = $request->file('excl');
        // $file = $request->file;
        $imp = new MovesImport();
        $new_balance = 0;
        $prev_balance = 0;
        // dd($request);
        // Excel::import($imp, $file);
        $array = ($imp)->toArray($file);
        // dd($array[0][1]);
        $array2 = array();
        $arrayNotFound = array();
        $cont = 0;
        $goodCont = 0;
        // dd($array);
        foreach ($array[0] as $moves)
        {
            $moves[5] = $this->transformDate($moves[5]);
            $moves[6] = $this->transformDate($moves[6]);
            if($moves[7] != null)
                $moves[7] = $this->transformDate($moves[7]);
            else
                $moves[7] = null;
            // dd($moves);
            $movimientos = DB::table('Month_fund')->select("new_balance", "amount", "apply_date", "fk_nuc", "movementid")->join('Nuc',"Nuc.id","=","fk_nuc")->where('nuc',$moves[1])->orderby("apply_date","DESC")->orderby("Month_fund.id","DESC")->whereNull('Month_fund.deleted_at')->first();
            // dd($movimientos,$moves);
            if ($movimientos != null)
            {
                $mov = DB::table('Month_fund')->select("movementid")->where('movementid',$moves[0])->whereNull('Month_fund.deleted_at')->first();
                if($mov != null)
                {
                    // dd("repetido");
                    $cont++;
                }
                else
                {
                    // dd($moves[2]);
                    $prev_balance = floatval($movimientos->new_balance);
                    if($moves[2] == "Abono" || $moves[2] == "Reinversion")
                    {
                        $new_balance = $moves[4] + $prev_balance;
                    }
                    else if($moves[2] == "Retiro parcial")
                    {
                        $new_balance = $prev_balance - $moves[4];
                        // dd($moves[2]);
                    }
                    else if ($moves[2] == "Retiro Total")
                    {
                        $new_balance = 0;
                        // dd($moves[2]);
                    }
                    else
                    {
                        $new_balance = $moves[4] + $prev_balance;
                    }
                    if($moves[3] == null) $moves[3] = 1;
                    $fund = new MonthFund;
                    $fund->movementid = $moves[0];
                    $fund->fk_nuc = $movimientos->fk_nuc;
                    $fund->type = $moves[2];
                    $fund->amount = $moves[4];
                    $fund->prev_balance = $prev_balance;
                    $fund->new_balance = $new_balance;
                    $fund->apply_date = $moves[6];
                    $fund->auth_date = $moves[5];
                    $fund->pay_date = $moves[7];
                    $fund->fk_charge = $moves[3];
                    $fund->save();
                    $goodCont++;

                    if($request->type == "Apertura")
                    {
                        $day = explode("-", $moves[6]);
                        $day = intval($day[2]);
                        $apr = Nuc::where('id', $movimientos->fk_nuc)->first();
                        if($day <= 15)
                        {
                            $apr->cut_date = 15;
                        }
                        else
                        {
                            $apr->cut_date = 30;
                        }
                        $apr->save();
                    }
                }
            }
            else
            {
                $nuc = DB::table('Nuc')->select("id")->where('nuc',$moves[1])->first();
                // dd($nuc);
                if($nuc == null)
                {
                    array_push($arrayNotFound, $moves[1]);
                }
                else
                {
                    if($moves[3] == null) $moves[3] = 1;
                    $prev_balance = 0;
                    $new_balance = $moves[4];
                    $fund = new MonthFund;
                    $fund->movementid = $moves[0];
                    $fund->fk_nuc = $nuc->id;
                    $fund->type = $moves[2];
                    $fund->amount = $moves[4];
                    $fund->prev_balance = $prev_balance;
                    $fund->new_balance = $new_balance;
                    $fund->apply_date = $moves[6];
                    $fund->auth_date = $moves[5];
                    $fund->pay_date = $moves[7];
                    $fund->fk_charge = $moves[3];
                    $fund->save();
                    $goodCont++;
                    $day = explode("-", $moves[6]);
                    $day = intval($day[2]);
                    // dd($day);
                    $fund->save();

                    if($moves[2] == "Apertura")
                    {
                        $apr = Nuc::where('id', $nuc->id)->first();
                        if($day <= 15)
                        {
                            $apr->cut_date = 15;
                        }
                        else
                        {
                            $apr->cut_date = 30;
                        }
                        $apr->save();
                    }

                }
            }
        }
        // dd($cont,$arrayNotFound);
        return response()->json(['status'=>true, 'message'=>"Datos Subidos", 'repetidos' => $cont, 'notFnd' => $arrayNotFound, 'importados' => $goodCont]);
    }
    public function transformDate($value)
    {
        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
    }
    public function updateFund()
    {
        // $nucs = DB::table('Nuc')->select('Nuc.id as nucid', 'Client.fk_agent as agnt')
        //     ->join('Client',"fk_client","=","Client.id")
        //     ->whereNull('Nuc.deleted_at')->get();


        // foreach($nucs as $nuc)
        // {
        //     $nuc = Nuc::where('id',$nuc->nucid)->update(['fk_agent'=>$nuc->agnt]);
        // }

        // $nucs = DB::table('Nuc')->select('Nuc.id as nucid')
        //     ->join('Month_fund','fk_nuc',"=",'Nuc.id')
        //     ->groupBy('fk_nuc')
        //     ->where('type','=',"Retiro total")
        //     ->whereNull('Nuc.deleted_at')->get();

        // foreach($nucs as $nuc)
        // {
        //     $nuc = Nuc::where('id',$nuc->nucid)->update(['active_stat'=>0]);
        // }

        // $nucs = DB::table('SixMonth_fund')->select('SixMonth_fund.id as nucid', 'Client.fk_agent as agnt')
        //     ->join('Client',"fk_client","=","Client.id")
        //     ->whereNull('SixMonth_fund.deleted_at')->get();

        // foreach($nucs as $nuc)
        // {
        //     $nuc = SixMonth_fund::where('id',$nuc->nucid)->update(['fk_agent'=>$nuc->agnt]);
        // }

        // date_default_timezone_set('America/Mexico_City');
        // $date2 = new DateTime();

        // $nucs = DB::table('SixMonth_fund')->select('end_date','id')
        //     ->where('end_date','<',$date2)
        //     ->where('active_stat','!=',0)
        //     ->whereNull('SixMonth_fund.deleted_at')->get();

        // foreach($nucs as $nuc)
        // {
        //     $nuc = SixMonth_fund::where('id',$nuc->id)->update(['active_stat'=>0]);
        // }

        $nucs = DB::table('Nuc')->select('Nuc.id as nid','apply_date')
            ->join('Month_fund',"fk_nuc","=","Nuc.id")
            ->where('type','Apertura')
            ->whereNull('cut_date')
            ->whereNull('Nuc.deleted_at')
            ->groupBy('nuc')->get();
        // dd($nucs);
        foreach($nucs as $nuc)
        {
            $dia = explode("-", $nuc->apply_date);
            $day = intval($dia[2]);
            // dd($nuc->apply_date,$dia[2]);
            $apr = Nuc::where('id', $nuc->nid)->first();
            if($day <= 15)
            {
                $apr->cut_date = 15;
            }
            else
            {
                $apr->cut_date = 30;
            }
            $apr->save();
        }

        return response()->json(['status'=>true, 'message'=>"Datos Actualizados"]);
    }
    public function updateFundNet()
    {
        $nucs = DB::table('Month_fund')->select('Nuc.id', 'month_flag', 'apply_date','nuc')
            ->join('Nuc',"fk_nuc","=","Nuc.id")
            ->groupBy('fk_nuc')
            ->orderBy('apply_date', 'asc')
            ->where('type','=',"Apertura")
            ->where('month_flag','<',8)
            ->whereNull('Nuc.deleted_at')->get();
        // dd($nucs);

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        $date2 = $date2->format('Y-m-t');
        $date2 = new DateTime($date2);
        // dd($date2);
        foreach ($nucs as $nuc)
        {
            $date1 = new DateTime($nuc->apply_date);
            $diff = $date1->diff($date2);

            if($diff->m >= 8 || $diff->y >= 1)
                $nc = Nuc::where('id',$nuc->id)->update(['month_flag'=>8]);
            else
                $nc = Nuc::where('id',$nuc->id)->update(['month_flag'=>$diff->m]);
        }

        // $nucs = DB::table('SixMonth_fund')->select('end_date','id')
        //     ->where('end_date','<',$date2)
        //     ->where('active_stat','!=',0)
        //     ->whereNull('SixMonth_fund.deleted_at')->get();

        // foreach($nucs as $nuc)
        // {
        //     $nuc = SixMonth_fund::where('id',$nuc->id)->update(['active_stat'=>0]);
        // }

        $nucs = DB::table('Nuc')->select('Nuc.id as nucid')
            ->join('Month_fund','fk_nuc',"=",'Nuc.id')
            ->groupBy('fk_nuc')
            ->where('type','=',"Retiro total")
            ->whereNull('Nuc.deleted_at')->get();

        foreach($nucs as $nuc)
        {
            $nuc = Nuc::where('id',$nuc->nucid)->update(['active_stat'=>0]);
        }
        return response()->json(['status'=>true, 'message'=>"Datos Actualizados"]);
    }
    public function updateusersNet()
    {
        $users = User::get();

        foreach($users as $user)
        {
            $user = User::where('id',$user->id)->update(['invoice_flag'=>null,'pay_flag'=>null]);
        }
        return response()->json(['status'=>true, 'message'=>"Datos Actualizados"]);
    }
    public function deleteFund(Request $request)
    {
        $fund = Nuc::find($request->id);
        $fund->delete();

        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $nucs = $this->ReturnData($profile,$request->active);

        return response()->json(['status'=>true, "message"=>"Fondo eliminado", "nucs" => $nucs, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function GetCP($active)
    {
        // dd("entre");
        $profile = User::findProfile();
        $perm_btn =Permission::permBtns($profile,31);
        $nucs = $this->ReturnData($profile,$active);

        return response()->json(['status'=>true, "nucs" => $nucs, "profile" => $profile, "permission" => $perm_btn]);
    }

    public function GetCharge($id)
    {
        $charge =  MonthFund::where('id',$id)->first();

        return response()->json(['status'=>true, "charge" => $charge->fk_charge]);
    }

    public function updateCharge(Request $request)
    {
        $charge = MonthFund::where('id',$request->id)->first();
        // dd($status);
        $charge->fk_charge = $request->charge;
        $charge->save();

        return response()->json(['status'=>true, "message"=>"Conducto Actualizado"]);
    }
}
