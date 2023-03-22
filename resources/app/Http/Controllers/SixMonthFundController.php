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
use App\SixMonth_fund;
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
        $nucs = DB::table('SixMonth_fund')
        ->select('*',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'),'nuc','SixMonth_fund.id as id')
        ->join('Client',"Client.id","=","fk_client")
        ->whereNull('SixMonth_fund.deleted_at')
        ->get();
        $cont = 0;
        foreach($nucs as $nuc)
        {
            $nucs[$cont]->amount = '$' . number_format($nuc->amount, 2);
            $cont++;
        }
        $clients = DB::table('Nuc')->select('Client.id',DB::raw('CONCAT(IFNULL(Client.name, "")," ",IFNULL(firstname, "")," ",IFNULL(lastname, "")) AS name'))
        ->join('Client',"Client.id","=","Nuc.fk_client")
        ->pluck('name','id');
        $perm = Permission::permView($profile,24);
        $perm_btn =Permission::permBtns($profile,24);
        $cmbStatus = Status::select('id','name')
        ->where("fk_section","23")
        ->pluck('name','id');
        // dd($clients);
        if($perm==0)
        {
            return redirect()->route('home');
        }
        else
        {
            return view('funds.sixmonthfund.sixmonthfund', compact('nucs','perm_btn','cmbStatus','clients'));
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
        $file = $request->file('excl');
        $imp = new MovesImport();
        $array = ($imp)->toArray($file);
        // dd($array[0][1]);
        $arrayNotFound = array();
        $cont = 0;
        $goodCont = 0;
        // dd($array);
        foreach ($array[0] as $moves)
        {
            $SixMonth_fund = SixMonth_fund::where('nuc',$moves[0])->first();
            // dd($SixMonth_fund);
            if($SixMonth_fund == null)
            {
                $this->SaveNucSixMonth($moves);
                $goodCont++;
            }
            else
            {
                $cont++;
                array_push($arrayNotFound,$moves[0]);
            }
        }
        // dd($cont,$arrayNotFound);
        return response()->json(['status'=>true, 'message'=>"Datos Subidos", 'repetidos' => $cont, 'notFnd' => $arrayNotFound, 'importados' => $goodCont]);
    }
    public function transformDate($value)
    {
        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format('Y-m-d');
    }
}
