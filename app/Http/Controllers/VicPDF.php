<?php

namespace App\Http\Controllers;

use Codedge\Fpdf\Fpdf\Fpdf;

class VicPDF extends FPDF
{
    var $month;
    // Tabla coloreada
    function Header()
    {
        // Logo
        $this->Image($_SERVER["DOCUMENT_ROOT"].'/public/img/logo.png',10,8,33);
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(30);
        // Título
        $this->Cell(245,30,$this->month,0,0,'C');
        // Salto de línea
        $this->Ln(20);
    }

    function FancyTable($sum,$tc,$total)
    {
        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        // Cabecera
        $this->SetY(40);
        $this->MultiCell(275,8,utf8_decode("Comisiónes Netas pendientes por pagar Vic INCLUYENDO TODOS LOS AGENTES Y CLIENTES DE VIC"),1,'C',true);
        $this->SetY(48);
        $this->MultiCell(57,8,"Fondo",1,'C',true);
        $this->SetY(48);
        $this->SetX(57);
        $this->MultiCell(57,8,"Monto Bruto USD",1,'C',true);
        $this->SetY(48);
        $this->SetX(114);
        $this->MultiCell(57,8,"Tipo de Cambio",1,'C',true);
        $this->SetY(48);
        $this->SetX(171);
        $this->MultiCell(57,8,"Monto Bruto MXN",1,'C',true);
        $this->SetY(48);
        $this->SetX(228);
        $this->MultiCell(57,8,"Monto Neto",1,'C',true);

        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);
        $this->SetY(56);
        $this->MultiCell(57,8,"CAPORTA",1,'C',true);
        $this->SetY(56);
        $this->SetX(57);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $sum)), 2, ".", ","),1,'C',true);
        $this->SetY(56);
        $this->SetX(114);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $tc)), 2, ".", ","),1,'C',true);
        $this->SetY(56);
        $this->SetX(171);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $sum*$tc)), 2, ".", ","),1,'C',true);
        $this->SetY(56);
        $this->SetX(228);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $sum*$tc*.9)), 2, ".", ","),1,'C',true);

        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        // Cabecera
        $this->SetY(72);
        $this->MultiCell(275,8,utf8_decode("Comisiónes OBDENA y OBCREDA"),1,'C',true);

        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);
        $this->SetY(80);
        $this->MultiCell(142,8,"TOTAL",1,'C',true);
        $this->SetY(80);
        $this->SetX(142);
        $this->MultiCell(143,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $total)), 2, ".", ","),1,'C',true);

        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        // Cabecera
        $this->SetY(96);
        $this->MultiCell(142,8,utf8_decode("Total ambos Fondos"),1,'C',true);
        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);
        $this->SetY(96);
        $this->SetX(142);
        $this->MultiCell(143,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $sum*$tc*.9 + $total)), 2, ".", ","),1,'C',true);

    }

    function CPTable($sum,$tc,$data)
    {
        $this->AddPage('L');
        $ycurr = 0;
        $yprev = 0;
        // Colores, ancho de línea y fuente en negrita

        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        $this->SetY(40);
        $this->MultiCell(218,8,utf8_decode("DESGLOSE CLIENTES VIC"),1,'C',true);
        $this->SetY(48);
        $this->MultiCell(114,8,"Cliente",1,'C',true);
        $this->SetY(48);
        $this->SetX(114);
        $this->MultiCell(57,8,"Monto Bruto",1,'C',true);
        $this->SetY(48);
        $this->SetX(171);
        $this->MultiCell(57,8,"Monto Neto",1,'C',true);
        $yprev = $this->GetY();

        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);

        $usd_inv = 0;
        $ten = 0;

        foreach($data as $row)
        {
            // dd($row['nuc']);
            if($ycurr > 180)
            {
                $this->AddPage('L');
                $yprev = 40;
            }
            $this->SetY($yprev);
            $this->MultiCell(114,8,utf8_decode($row['nuc']." ".$row['cname']),1,'L',true);
            $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(114);
            $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['usd_invest1'])), 2, ".", ","),1,'R',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(171);
            $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['ten'])), 2, ".", ","),1,'R',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $yprev = $ycurr;
            $this->Ln();
            $usd_inv += $row['usd_invest1'];
            $ten += $row['ten'];
        }

        $this->SetY($yprev);
        $this->MultiCell(114,8,utf8_decode("Total"),1,'L',true);
        $ycurr = $this->GetY();
        $this->SetY($yprev);
        $this->SetX(114);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $usd_inv)), 2, ".", ","),1,'R',true);
        if($ycurr < $this->GetY()) $ycurr = $this->GetY();
        $this->SetY($yprev);
        $this->SetX(171);
        $this->MultiCell(57,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $ten)), 2, ".", ","),1,'R',true);
        if($ycurr < $this->GetY()) $ycurr = $this->GetY();
        $this->SetY($yprev);
        $yprev = $ycurr;
        $this->Ln();
    }

    function LPTable($data,$secData,$total)
    {
        $this->AddPage('L');
        $ycurr = 0;
        $yprev = 0;

        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        $this->SetY(40);
        $this->MultiCell(275,8,utf8_decode("Primer Año"),1,'C',true);
        $this->SetY(48);
        $this->MultiCell(50,8,utf8_decode("Obligación"),1,'C',true);
        $this->SetY(48);
        $this->SetX(50);
        $this->MultiCell(20,8,utf8_decode("Apertura"),1,'C',true);
        $this->SetY(48);
        $this->SetX(70);
        $this->MultiCell(77,8,utf8_decode("Agente"),1,'C',true);
        $this->SetY(48);
        $this->SetX(147);
        $this->MultiCell(78,8,utf8_decode("Cliente"),1,'C',true);
        $this->SetY(48);
        $this->SetX(225);
        $this->MultiCell(30,8,utf8_decode("Monto"),1,'C',true);
        $this->SetY(48);
        $this->SetX(255);
        $this->MultiCell(30,8,utf8_decode("Comisión"),1,'C',true);
        $yprev = $this->GetY();

        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);

        $usd_inv = 0;
        $ten = 0;

        foreach($data as $row)
        {
            // dd($row['nuc']);
            if($ycurr > 180)
            {
                $this->AddPage('L');
                $yprev = 40;
            }
            $this->SetY($yprev);
            $this->MultiCell(50,8,utf8_decode($row['nuc']),1,'L',true);
            $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(50);
            $this->MultiCell(20,8,utf8_decode($row['pay_date']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(70);
            $this->MultiCell(77,8,utf8_decode($row['usname']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(147);
            $this->MultiCell(78,8,utf8_decode($row['clname']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(225);
            $this->MultiCell(30,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['amount'])), 2, ".", ",")." ".utf8_decode($row['currency']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(255);
            $this->MultiCell(30,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['gross_amount'])), 2, ".", ","),1,'C',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $yprev = $ycurr;
            $this->Ln();
        }
        if($ycurr > 180)
        {
            $this->AddPage('L');
            $yprev = 40;
        }

        $yprev += 8;
        $this->SetY($yprev);
        $this->Ln();

        $this->SetFillColor(16,106,106);
        $this->SetTextColor(255);
        $this->SetDrawColor(0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial','',8);
        $this->SetY($yprev);
        $this->MultiCell(275,8,utf8_decode("Segundo Año"),1,'C',true);
        $yprev += 8;
        $this->SetY($yprev);
        $this->MultiCell(50,8,utf8_decode("Obligación"),1,'C',true);
        $this->SetY($yprev);
        $this->SetX(50);
        $this->MultiCell(20,8,utf8_decode("Apertura"),1,'C',true);
        $this->SetY($yprev);
        $this->SetX(70);
        $this->MultiCell(77,8,utf8_decode("Agente"),1,'C',true);
        $this->SetY($yprev);
        $this->SetX(147);
        $this->MultiCell(78,8,utf8_decode("Cliente"),1,'C',true);
        $this->SetY($yprev);
        $this->SetX(225);
        $this->MultiCell(30,8,utf8_decode("Monto"),1,'C',true);
        $this->SetY($yprev);
        $this->SetX(255);
        $this->MultiCell(30,8,utf8_decode("Comisión"),1,'C',true);
        $yprev = $this->GetY();

        $this->SetFillColor(235,235,235);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);

        $usd_inv = 0;
        $ten = 0;

        foreach($secData as $row)
        {
            // dd($row['nuc']);
            if($ycurr > 180)
            {
                $this->AddPage('L');
                $yprev = 40;
            }
            $this->SetY($yprev);
            $this->MultiCell(50,8,utf8_decode($row['nuc']),1,'L',true);
            $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(50);
            $this->MultiCell(20,8,utf8_decode($row['pay_date']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(70);
            $this->MultiCell(77,8,utf8_decode($row['usname']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(147);
            $this->MultiCell(78,8,utf8_decode($row['clname']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(225);
            $this->MultiCell(30,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['amount'])), 2, ".", ",")." ".utf8_decode($row['currency']),1,'L',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $this->SetX(255);
            $this->MultiCell(30,8,"$".number_format(floatval(preg_replace('/[^\d\.]+/', '', $row['gross_amount'])), 2, ".", ","),1,'C',true);
            if($ycurr < $this->GetY()) $ycurr = $this->GetY();
            $this->SetY($yprev);
            $yprev = $ycurr;
            $this->Ln();
        }
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function PrintPDF($month,$sum,$tc,$data,$dataFstLp,$dataScndLp,$total)
    {
        $this->month = $month;
        $this->AliasNbPages();
        $this->AddPage('L');
        $this->SetFont('Times','',12);
        $this->FancyTable($sum,$tc,$total);
        $this->CPTable($sum,$tc,$data);
        $this->LPTable($dataFstLp,$dataScndLp,$total);
    }
}
?>
