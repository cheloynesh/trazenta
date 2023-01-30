<?php

namespace App\Http\Controllers;
Use PhpOffice\PhpSpreadsheet\Shared\Date;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthFund;
use App\Nuc;
use App\Status;
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
        $nucs = DB::table('Nuc')
        ->select('Nuc.id as id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),"nuc",'Status.id as statId','Status.name as estatus','color')
        ->join('Client',"Client.id","=","Nuc.fk_client")
        ->join('Status',"Nuc.estatus","=","Status.id")
        ->whereNull('Client.deleted_at')
        ->get();
        $clients = DB::table('Nuc')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->join('Client',"Client.id","=","Nuc.fk_client")
        ->whereNull('Client.deleted_at')
        ->pluck('name','id');
        $perm = Permission::permView($profile,19);
        $perm_btn =Permission::permBtns($profile,19);
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
            return view('funds.monthfund.monthfunds', compact('nucs','perm_btn','cmbStatus','clients'));
        }
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
        return response()->json(['status'=>true, "message"=>"Estatus Actualizado"]);
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
        $nuc = Nuc::where('id',$request->id)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client]);
        return response()->json(['status'=>true, 'message'=>"Nuc Actualizado"]);
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
            $moves[4] = $this->transformDate($moves[4]);
            $moves[5] = $this->transformDate($moves[5]);
            if($moves[6] != null)
                $moves[6] = $this->transformDate($moves[6]);
            else
                $moves[6] = null;
            // dd($moves);
            $movimientos = DB::table('Month_fund')->select("new_balance", "amount", "apply_date", "fk_nuc")->join('Nuc',"Nuc.id","=","fk_nuc")->where('nuc',$moves[1])->orderby("apply_date","DESC")->orderby("Month_fund.id","DESC")->whereNull('Month_fund.deleted_at')->first();
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
                        $new_balance = $moves[3] + $prev_balance;
                    }
                    else if($moves[2] == "Retiro parcial")
                    {
                        $new_balance = $prev_balance - $moves[3];
                        // dd($moves[2]);
                    }
                    else if ($moves[2] == "Retiro Total")
                    {
                        $new_balance = 0;
                        // dd($moves[2]);
                    }
                    else
                    {
                        $new_balance = $moves[3] + $prev_balance;
                    }
                    $fund = new MonthFund;
                    $fund->movementid = $moves[0];
                    $fund->fk_nuc = $movimientos->fk_nuc;
                    $fund->type = $moves[2];
                    $fund->amount = $moves[3];
                    $fund->prev_balance = $prev_balance;
                    $fund->new_balance = $new_balance;
                    $fund->apply_date = $moves[5];
                    $fund->auth_date = $moves[4];
                    $fund->pay_date = $moves[6];
                    $fund->save();
                    $goodCont++;
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
                    $prev_balance = 0;
                    $new_balance = $moves[3];
                    $fund = new MonthFund;
                    $fund->movementid = $moves[0];
                    $fund->fk_nuc = $nuc->id;
                    $fund->type = $moves[2];
                    $fund->amount = $moves[3];
                    $fund->prev_balance = $prev_balance;
                    $fund->new_balance = $new_balance;
                    $fund->apply_date = $moves[5];
                    $fund->auth_date = $moves[4];
                    $fund->pay_date = $moves[6];
                    $fund->save();
                    $goodCont++;
                    $day = explode("-", $moves[5]);
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
        $nucs = DB::table('Month_fund')->select('Nuc.id', 'month_flag', 'apply_date','nuc')
            ->join('Nuc',"fk_nuc","=","Nuc.id")
            ->groupBy('fk_nuc')
            ->orderBy('apply_date', 'asc')
            ->where('type','=',"Apertura")
            ->where('month_flag','<',7)
            ->whereNull('Nuc.deleted_at')->get();

        date_default_timezone_set('America/Mexico_City');
        $date2 = new DateTime();
        // dd($nucs);
        foreach ($nucs as $nuc)
        {
            $date1 = new DateTime($nuc->apply_date);
            $diff = $date1->diff($date2);
            if($diff->m >= 7 || $diff->y >= 1)
                $nc = Nuc::where('id',$nuc->id)->update(['month_flag'=>7]);
            else
                $nc = Nuc::where('id',$nuc->id)->update(['month_flag'=>$diff->m]);
        }
        return response()->json(['status'=>true, 'message'=>"Datos Actualizados"]);
    }
}
