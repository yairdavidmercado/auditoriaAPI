<?php


require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
//$dia=date(Y);
$html='
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
</head>
<body>

  <div class="container">

<div class="table-responsive">
  <table class="table">


  <caption>Datos Personales</caption>

    <thead>
        <tr>
           <th>N</th>
           <th>Nombre</th>
           <th>Detalle</th>
           <th>Cliente</th>
           <th>Estado</th>
           <th>Fecha</th>
           <th>Total</th>
           <th>Pagado</th>
           <th>Saldo</th>
         </tr>
    </thead>


    <tbody>
        <tr>
           <td>1</td>
           <td>Maria</td>
           <td width="300px">pode4mos trabajar con el tamaño de la tabla en esta celda para que no importe lo demas
          </td>
           <td>Maria</td>
           <td>Pago</td>
           <td> 2016/01/11</td>
           <td>$45897403</td>
           <td>$45897403</td>
           <td>$45897403</td>
       </tr>
      
    </tbody>
</table></div></div>

        <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>






</body>
</html>


 ';
// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("my_pdf.pdf", array("Attachment" => 0));
?>
