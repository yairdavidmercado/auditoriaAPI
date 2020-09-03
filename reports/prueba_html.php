<?php
session_start(); 
set_time_limit(3000);
ini_set('memory_limit', '128M');
include '../../php/conexion.php';
date_default_timezone_set('America/Bogota');


$idUsuario                  = $_SESSION["cod_usua"];
$codigo                     =  $_GET["codigo"];
$parametro                  = $_GET["parametro"];
$fecha_informe              = $_GET["fecha_informe"];

  $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
  if($conn){
   
  if ($codigo == '1') {
      $result = pg_query($conn, "SELECT t1.consec, 
                                t1.nombre AS titulo, 
                                wmostrar_subtitulo_ac(t1.consec, '3') AS subtitulo, 
                                t2.consec AS consec_preg, 
                                t2.nombre AS pregunta, 
                                t2.dependencia AS dep_pregunta,
                                t3.consec AS consec_item,
                                t3.nombre AS nombre_item,
                                t3.tipo AS tipo,
                                t3.dependencia AS dep_item,
                                wverificar_respuesta_auditoriac(t3.consec, '$parametro') AS respuesta
                                FROM wpreguntas AS t1
                                INNER JOIN wpreguntas AS t2 ON (t1.consec=t2.dependencia)
                                INNER JOIN wpreguntas AS t3 ON (t2.consec=t3.dependencia)
                                 WHERE TRIM(t1.tipo) ='TITULO'  AND TRIM(t2.tipo)='PREGUNTA' AND (TRIM(t3.tipo)='OPCION' OR TRIM(t3.tipo)='OPCION2' OR TRIM(t3.tipo)='OPCION3' OR TRIM(t3.tipo)='OPCIONS' OR TRIM(t3.tipo)='FECHA' OR TRIM(t3.tipo)='OBSERVACION')
                                 AND t1.perfil = '3' order by t3.consec asc");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  $id = '';
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();

                            // $tabla_evoluciones = crear_tabla_evoluciones($parametro, $conn);
                            // $tabla_signos_vitales = crear_tabla_signos_vitales($parametro, $conn);
                            // $tabla_medicamentos = crear_tabla_medicamentos($parametro, $conn);
                            $datos["consec"] = $row["consec"];
                            $datos["titulo"] = strtoupper ($row["titulo"]);
                            $datos["sub_titulo"] = $row["sub_titulo"];
                            $datos["pregunta"] = $row["pregunta"];
                            $datos["dep_pregunta"] = $row["dep_pregunta"];
                            $datos["consec_preg"] = $row["consec_preg"];
                            $datos["consec_item"] = $row["consec_item"];
                            $datos["nombre_item"] = $row["nombre_item"];
                            $datos["dep_item"] = $row["dep_item"];
                            $datos["respuesta"] = $row["respuesta"];

                            if ($consec > $id || $id == '' ) {

                            }
                            $id = $consec;

                            
                            //$preguntas = mostrar_preguntas($consec, $conn);
                                                                                                                        

                    // push single product into final response array
                    array_push($response["resultado"], $datos);
                    $total_array = json_encode($response);
                    $tabla = titulos($total_array);
                  }
                  $response["success"] = true;
                }else{
                  $fila = 'No se han encontrado resultados';
                  $response["success"] = false;
                  $response["message"] = "No se encontraron registros";
                  // echo no users JSON
                }
      

    }else if ($codigo == '2') {
      $result = pg_query($conn, "SELECT t1.consec, 
                                t1.nombre AS titulo, 
                                wmostrar_subtitulo_ac(t1.consec, '3') AS subtitulo, 
                                t2.consec AS consec_preg, 
                                t2.nombre AS pregunta, 
                                t2.dependencia AS dep_pregunta,
                                t3.consec AS consec_item,
                                t3.nombre AS nombre_item,
                                t3.tipo AS tipo,
                                t3.dependencia AS dep_item,
                                wverificar_respuesta_auditoriac(t3.consec, '$parametro') AS respuesta
                                FROM wpreguntas AS t1
                                INNER JOIN wpreguntas AS t2 ON (t1.consec=t2.dependencia)
                                INNER JOIN wpreguntas AS t3 ON (t2.consec=t3.dependencia)
                                 WHERE TRIM(t1.tipo) ='TITULO'  AND TRIM(t2.tipo)='PREGUNTA' AND (TRIM(t3.tipo)='OPCION' OR TRIM(t3.tipo)='OPCION2' OR TRIM(t3.tipo)='OPCION3' OR TRIM(t3.tipo)='OPCIONS' OR TRIM(t3.tipo)='FECHA' OR TRIM(t3.tipo)='OBSERVACION')
                                 AND t1.perfil = '3' 
                                 AND wverificar_respuesta_auditoriac(t3.consec, '$parametro') = 'NO CUMPLE'  order by t3.consec asc");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  $id = '';
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();

                            // $tabla_evoluciones = crear_tabla_evoluciones($parametro, $conn);
                            // $tabla_signos_vitales = crear_tabla_signos_vitales($parametro, $conn);
                            // $tabla_medicamentos = crear_tabla_medicamentos($parametro, $conn);
                            $datos["consec"] = $row["consec"];
                            $datos["titulo"] = strtoupper ($row["titulo"]);
                            $datos["sub_titulo"] = $row["sub_titulo"];
                            $datos["pregunta"] = $row["pregunta"];
                            $datos["dep_pregunta"] = $row["dep_pregunta"];
                            $datos["consec_preg"] = $row["consec_preg"];
                            $datos["consec_item"] = $row["consec_item"];
                            $datos["nombre_item"] = $row["nombre_item"];
                            $datos["dep_item"] = $row["dep_item"];
                            $datos["respuesta"] = $row["respuesta"];

                            if ($consec > $id || $id == '' ) {

                            }
                            $id = $consec;

                            
                            //$preguntas = mostrar_preguntas($consec, $conn);
                                                                                                                        

                    // push single product into final response array
                    array_push($response["resultado"], $datos);
                    $total_array = json_encode($response);
                    $tabla = titulos($total_array);
                  }
                  $response["success"] = true;
                }else{
                  $fila = 'No se han encontrado resultados';
                  $response["success"] = false;
                  $response["message"] = "No se encontraron registros";
                  // echo no users JSON
                }
      

    }else{

    }
      

    }else{
    $html='No hay conexión';
  }

  function titulos($array1){
    $array = json_decode($array1, true);
    if (is_array($array) || is_object($array))
    {
      $id_titulo = '';
        foreach($array as $obj){
          for ($x = 0; $x <= count($obj); $x++) {
            if ($obj[$x]['consec'] > $id_titulo || $id_titulo == '') {
               $value .= '<div class="row" style="background-color:#EFF0F1">
                            <label class="text-info" style="font-size:9px"><b>'.$obj[$x]['titulo'].'</b></label>
                            <p style="font-size:10px">'.$obj[$x]['sub_titulo'].'</p>
                          </div>

                          <div class="row">
                            <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                            <!-- Add the extra clearfix for only the required viewport -->
                            <div class="clearfix visible-xs-block"></div>
                          </div>'.preguntas($array1, $obj[$x]['consec']);

               

               $id_titulo = $obj[$x]['consec'];
            }

          }

        }
    }
    return $value;
  }


  function preguntas($array1, $id){
    $array = json_decode($array1, true);
    if (is_array($array) || is_object($array))
    {
      $id_preg = '';
      $contador = 0;
        foreach($array as $obj){
          for ($x = 0; $x <= count($obj); $x++) {
            if ($obj[$x]['consec_preg'] > $id_preg || $id_preg == '') {
              if ($obj[$x]['dep_pregunta'] == $id) {
              $contador = $contador+1;

              if ($contador == 1) {
               $content .=  '<div class="row">';
               $content .= '<div style="font-size:9px; padding:2.5px;" class="col-xs-6">

                                <span class="text-muted" style="color:#000">'.$obj[$x]['pregunta'].'</span>
                                <br>
                                '.respuestas($array1, $obj[$x]['consec_preg']).'
                              </div>';

              }else if ($contador == 2) {

                $content .= '<div style="font-size:9px; padding:2.5px;" class="col-xs-6">

                                <span class="text-muted" style="color:#000">'.$obj[$x]['pregunta'].'</span>
                                <br>
                                '.respuestas($array1, $obj[$x]['consec_preg']).'
                              </div>';
                 $content .=  '</div>';
                 $contador = 0;
                }

              }

              
               $id_preg = $obj[$x]['consec_preg'];

            }

          }


        }
    }
    return $content.'</div>';
  }

  function respuestas($array1, $id){
    $array = json_decode($array1, true);
    if (is_array($array) || is_object($array))
    {
      $id_item = '';
      $contador = 0;
        foreach($array as $obj){
          for ($x = 0; $x <= count($obj); $x++) {
            if ($obj[$x]['consec_item'] > $id_item || $id_item == '') {
              if ($obj[$x]['dep_item'] == $id) {

                if ($obj[$x]['respuesta'] == 'NO CUMPLE') {
                  $value .= '<span class="text-muted" style="color:#b56600"><b>'.$obj[$x]['respuesta'].'</b></span><br>';
                }else if ($obj[$x]['respuesta'] == 'CUMPLE') {
                  $value .= '<span class="text-muted" style="color:#000"><b>'.$obj[$x]['respuesta'].'</b></span><br>';
                }else if ($obj[$x]['respuesta'] == 'NO APLICA') {
                  $value .= '<span class="text-muted" style="color:#000"><b>'.$obj[$x]['respuesta'].'</b></span><br>';
                }else if ($obj[$x]['respuesta'] == 'SI') {
                  $value .= '<span class="text-muted" style="color:#000"><b>'.$obj[$x]['respuesta'].'</b></span><br>';
                }else if ($obj[$x]['respuesta'] == 'NO') {
                  $value .= '<span class="text-muted" style="color:#b56600"><b>'.$obj[$x]['respuesta'].'</b></span><br>';
                }else{
                  $value .= '<span class="text-muted" style="color:#000">'.$obj[$x]['nombre_item'].'</span>
                                <br><span class="text-muted">'.$obj[$x]['respuesta'].'</span><br>';
                }

              //$contador = $contador+1;                                                         
              }

               $id_item = $obj[$x]['consec_item'];
            }

          }

        }
    }
    return $value;
  }


  function informacion_paciente($cod_audi, $conn1){
    $result = pg_query($conn1,  "SELECT 
                                    wauditorias.cod_audi,
                                    wauditorias.terminado,
                                    tadmision.cod_admi,
                                    tadmision.nom_acom,
                                    tpaciente.cod_pacien,
                                    tpaciente.id_pacien,
                                    tpaciente.tipo_id_pacien,
                                    tpaciente.fecha_nac,
                                    (select sel_edad(tpaciente.fecha_nac) LIMIT 1)as edad,
                                    tpaciente.sexo_pacien,
                                    tpaciente.est_civil,
                                    tpaciente.dir_pacien,
                                    tpaciente.tiposangre,
                                    tocupaciones.nombre AS ocupacion,
                                    tmunicipio.nom_muni,
                                    tdepartamento.nom_depa,
                                    tentidad.nom_ase,
                                    tpaciente.zona,
                                    tpaciente.tel_pacien,
                                    CASE tpaciente.tipo_afiliado WHEN '1' THEN 'COTIZANTE' WHEN '2' THEN 'BENEFICIARIO' WHEN '3' THEN 'ADICIONAL' END AS tipoafiliado,
                                    tpaciente.nivel,
                                    tentidad.id_ase,
                                    trim(tpaciente.nom1)||' '||trim(tpaciente.nom2)||' '||trim(tpaciente.apell1)||' '||trim(tpaciente.apell2) AS nombre,
                                  to_char(wauditorias.fechacrea, 'HH:MI:SS am') AS hora_creacion,
                                  SUBSTRING(wauditorias.fechacrea::text FROM 0 FOR 11) AS fecha_solicitud,
                                  (SELECT nom_usua FROM tusuario WHERE cod_usua = wauditorias.cod_usua LIMIT 1 ) AS creador,
                                  SUBSTRING(now()::text FROM 0 FOR 11) AS fecha_actual,
                                  to_char(now(), 'HH:MI:SS AM') AS hora_actual
                                    FROM tadmision 
                                    INNER JOIN wauditorias ON tadmision.cod_admi = wauditorias.cod_admi
                                    INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                    LEFT JOIN tocupaciones ON tpaciente.ocupacion = tocupaciones.codigo
                                    LEFT JOIN tmunicipio ON tpaciente.codmunicipio = tmunicipio.cod_muni AND tpaciente.coddpto = tmunicipio.cod_depa
                                    LEFT JOIN tdepartamento ON tpaciente.coddpto = tdepartamento.cod_depa
                                    LEFT JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                    LEFT JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra
                                    LEFT JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad
                                    WHERE wauditorias.cod_audi = '$cod_audi'::integer
                                    AND wauditorias.anulado = 'f';");

      if (pg_num_rows($result) > 0)
                {
                  //$response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $fecha_solicitud = $row["fecha_solicitud"];
                    $hora = $row["hora_creacion"];
                    $cod_audi = $row["cod_audi"];
                    $terminado = $row["terminado"];

                    if ($terminado == 'f') {
                      $terminado = '<div id="marca_agua">
                                      <img src="img/sin_finalizar.png" height="100%" width="100%" />
                                  </div>';
                    }else if ($terminado == 't') {
                      $terminado = ' ';
                    }

                    $header = '<table border="0" style="width:100%; font-size:9px">

                                <tr>
                                  <td style="width:100px;"  rowspan="2">
                                    <div class="grid-item" style="width:150px; height:60px;">
                                    <img src="img/logo_esperanza.png" width="100%">
                                    </div>
                                  </td>

                                  <td style="font-size:12px;" colspan="2">
                                    <span class="title"><b>CLÍNICA LA ESPERANZA<b></span>
                                  </td>

                                  <td style="font-size:12px; text-align:right;">

                                    <span class="text-info"><b>AUDITORÍA TÉCNICA ADMINISTRATIVA</b></span>
                                  </td>
                                  
                                </tr>
                                <tr>
                                  <td style="width:60px; text-align:right;">
                                      <span><b>NIT: </b></span>
                                      <br>
                                      <span><b>DIRECCIÓN: </b></span>
                                      <br>
                                      <span><b>TELÉFONO: </b></span>
                                      <br>
                                      <span><b>EMAIL: </b></span>
                                  </td>
                                  <td>
                                      <span>900005955</span>
                                      <br>
                                      <span>CLL 12 #4-58 Barrio Buenavista</span>
                                      <br>
                                      <span>(4) 7848903 - 7868207 - 7869501</span>
                                      <br>
                                      <span>evaluamosipsltda@hotmail.com</span>
                                  </td>
                                  <td style="width:300px; text-align:right;">
                                    <span style="font-size:11px"><b>RESULTADO DE AUDITORIA No. '.$cod_audi.'</b></span>
                                    <br>
                                    <span style="font-size:9px;">'.obtenerFechaEnLetra($fecha_solicitud).' - '.$hora.'</span>
                                  </td>
                                </tr>
                              </table>';

                    //$datos = array();
                    $content = '<div class="row" style="font-size:9px">
                                                  <table border="0" style="width:100%">

                                                    <tr>

                                                      <td style="height:-2px;" colspan="3">
                                                        <span><b>IDENTIFICACIÓN: </b>'.$row["ipo_id_pacien"].' '.$row["id_pacien"].'</span>
                                                      </td>

                                                      <td style="height:-2px;">
                                                        <span><b>N° ADMISÍON: </b>'.$row["cod_admi"].'</span>
                                                      </td>

                                                    </tr>
                                                    <tr>

                                                      <td style="height:-2px;" colspan="3">
                                                        <span><b>NOMBRES Y APELLIDOS: </b>'.$row["nombre"].'</span>
                                                      </td>

                                                      <td style="height:-2px;">
                                                        <span><b>TIPO DE AFILIACIÓN: </b> '.$row["tipoafiliado"].'</span>
                                                      </td>

                                                    </tr>
                                                    <tr>

                                                      <td style="width:170px;">
                                                        <span><b>FECHA DE NACIMIENTO: </b>'.$row["fecha_nac"].'</span>
                                                      </td>

                                                      <td style="width:120px;">
                                                        <span><b>EDAD: </b>'.$row["edad"].'</span>
                                                      </td>

                                                      <td style="width:100px;">
                                                        <span><b>SEXO: </b>'.$row["sexo_pacien"].'</span>
                                                      </td>
                                                      <td style="height:-2px;">
                                                        <span><b>NOMBRE DE ACOMPAÑANTE: </b> '.$row["nom_acom"].'</span>
                                                      </td>

                                                    </tr>
                                                    <tr>

                                                      <td style="height:-2px;" colspan="3">
                                                        <span><b>TELÉFONO: </b>'.$row["tel_pacien"].'</span>
                                                      </td>
                                                      
                                                      <td style="height:-2px;">

                                                      </td>

                                                    </tr>
                                                    <tr>

                                                      <td style="height:-2px;" colspan="3">
                                                        <span><b>ASEGURADORA: </b>'.$row["nom_ase"].'</span>
                                                      </td>

                                                      <td style="height:-2px;">

                                                      </td>

                                                    </tr>

                                                  </table>

                                              </div>';
                    // push single product into final response array
                    // array_push($response["resultado"], $datos);
                  }

                 return '<header>
                            '.$header.'
                        </header>
                        '.$terminado.'
                        <div class="row" style="background-color:#EFF0F1">

                          <label class="text-info" style="font-size:9px"><b>INFORMACION DEL PACIENTE</b></label>
                        </div>

                        <div class="row">
                          <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                          <!-- Add the extra clearfix for only the required viewport -->
                          <div class="clearfix visible-xs-block"></div>
                        </div>'.$content;
                  // $response["success"] = true;
                }else{

                  $tabla = '';

                }
  }




  function obtenerFechaEnLetra($fecha){
    $dia= conocerDiaSemanaFecha($fecha);
    $num = date("j", strtotime($fecha));
    $anno = date("Y", strtotime($fecha));
    $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    return $dia.', '.$num.' de '.$mes.' del '.$anno;
}
 
function conocerDiaSemanaFecha($fecha) {
    $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    $dia = $dias[date('w', strtotime($fecha))];
    return $dia;
}

  $html='<!DOCTYPE html>
            <html>
            <head>
              <meta charset="utf-8">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title></title>
              <link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
                <style>

                  /** Define the margins of your page **/
                    @page {
                        margin: 100px 40px;
                    }

                    body {
                        margin-top: 1cm;
                        
                    }

                    header {
                        position: fixed;
                        top: -70px;
                        left: 0px;
                        right: 0px;
                        height: 200px;

                    }

                  * {
                        box-sizing: border-box;
                    }

                    [class*="col-"] {
                        float: left;
                        padding: 0px;
                    }

                    [class*=col-]{
                        & + .col-xs-12, & + .col-sm-12, & + .col-md-12 , & + .col-lg-12{
                            float: left;
                        }
                    }
                    html {
                        font-family: "Tahoma","Verdana","Segoe","sans-serif";
                    }

                     #marca_agua {
                          position: fixed;

                          /** 
                              Set a position in the page for your image
                              This should center it vertically
                          **/
                          bottom:   1.5cm;
                          left:     1.5cm;

                          /** Change image dimensions**/
                          width:    16cm;
                          height:   20cm;

                          /** Your watermark should be behind every content**/
                          z-index:  -1000;
                      }

                </style>
            </head>
            <body> 
              '.informacion_paciente($parametro, $conn).'
              '.$tabla.'

            </div>

                    <script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>

            </body>
            </html>';

// instantiate and use the dompdf class

echo $html;
?>