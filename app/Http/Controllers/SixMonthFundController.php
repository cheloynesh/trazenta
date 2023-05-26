<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Client;
use App\Permission;
use App\MonthFund;
use App\Nuc;
use App\Status;
use App\Coupon;
use App\Paymentform;
use App\Application;
use App\Charge;
use App\SixMonth_fund;
use App\Insurance;
use App\Exports\ExportFund;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MovesImport;
use Carbon\Carbon;
use Datetime;
use DB;

class SixMonthFundController extends Controller
{
    public function index(){
        $profile = User::findProfile();
        $cont = 0;
        $clients = DB::table('Client')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->orderBy('name')->pluck('name','id');
        $perm = Permission::permView($profile,24);
        $perm_btn =Permission::permBtns($profile,24);
        $paymentForms = Paymentform::pluck('name','id');
        $charges = Charge::pluck('name','id');
        $applications = Application::pluck('name','id');
        $insurances = Insurance::orderBy('name')->where('fund_type','LP')->get();
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","23")
        ->pluck('name','id');
        $user = User::user_id();

        if($profile == 12)
        {
            $nucs = DB::table('SixMonth_fund')
                ->select('*',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),'nuc','SixMonth_fund.id as id',DB::raw('CONCAT("$", FORMAT(amount, 2)) AS amount'))
                ->join('Client',"Client.id","=","fk_client")
                ->where('fk_agent',$user)
                ->whereNull('SixMonth_fund.deleted_at')
                ->get();
        }
        else
        {
            $nucs = DB::table('SixMonth_fund')
                ->select('*',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),'nuc','SixMonth_fund.id as id',DB::raw('CONCAT("$", FORMAT(amount, 2)) AS amount'))
                ->join('Client',"Client.id","=","fk_client")
                ->whereNull('SixMonth_fund.deleted_at')
                ->get();
        }
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.sixmonthfund.sixmonthfund', compact('nucs','perm_btn','cmbStatus','clients','paymentForms','applications','insurances','charges'));
        }
    }
    public function GetInfo($id)
    {
        $nuc = Coupon::where('fk_nuc',$id)->whereNull('deleted_at')->get();
        return response()->json(['status'=>true, "data"=>$nuc]);
    }
    public function destroy($id)
    {
        $SixMonth_fund = SixMonth_fund::find($id);
        $SixMonth_fund->delete();
        return response()->json(['status'=>true, "message"=>"Fondo eliminado"]);
    }
    public function SaveNucSixMonth($moves)
    {
        $deposit_date = new DateTime($this->transformDate($moves[4]));
        $initial_date = new DateTime($deposit_date->format('Y')."-".$deposit_date->format('m')."-01");
        if(intval($deposit_date->format('d')) <= 10)
        {
            $initial_date->modify('+2 month');
        }
        else
        {
            $initial_date->modify('+3 month');
        }

        $end_date = clone $initial_date;
        $end_date->modify('+2 year');

        $nuc = new SixMonth_fund;
        $nuc->nuc = $moves[0];
        $nuc->currency = $moves[3];
        $nuc->amount = $moves[2];
        $nuc->fk_client = $moves[1];
        $nuc->deposit_date = $deposit_date;
        $nuc->initial_date = $initial_date;
        $nuc->end_date = $end_date;
        $nuc->save();

        $nuc->id;

        $date1 = clone $initial_date;
        $date2= clone $deposit_date;
        $number = 1;
        if($nuc->currency == "MXN")
        {
            for($cont = 0; $cont < 24; $cont += 2)
            {
                $coupon = new Coupon;
                $coupon->number = $number;
                if($cont != 0) $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                $coupon->amount = intval($diff) * 256.94443 * ($nuc->amount/500000);
                // dd($coupon->amount);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $nuc->id;
                $coupon->save();
                $number++;
                $date2= clone $date1;
            }
        }
        else
        {
            for($cont = 0; $cont < 24; $cont += 2)
            {
                $coupon = new Coupon;
                $coupon->number = $number;
                if($cont != 0) $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                $coupon->amount = intval($diff) * 10.06943 * ($nuc->amount/25000);
                // dd($coupon->amount);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $nuc->id;
                $coupon->save();
                $number++;
                $date2= clone $date1;
            }
        }
    }
    public function import(Request $request)
    {
        set_time_limit(1000);
        // $file = $request->file('excl');
        // $imp = new MovesImport();
        // $array = ($imp)->toArray($file);
        // // dd($array[0][1]);
        // $arrayNotFound = array();
        // $cont = 0;
        // $goodCont = 0;
        // // dd($array);
        // foreach ($array[0] as $moves)
        // {
        //     $SixMonth_fund = SixMonth_fund::where('nuc',$moves[0])->first();
        //     // dd($SixMonth_fund);
        //     if($SixMonth_fund == null)
        //     {
        //         $this->SaveNucSixMonth($moves);
        //         $goodCont++;
        //     }
        //     else
        //     {
        //         $cont++;
        //         array_push($arrayNotFound,$moves[0]);
        //     }
        // }
        // dd($cont,$arrayNotFound);
        $nucs = DB::table('SixMonth_fund')->select('*','SixMonth_fund.id as id')
            ->join('Insurance','Insurance.id','=','fk_insurance')
            ->whereNull('SixMonth_fund.deleted_at')->get();
        foreach($nucs as $nuc)
        {
            $deposit_date = new DateTime($nuc->deposit_date);
            $initial_date = new DateTime($deposit_date->format('Y')."-".$deposit_date->format('m')."-01");
            $initial = clone $initial_date;
            $initialflag = 0;
            $initialdiff = 0;

            if(intval($deposit_date->format('d')) <= 10)
            {
                $initial_date->modify('+2 month');
                $initialflag = 0;
                $initialdiff = $deposit_date->diff($initial)->days;
            }
            else
            {
                $initial_date->modify('+3 month');
                $initialflag = 1;
                $initial->modify('+1 month');
                $initialdiff = $deposit_date->diff($initial)->days;
            }
            // dd($initialdiff);

            $end_date = clone $deposit_date;
            $end_date->modify('+2 year');


            // dd($end_date);

            $nucEdit = SixMonth_fund::where('id',$nuc->id)->update(['end_date'=>$end_date]);

            // if(floatval($request->amount) != floatval($nuc->amount) || $request->currency != $nuc->currency || $request->deposit_date != $nuc->deposit_date)
            // {
                $coupons = Coupon::where("fk_nuc",$nuc->id)->get();
                foreach($coupons as $coup)
                {
                    $coup->delete();
                }

                $date1 = clone $initial_date;
                $date2= clone $deposit_date;
                $number = 1;
                for($cont = 0; $cont < 22; $cont += 2)
                {
                    $coupon = new Coupon;
                    $coupon->number = $number;
                    if($cont != 0) $date1->modify('+2 month');
                    $diff = $date1->diff($date2)->days;
                    if($nuc->currency == "MXN")
                    {
                        $coupon->amount = intval($diff) * (($nuc->amount * ($nuc->yield/100))/360);
                    }
                    else
                    {
                        $coupon->amount = intval($diff) * (($nuc->amount * ($nuc->yield_usd/100))/360);
                    }
                    $coupon->pay_date = $date1;
                    $coupon->fk_nuc = $nuc->id;
                    $coupon->save();
                    $number++;
                    $date2= clone $date1;
                }
                $coupon = new Coupon;
                $coupon->number = $number;
                $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                if($nuc->currency == "MXN")
                {
                    $coupon->amount = intval($diff) * (($nuc->amount * ($nuc->yield/100))/360);
                    if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($nuc->yield/100))/360);
                    else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($nuc->yield/100))/360);
                }
                else
                {
                    $coupon->amount = intval($diff) * (($nuc->amount * ($nuc->yield_usd/100))/360);
                    if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($nuc->amount * ($nuc->yield_usd/100))/360);
                    else $coupon->amount -= intval($initialdiff) * (($nuc->amount * ($nuc->yield_usd/100))/360);
                }
                // dd($coupon->amount);
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $nuc->id;
                $coupon->save();
            // }
        }


        return response()->json(['status'=>true, 'message'=>"Datos Subidos", 'repetidos' => $cont, 'notFnd' => $arrayNotFound, 'importados' => $goodCont]);
    }
    public function transformDate($value)
    {
        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
    }
    public function GetNuc($id)
    {
        $nuc = SixMonth_fund::where('id',$id)->first();
        return response()->json(["status"=>true, "data"=>$nuc]);
    }
    public function update(Request $request, $id)
    {
        $deposit_date = new DateTime($request->deposit_date);
        $initial_date = new DateTime($deposit_date->format('Y')."-".$deposit_date->format('m')."-01");
        $initial = clone $initial_date;
        $initialflag = 0;
        $initialdiff = 0;

        if(intval($deposit_date->format('d')) <= 10)
        {
            $initial_date->modify('+2 month');
            $initialflag = 0;
            $initialdiff = $deposit_date->diff($initial)->days;
        }
        else
        {
            $initial_date->modify('+3 month');
            $initialflag = 1;
            $initial->modify('+1 month');
            $initialdiff = $deposit_date->diff($initial)->days;
        }

        $end_date = clone $deposit_date;
        $end_date->modify('+2 year');

        $nuc = DB::table('SixMonth_fund')->select('*')
            ->join('Insurance','Insurance.id','=','fk_insurance')
            ->where('SixMonth_fund.id',$request->id)->first();

        // dd($end_date);

        $nucEdit = SixMonth_fund::where('id',$request->id)->update(['nuc'=>$request->nuc,'fk_client'=>$request->fk_client, 'amount'=>$request->amount,'currency'=>$request->currency,
        'deposit_date'=>$deposit_date,'initial_date'=>$initial_date,'end_date'=>$end_date, 'fk_application'=>$request->fk_application,'fk_payment_form'=>$request->fk_payment_form,'fk_charge'=>$request->fk_charge,'fk_insurance'=>$request->fk_insurance]);

        if(floatval($request->amount) != floatval($nuc->amount) || $request->currency != $nuc->currency || $request->deposit_date != $nuc->deposit_date)
        {
            $coupons = Coupon::where("fk_nuc",$request->id)->get();
            foreach($coupons as $coup)
            {
                $coup->delete();
            }

            $date1 = clone $initial_date;
            $date2= clone $deposit_date;
            $number = 1;
            for($cont = 0; $cont < 22; $cont += 2)
            {
                $coupon = new Coupon;
                $coupon->number = $number;
                if($cont != 0) $date1->modify('+2 month');
                $diff = $date1->diff($date2)->days;
                if($request->currency == "MXN")
                {
                    $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield/100))/360);
                }
                else
                {
                    $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield_usd/100))/360);
                }
                $coupon->pay_date = $date1;
                $coupon->fk_nuc = $request->id;
                $coupon->save();
                $number++;
                $date2= clone $date1;
            }
            $coupon = new Coupon;
            $coupon->number = $number;
            $date1->modify('+2 month');
            $diff = $date1->diff($date2)->days;
            if($request->currency == "MXN")
            {
                $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield/100))/360);
                if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($request->amount * ($nuc->yield/100))/360);
                else $coupon->amount -= intval($initialdiff) * (($request->amount * ($nuc->yield/100))/360);
            }
            else
            {
                $coupon->amount = intval($diff) * (($request->amount * ($nuc->yield_usd/100))/360);
                if($initialflag == 0) $coupon->amount += intval($initialdiff) * (($request->amount * ($nuc->yield_usd/100))/360);
                else $coupon->amount -= intval($initialdiff) * (($request->amount * ($nuc->yield_usd/100))/360);
            }
            // dd($coupon->amount);
            $coupon->pay_date = $date1;
            $coupon->fk_nuc = $request->id;
            $coupon->save();
        }

        return response()->json(['status'=>true, 'message'=>"Nuc Actualizado"]);
    }
}
