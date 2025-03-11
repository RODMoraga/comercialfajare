<?php
require("../../fpdf/fpdf.php") ;
require("../../config/Connection.php");

class PDF extends FPDF {
    function Header() {
        $this->Image("image-cf.png",90,10,30,25);
        $this->SetFont("Arial","B",16) ;
        $this->Cell(120) ;
        $this->Cell(60,10,"GUIA DE DESPACHO",0,0,"C") ;
        $this->Ln(7) ;
    }

    function Footer() {}

}

$textSQL = "SELECT h.folio
, date_format(h.deliverdate, '%d-%m-%Y') AS deliverdate
, h.dateorder 
, c.customercode 
, c.customername
, c.complex 
, c.phone1 
, h.net 
, h.tax 
, h.discount 
, h.total 
, ucase(concat(c.street, ', ', c2.cityname)) AS street 
FROM headerdocument h 
INNER JOIN customers c ON h.customerid = c.customerid  
INNER JOIN cities c2 ON c.cityid = c2.cityid 
WHERE h.headerdocumentid=".$_POST['id'];

/*$file = fopen('../../logs/ordernotepdf.sql','w') ;
fwrite($file, $textSQL) ;
fclose($file) ;*/

$query = $connection->query( $textSQL ) ;

while ($obj = $query->fetch_object()) {
    $name = $obj->complex;
    $code = $obj->customercode;
    $folio = $obj->folio;
    $phone1 = $obj->phone1;
    $street = iconv('utf-8', 'cp1252', $obj->street);
    $deliverdate = $obj->deliverdate;
    $net = $obj->net;
    $discount = $obj->discount;
    $tax = $obj->tax;
    $total = $obj->total;
}

$pdf = new PDF();

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont("Arial","",10) ;
$pdf->Cell(20,10,"Empresa:",0,0,"") ;
$pdf->Cell(30,10,"COMERCIAL FAJARE",0,0,"") ;
$pdf->Cell(80) ;
$pdf->Cell(30,10,"Folio:",0,0,"") ;
$pdf->Cell(10,10,$folio,0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Telefono:",0,0,"") ;
$pdf->Cell(30,10,"+56 9 4634 1451",0,0,"") ;
$pdf->Cell(80) ;
$pdf->Cell(30,10,"Fecha Emision:",0,0,"") ;
$pdf->Cell(10,10,date('d-m-Y'),0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"E-Mail:",0,0,"") ;
$pdf->Cell(30,10,"contacto@comercialfajare.cl",0,0,"") ;
$pdf->Cell(80) ;
$pdf->Cell(30,10,"Fecha Entrega:",0,0,"") ;
$pdf->Cell(10,10,$deliverdate,0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Sitio Web:",0,0,"") ;
$pdf->Cell(30,10,"comercialfajare.com",0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Direccion:",0,0,"") ;
$pdf->Cell(30,10,"CAMINO MELIPILLA # 7786, SANTIAGO",0,0,"") ;
$pdf->Ln(10) ;
$pdf->SetTextColor(0,0,0) ;
$pdf->Cell(190,25,"",1,0);
$pdf->Ln(0) ;
$pdf->Cell(20,10,"Rut:",0,0,"") ;
$pdf->Cell(30,10,$code,0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Clientes:",0,0,"") ;
$pdf->Cell(30,10,$name,0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Direccion:",0,0,"") ;
$pdf->Cell(30,10,$street,0,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(20,10,"Telefono:",0,0,"") ;
$pdf->Cell(30,10,$phone1,0,0,"") ;
$pdf->Ln(12) ;

$pdf->Cell(190,100,"",1,0,);
$pdf->Ln(0) ;
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,5,"CODIGO",1,0,"");
$pdf->Cell(65,5,"DESCRIPCION",1,0,"");
$pdf->Cell(20,5,"MEDIDA",1,0,"");
$pdf->Cell(20,5,"CANTIDAD",1,0,"");
$pdf->Cell(20,5,"PRECIO",1,0,"");
$pdf->Cell(15,5,"DCTO",1,0,"");
$pdf->Cell(30,5,"TOTAL",1,0,"");
$pdf->Ln(5) ;

$pdf->SetFont('Arial','',9);
$query = $connection->query("SELECT p.productcode, p.productname, '' AS measure, d.quantity, d.price, d.discount1/100 as discount1, d.quantity * d.price * (1 - (d.discount1 / 100)) AS subtotal 
FROM headerdocument h 
INNER JOIN detaildocument d ON h.headerdocumentid = d.headerdocumentid 
INNER JOIN products p ON d.productid = p.productid 
WHERE h.headerdocumentid = ".$_POST['id']);

$num_line = 0;
while ($obj = $query->fetch_object()) {
    $txt = iconv('utf-8', 'cp1252', $obj->productname);
    $pdf->Cell(20,5,$obj->productcode,0,0,"");
    $pdf->Cell(65,5,$txt,0,0,"");
    $pdf->Cell(20,5,$obj->measure,0,0,"");
    $pdf->Cell(20,5,$obj->quantity,0,0,"R");
    $pdf->Cell(20,5,number_format($obj->price,0,',','.'),0,0,"R");
    $pdf->Cell(15,5,number_format($obj->discount1,2,',','.').'%',0,0,"R");
    $pdf->Cell(30,5,number_format($obj->subtotal,0,',','.'),0,0,"R");
    $pdf->Ln(5) ;
    $num_line++;
}

$pdf->SetFont('Arial','B',10);
$max_line = 95 - $num_line;

switch ($num_line)
{
    case 2:
        $max_line = $max_line - 4;
        break;
    case 3:
        $max_line = $max_line - 8;
        break;
    case 4:
        $max_line = $max_line - 12;
        break;
    case 5:
        $max_line = $max_line - 16;
        break;
    case 6:
        $max_line = $max_line - 20;
        break;
    case 7:
        $max_line = $max_line - 24;
        break;
    case 8:
        $max_line = $max_line - 28;
        break;
    case 9:
        $max_line = $max_line - 32;
        break;
    case 10:
        $max_line = $max_line - 36;
        break;
}

$pdf->Ln($max_line) ;
$pdf->Cell(90,25,"",1,0,"");
$pdf->Cell(10);
$pdf->Cell(90,25,"",1,0,"");
$pdf->Ln(0) ;
$pdf->Cell(90,5,"Comentarios",1,0,"C");
$pdf->Cell(10);
$pdf->Cell(45,5,"Suma:",1,0, "L");
$pdf->Cell(45,5,number_format($net,0,',','.'),1,0,"R") ;
$pdf->Ln(5) ;
$pdf->Cell(90,5,"",0,0,"C");
$pdf->Cell(10);
$pdf->Cell(45,5,"Descuento:",1,0, "L");
$pdf->Cell(45,5,number_format($discount,0,',','.'),1,0,"R") ;
$pdf->Ln(5) ;
$pdf->Cell(90,5,"",0,0,"C");
$pdf->Cell(10);
$pdf->Cell(45,5,"Subtotal:",1,0, "L");
$pdf->Cell(45,5,number_format($net - $discount,0,',','.'),1,0,"R") ;
$pdf->Ln(5) ;
$pdf->Cell(90,5,"",0,0,"C");
$pdf->Cell(10);
$pdf->Cell(45,5,"Iva (19%):",1,0, "L");
$pdf->Cell(45,5,number_format($tax,0,',','.'),1,0,"R") ;
$pdf->Ln(5) ;
$pdf->Cell(90,5,"",0,0,"C");
$pdf->Cell(10);
$pdf->Cell(45,5,"Total:",1,0, "L");
$pdf->Cell(45,5,number_format($total,0,',','.'),1,0,"R") ;
$pdf->Ln(6) ;
$pdf->Cell(90,20,"",1,0,"");
$pdf->Ln(0) ;
$pdf->Cell(90,5,"Notas Especiales e Instrucciones",1,0,"C");
$pdf->Ln(35) ;
$pdf->Cell(43,0,"",1,0,"") ;
$pdf->Cell(4) ;
$pdf->Cell(43,0,"",1,0,"") ;
$pdf->Ln(5) ;
$pdf->Cell(45,0,"Firma",0,0,"C") ;
$pdf->Cell(45,0,"Recibido Por",0,0,"C") ;
$pdf->Ln(2) ;

$pdf->SetFont('Arial','',8);
$pdf->Ln(5) ;
$pdf->Cell(190,0,"",1,0,"") ;
$pdf->Ln(2) ;
$pdf->Cell(0,0,"Si tienes alguna pregunta por favor contactanos",0,0,"C") ;
$pdf->Ln(3) ;
$txt = iconv('utf-8', 'cp1252', "Teléfono Contacto: (+56 9) 9536-0923 - E-mail: contacto@comercialfajare.cl");
$pdf->Cell(0,0,$txt,0,0,"C") ;
$pdf->Ln(3) ;
$pdf->Cell(0,0,"Gracias por su preferencia",0,0,"C") ;

$pdf->Output('D',$folio.'.pdf');
?>