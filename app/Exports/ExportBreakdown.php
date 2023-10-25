<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use DB;

class ExportBreakdown implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $id;
    protected $month;
    protected $quarter;
    protected $year;
    protected $fund;
    protected $type;

    public function __construct($id,$month,$quarter,$year,$fund,$type)
    {
        $this->id = $id;
        $this->year = $year;
        $this->type = $type;
        if($month == 'a') $this->month = '%'; else $this->month = $month;
        if($quarter == 'a') $this->quarter = '%'; else $this->quarter = $quarter;
        if($fund == 'a') $this->fund = '%'; else $this->quart = $quart;
    }
    public function collection()
    {
        switch($this->type)
        {
            case 1: $movimientos = DB::select('call exportNewCP(?,?,?,?,?)',[$this->id,$this->year,$this->month,$this->quarter,$this->fund]); break;
            case 2: $movimientos = DB::select('call exportNewLP(?,?,?,?,?)',[$this->id,$this->year,$this->month,$this->quarter,$this->fund]); break;
            case 3: $movimientos = DB::select('call exportOutCP(?,?,?,?,?)',[$this->id,$this->year,$this->month,$this->quarter,$this->fund]); break;
        }
        return new Collection($movimientos);
    }
    public function headings(): array
    {
        return ["ID","Cliente","Contraro","Fecha de aplicación","Moneda","Monto","Tipo"];
    }
}
