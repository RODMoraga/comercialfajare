<?php

namespace App\Fajare\uploads;

use FPDF;

class GenerateDocument extends FPDF {

    public function __construct()
    {

    }

    function Header() {
        $this->Image('src/public/img/image-cf.png',90,10,30,25);
        $this->SetFont('Arial','B',16) ;
        $this->Cell(120) ;
        $this->Cell(60,10,'GUIA DE DESPACHO',0,0,'C') ;
        $this->Ln(7) ;
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont("Arial", "I", 8);
        $this->Cell(0, 10, "Page ".$this->PageNo()."/{nb}", 0, 0, "C");
    }
}