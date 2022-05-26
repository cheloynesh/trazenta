<?php

namespace App\Imports;

use App\MonthFund;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

class MovesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;

    public function model(array $row)
    {
        return new MonthFund([
            'nuc'=> $row[0],
            'type'=> $row[1],
            'amount'=> $row[2],
            'fecha_creacion'=> $row[3],
            'auth_date'=> $row[4],
            'apply_date'=> $row[5],
            'pay_date'=> $row[6]
        ]);
    }
}
