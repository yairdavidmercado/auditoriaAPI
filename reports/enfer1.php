<?php
session_start(); 
set_time_limit(300);
include '../../php/conexion.php';
include '../../php/aseguradoras.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
//$dia=date(Y);

$idUsuario                  = $_SESSION["cod_usua"];
$codigo                     = $_GET["codigo"];
$parametro                  = $_GET["parametro"];
$fecha_informe              = $_GET["fecha_informe"];

  $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
  if($conn){
    if ($codigo == '1') {
      $result = pg_query($conn,  "SELECT DISTINCT tadmision.cod_admi AS admision,
                                  wauditoria_admin.consec AS n_auditoria,
                                  tpaciente.id_pacien,
                                  tadmision.fecha_ingre::date as fecha_ingreso,
                                  tpaciente.nom1||' '||tpaciente.nom2||' '||tpaciente.apell1||' '||tpaciente.apell2 AS nom_paciente,
                                  ".$aseguradoras." as nom_contrato,

                                  (CASE wauditoria_admin.steps_1_p1 WHEN '2' 
                                  THEN wauditoria_admin.steps_1_obs_p1||'\n' 
                                  ELSE '' END) AS steps_1_obs_p1, 

                                  (CASE wauditoria_admin.steps_1_p2 WHEN '2' 
                                  THEN wauditoria_admin.steps_1_obs_p2||'\n' 
                                  ELSE '' END) AS steps_1_obs_p2,

                                  (CASE wauditoria_admin.steps_1_p3 WHEN '2' 
                                  THEN wauditoria_admin.steps_1_obs_p3||'\n' 
                                  ELSE '' END) AS steps_1_obs_p3,

                                  (CASE wauditoria_admin.steps_1_p4 WHEN '2' 
                                  THEN wauditoria_admin.steps_1_obs_p4||'\n' 
                                  ELSE '' END) AS steps_1_obs_p4,

                                  (CASE wauditoria_admin.steps_2_p1 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p1||'\n' 
                                  ELSE '' END) AS steps_2_obs_p1, 

                                  (CASE wauditoria_admin.steps_2_p2 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p2||'\n' 
                                  ELSE '' END) AS steps_2_obs_p2,

                                  (CASE wauditoria_admin.steps_2_p3 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p3||'\n' 
                                  ELSE '' END) AS steps_2_obs_p3,

                                  (CASE wauditoria_admin.steps_2_p4 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p4||'\n' 
                                  ELSE '' END) AS steps_2_obs_p4,

                                  (CASE wauditoria_admin.steps_2_p5 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p5||'\n' 
                                  ELSE '' END) AS steps_2_obs_p5, 

                                  (CASE wauditoria_admin.steps_2_p6 WHEN '2' 
                                  THEN wauditoria_admin.steps_2_obs_p6||'\n' 
                                  ELSE '' END) AS steps_2_obs_p6,

                                  (CASE wauditoria_admin.steps_3_p1 WHEN '2' 
                                  THEN wauditoria_admin.steps_3_obs_p1||'\n' 
                                  ELSE '' END) AS steps_3_obs_p1,

                                  (CASE wauditoria_admin.steps_4_p1 WHEN '2' 
                                  THEN wauditoria_admin.steps_4_obs_p1||'\n' 
                                  ELSE '' END) AS steps_4_obs_p1,

                                  (CASE wauditoria_admin.steps_4_p2 WHEN '2' 
                                  THEN wauditoria_admin.steps_4_obs_p2||'\n' 
                                  ELSE '' END) AS steps_4_obs_p2,

                                  (CASE wauditoria_admin.steps_4_p3 WHEN '2' 
                                  THEN wauditoria_admin.steps_4_obs_p3||'\n' 
                                  ELSE '' END) AS steps_4_obs_p3,

                                  (CASE wauditoria_admin.steps_4_p4 WHEN '2' 
                                  THEN wauditoria_admin.steps_4_obs_p4||'\n' 
                                  ELSE '' END) AS steps_4_obs_p4,

                                  (select tplan.nom_plan from tplan WHERE tplan.cod_plan = tcontrato.cod_plan) AS regimen,
                                  tadmision.fecha_ingre::date,
                                  CASE tadmision.via_ingreso WHEN '1'
                                  THEN 'CONSULTA EXTERNA O PROGRAMADA'
                                  WHEN '2'
                                  THEN 'URGENCIAS'
                                  WHEN '3'
                                  THEN 'REMITIDO'
                                  WHEN '4'
                                  THEN 'NACIDO EN INSTITUCION' END AS servicio_ingre,
                                  wauditoria_admin.servicio AS servicio_actual,
                                  CASE ((SELECT MIN(wauditoria_admin.fecha_crea) FROM wauditoria_admin WHERE wauditoria_admin.cod_admi = tadmision.cod_admi) = wauditoria_admin.fecha_crea) 
                                  WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                  CASE tadmision.tipo_enfermedad WHEN 2
                                  THEN 'SI' ELSE 'NO' END AS acc_transito,

                                  (SELECT nom_usua FROM tusuario WHERE tusuario.cod_usua = wauditoria_admin.cod_usua) AS autor,
                                  wauditoria_admin.fecha_crea AS fecha_creacion,
                                  to_char(wauditoria_admin.fecha_crea, 'HH24:MI') AS hora_creacion,
                                  to_char(wauditoria_admin.fecha_crea, 'DD') AS dia_creacion,
                                  CASE to_char(wauditoria_admin.fecha_crea, 'MM')
                                  WHEN '01' THEN 'ENERO'
                                  WHEN '02' THEN 'FEBRERO'
                                  WHEN '03' THEN 'MARZO'
                                  WHEN '04' THEN 'ABRIL'
                                  WHEN '05' THEN 'MAYO'
                                  WHEN '06' THEN 'JUNIO'
                                  WHEN '07' THEN 'JULIO'
                                  WHEN '08' THEN 'AGOSTO'
                                  WHEN '09' THEN 'SEPTIEMBRE'
                                  WHEN '10' THEN 'OCTUBRE'
                                  WHEN '11' THEN 'NOVIEMBRE'
                                  WHEN '12' THEN 'DICIEMBRE'
                                  END AS mes_creacion,
                                  to_char(wauditoria_admin.fecha_crea, 'YYYY') AS year_creacion,
                                  tipo_hallazgo,
                                  descripcion_tipo_hallazgo
                                  FROM tadmision 
                                  INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                  INNER JOIN wauditoria_admin ON wauditoria_admin.cod_admi = tadmision.cod_admi
                                  FULL JOIN waccidente_transito ON waccidente_transito.cod_admi = tadmision.cod_admi
                                  INNER JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                  INNER JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra 
                                  INNER JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad 
                                  WHERE wauditoria_admin.fecha_crea::date = '".$fecha_informe."'::date
                                  AND wauditoria_admin.cod_usua = ".$idUsuario."
                                  ORDER BY wauditoria_admin.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();

                            $steps_1_obs_p1                   = nl2br($row["steps_1_obs_p1"]);
                            $steps_1_obs_p2                   = nl2br($row["steps_1_obs_p2"]);
                            $steps_1_obs_p3                   = nl2br($row["steps_1_obs_p3"]);
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_2_obs_p1                   = nl2br($row["steps_2_obs_p1"]);
                            $steps_2_obs_p2                   = nl2br($row["steps_2_obs_p2"]);
                            $steps_2_obs_p3                   = nl2br($row["steps_2_obs_p3"]);
                            $steps_2_obs_p4                   = nl2br($row["steps_2_obs_p4"]);
                            $steps_2_obs_p5                   = nl2br($row["steps_2_obs_p5"]);
                            $steps_2_obs_p6                   = nl2br($row["steps_2_obs_p6"]);
                            $steps_3_obs_p1                   = nl2br($row["steps_3_obs_p1"]);
                            $steps_4_obs_p1                   = nl2br($row["steps_4_obs_p1"]);
                            $steps_4_obs_p2                   = nl2br($row["steps_4_obs_p2"]);
                            $steps_4_obs_p3                   = nl2br($row["steps_4_obs_p3"]);
                            $steps_4_obs_p4                   = nl2br($row["steps_4_obs_p4"]);

                            $hora                             = $row["hora_creacion"];
                            $dia                              = $row["dia_creacion"];
                            $mes                              = strtolower($row["mes_creacion"]);
                            $year                             = $row["year_creacion"];
                            $n_auditoria                      = $row["n_auditoria"];
                            $admision                         = $row["admision"];
                            $fecha_ingre                      = $row["fecha_ingre"];
                            $nom_contrato                     = $row["nom_contrato"];
                            $servicio_actual                  = $row["servicio_actual"];
                            $nom_paciente                     = $row["nom_paciente"];
                            $revision                         = $row["revision"];
                            $autor                            = $row["autor"];
                            $tipo_hallazgo                    = $row["tipo_hallazgo"];
                            $descripcion_tipo_hallazgo        = nl2br($row["descripcion_tipo_hallazgo"]);
                            $tabla                           .= '<center>
                                                                  <img src="img/header_auditoria_concurrente.png" style="width:100%">
                                                                </center>

                                                                <div class="table-responsive">
                                                                  <table class="table" style="width: 100%">
                                                                    
                                                                    <thead>
                                                                        <tr style="background-color:#f9f9f9">
                                                                           <th colspan="12"><center>FORMATO DE HALLAZGOS DE AUDITORÍA CONCURRENTE CLINICA LA ESPERANZA</center></th>
                                                                        </tr>
                                                                        <tr style="background-color:#f9f9f9">
                                                                           <th colspan="12"><center>EVALUAMOS IPS</center></th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                           <th colspan="4">FECHA:
                                                                           <span style="color:#676767">'.$dia.' de '.$mes.' '.$year.'</span>
                                                                           </th>
                                                                           <th colspan="4">HORA: 
                                                                           <span style="color:#676767">'.$hora.'</span>
                                                                           </th>
                                                                           <th colspan="4"></th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                           <th colspan="4">ADMISIÓN: 
                                                                           <span style="color:#676767">'.$admision.'</span>
                                                                           </th>
                                                                           <th colspan="4">EPS: 
                                                                           <span style="color:#676767">'.$nom_contrato.'</span>
                                                                           </th>
                                                                           <th colspan="4">SERVICIO ACTUAL: 
                                                                           <span style="color:#676767">'.$servicio_actual.'</span>
                                                                           </th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                           <th colspan="12">PACIENTE: 
                                                                           <span style="color:#676767">'.$nom_paciente.'</span>
                                                                           </th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                           <th colspan="12">TIPO DE HALLAZGO: 
                                                                           <span style="color:#676767">'.$tipo_hallazgo.'</span>
                                                                           </th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                           <th colspan="12">DESCRIPCIÓN: 
                                                                           <span style="color:#676767">'.$revision.'</span>
                                                                           </th>
                                                                        </tr>
                                                                        <tr style="font-size:10px">
                                                                        <th colspan="12">

                                                                          <p style="text-align:justify;">
                                                                            <span style="color:#676767">'
                                                                              .$steps_1_obs_p1.
                                                                                $steps_1_obs_p2.
                                                                                $steps_1_obs_p3.
                                                                                $steps_1_obs_p4.
                                                                                $steps_2_obs_p1.
                                                                                $steps_2_obs_p2.
                                                                                $steps_2_obs_p3.
                                                                                $steps_2_obs_p4.
                                                                                $steps_2_obs_p5.
                                                                                $steps_2_obs_p6.
                                                                                $steps_3_obs_p1.
                                                                                $steps_4_obs_p1.
                                                                                $steps_4_obs_p2.
                                                                                $steps_4_obs_p3.
                                                                                $steps_4_obs_p4.
                                                                            '</span>
                                                                          </p>

                                                                        </th>
                                                                        </tr>
                                                                        <tr>
                                                                           <th colspan="12">AUDITOR QUE CERTIFICA: 
                                                                           <span style="color:#676767">'.$autor.'</span>
                                                                           </th>
                                                                        </tr>
                                                                    </thead>
                                                                  </table>
                                                                </div>';                                                                         

                    // push single product into final response array
                    array_push($response["resultado"], $datos);
                  }
                  $response["success"] = true;
                }else{
                  $fila = 'No se han encontrado resultados';
                  $response["success"] = false;
                  $response["message"] = "No se encontraron registros";
                  // echo no users JSON
                }
      

    }else if ($codigo == '2') {
      $result = pg_query($conn,  "SELECT DISTINCT tadmision.cod_admi AS admision,
                                  tpaciente.id_pacien,
                                  tadmision.fecha_ingre::date as fecha_ingreso,
                                  tpaciente.nom1||' '||tpaciente.nom2||' '||tpaciente.apell1||' '||tpaciente.apell2 AS nom_paciente,
                                  ".$aseguradoras." as nom_contrato,


                                  CASE ((SELECT MIN(wauditoria_enfer.fecha_crea) FROM wauditoria_enfer WHERE wauditoria_enfer.cod_admi = tadmision.cod_admi) = wauditoria_enfer.fecha_crea) 
                                    WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                    CASE tadmision.tipo_enfermedad WHEN 2
                                    THEN 'SI' ELSE 'NO' END AS acc_transito,

                                    wauditoria_enfer.fecha1_steps_1_p1, 
                                   wauditoria_enfer.fecha2_steps_1_p1, 
                                   wauditoria_enfer.steps_1_obs, 
                                  (CASE wauditoria_enfer.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p2,
                                    wauditoria_enfer.steps_1_obs_p2 AS steps_1_obs_p2,
                                  (CASE wauditoria_enfer.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p3,
                                    wauditoria_enfer.steps_1_obs_p3 AS steps_1_obs_p3,
                                  (CASE wauditoria_enfer.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p4,
                                  (CASE wauditoria_enfer.steps_1_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p5,
                                  (CASE wauditoria_enfer.steps_1_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p6,
                                  (CASE wauditoria_enfer.steps_1_p7 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p7,
                                  (CASE wauditoria_enfer.steps_1_p8 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p8,
                                  (CASE wauditoria_enfer.steps_1_p9 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p9,
                                  (CASE wauditoria_enfer.steps_1_p10 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p10, 
                                    wauditoria_enfer.steps_1_obs_p4 AS steps_1_obs_p4,

                                  (CASE wauditoria_enfer.steps_2_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p1, 
                                    wauditoria_enfer.steps_2_obs AS steps_2_obs,
                                    wauditoria_enfer.fecha1_steps_2_p1, 
                                    wauditoria_enfer.fecha2_steps_2_p1,
                                  (CASE wauditoria_enfer.steps_2_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p2,
                                  wauditoria_enfer.fecha1_steps_2_p2, 
                                  wauditoria_enfer.fecha2_steps_2_p2,
                                  wauditoria_enfer.steps_2_obs_p2,
                                  (CASE wauditoria_enfer.steps_3_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_3_p1,
                                  wauditoria_enfer.fecha1_steps_3_p1, 
                                  wauditoria_enfer.fecha2_steps_3_p1,
                                  wauditoria_enfer.steps_3_obs,

                                  (CASE wauditoria_enfer.steps_3_p2 WHEN '1' THEN 'SI' WHEN '2' THEN 'NO' END) AS steps_3_p2,

                                  (CASE wauditoria_enfer.steps_4_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p1,
                                   wauditoria_enfer.steps_4_obs, 
                                   (CASE wauditoria_enfer.steps_4_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p2,
                                   (CASE wauditoria_enfer.steps_4_p2a WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p2a,
                                   (CASE wauditoria_enfer.steps_4_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p3,
                                   (CASE wauditoria_enfer.steps_4_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p4,
                                   (CASE wauditoria_enfer.steps_4_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p5,
                                   (CASE wauditoria_enfer.steps_4_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p6,

                                   wauditoria_enfer.fecha1_steps_4_p2, 
                                   wauditoria_enfer.fecha2_steps_4_p2, 
                                   wauditoria_enfer.steps_4_obs_p2, 

                                   (CASE wauditoria_enfer.steps_5_p1 WHEN '1' THEN 'SI' WHEN '2' THEN 'NO' END) AS steps_5_p1,

                                   (CASE wauditoria_enfer.steps_6_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_6_p1,
                                   wauditoria_enfer.fecha1_steps_6_p1, 
                                   wauditoria_enfer.fecha2_steps_6_p1, 
                                   wauditoria_enfer.steps_6_obs,

                                  (select tplan.nom_plan from tplan WHERE tplan.cod_plan = tcontrato.cod_plan) AS regimen,
                                  tadmision.fecha_ingre::date,
                                  CASE tadmision.via_ingreso WHEN '1'
                                  THEN 'CONSULTA EXTERNA O PROGRAMADA'
                                  WHEN '2'
                                  THEN 'URGENCIAS'
                                  WHEN '3'
                                  THEN 'REMITIDO'
                                  WHEN '4'
                                  THEN 'NACIDO EN INSTITUCION' END AS servicio_ingre,
                                  wauditoria_enfer.servicio AS servicio_actual,
                                  CASE ((SELECT MIN(wauditoria_enfer.fecha_crea) FROM wauditoria_enfer WHERE wauditoria_enfer.cod_admi = tadmision.cod_admi) = wauditoria_enfer.fecha_crea) 
                                  WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                  CASE tadmision.tipo_enfermedad WHEN 2
                                  THEN 'SI' ELSE 'NO' END AS acc_transito,

                                  (SELECT nom_usua FROM tusuario WHERE tusuario.cod_usua = wauditoria_enfer.cod_usua) AS autor,
                                  wauditoria_enfer.fecha_crea AS fecha_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'HH24:MI') AS hora_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'DD') AS dia_creacion,
                                  CASE to_char(wauditoria_enfer.fecha_crea, 'MM')
                                  WHEN '01' THEN 'ENERO'
                                  WHEN '02' THEN 'FEBRERO'
                                  WHEN '03' THEN 'MARZO'
                                  WHEN '04' THEN 'ABRIL'
                                  WHEN '05' THEN 'MAYO'
                                  WHEN '06' THEN 'JUNIO'
                                  WHEN '07' THEN 'JULIO'
                                  WHEN '08' THEN 'AGOSTO'
                                  WHEN '09' THEN 'SEPTIEMBRE'
                                  WHEN '10' THEN 'OCTUBRE'
                                  WHEN '11' THEN 'NOVIEMBRE'
                                  WHEN '12' THEN 'DICIEMBRE'
                                  END AS mes_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'YYYY') AS year_creacion,
                                  tipo_hallazgo,
                                  descripcion_tipo_hallazgo
                                  FROM tadmision 
                                  INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                  INNER JOIN wauditoria_enfer ON wauditoria_enfer.cod_admi = tadmision.cod_admi
                                  FULL JOIN waccidente_transito ON waccidente_transito.cod_admi = tadmision.cod_admi
                                  INNER JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                  INNER JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra 
                                  INNER JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad 
                                  WHERE wauditoria_enfer.consec = ".$parametro."
                                  ORDER BY wauditoria_enfer.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();


                                $fecha1_steps_1_p1         =           $row["fecha1_steps_1_p1"];
                                $fecha2_steps_1_p1         =           $row["fecha2_steps_1_p1"];
                                $steps_1_obs               =           nl2br($row["steps_1_obs"]);
                                $steps_1_p2                =           $row["steps_1_p2"];
                                $steps_1_obs_p2            =           nl2br($row["steps_1_obs_p2"]);
                                $steps_1_p3                =           $row["steps_1_p3"];
                                $steps_1_obs_p3            =           nl2br($row["steps_1_obs_p3"]);
                                $steps_1_p4                =           $row["steps_1_p4"];
                                $steps_1_p5                =           $row["steps_1_p5"];
                                $steps_1_p6                =           $row["steps_1_p6"];
                                $steps_1_p7                =           $row["steps_1_p7"];
                                $steps_1_p8                =           $row["steps_1_p8"];
                                $steps_1_p9                =           $row["steps_1_p9"];
                                $steps_1_p10               =           $row["steps_1_p10"];
                                $steps_1_obs_p4            =           nl2br($row["steps_1_obs_p4"]);
                                $steps_2_p1                =           $row["steps_2_p1"];
                                $fecha1_steps_2_p1         =           $row["fecha1_steps_2_p1"];
                                $fecha2_steps_2_p1         =           $row["fecha2_steps_2_p1"];
                                $steps_2_obs               =           nl2br($row["steps_2_obs"]);
                                $steps_2_p2                =           $row["steps_2_p2"];
                                $fecha1_steps_2_p2         =           $row["fecha1_steps_2_p2"];
                                $fecha2_steps_2_p2         =           $row["fecha2_steps_2_p2"];
                                $steps_2_obs_p2            =           nl2br($row["steps_2_obs_p2"]);
                                $steps_3_p1                =           $row["steps_3_p1"];
                                $fecha1_steps_3_p1         =           $row["fecha1_steps_3_p1"];
                                $fecha2_steps_3_p1         =           $row["fecha2_steps_3_p1"];
                                $steps_3_obs               =           nl2br($row["steps_3_obs"]);
                                $steps_3_p2                =           $row["steps_3_p2"];
                                $steps_4_p1                =           $row["steps_4_p1"];
                                $steps_4_obs               =           nl2br($row["steps_4_obs"]);
                                $steps_4_p2                =           $row["steps_4_p2"];
                                $steps_4_p2a               =           $row["steps_4_p2a"];
                                $steps_4_p3                =           $row["steps_4_p3"];
                                $steps_4_p4                =           $row["steps_4_p4"];
                                $steps_4_p5                =           $row["steps_4_p5"];
                                $steps_4_p6                =           $row["steps_4_p6"];
                                $fecha1_steps_4_p2         =           $row["fecha1_steps_4_p2"];
                                $fecha2_steps_4_p2         =           $row["fecha2_steps_4_p2"];
                                $steps_4_obs_p2            =           nl2br($row["steps_4_obs_p2"]);
                                $steps_5_p1                =           $row["steps_5_p1"];
                                $steps_6_p1                =           $row["steps_6_p1"];
                                $fecha1_steps_6_p1         =           $row["fecha1_steps_6_p1"];
                                $fecha2_steps_6_p1         =           $row["fecha2_steps_6_p1"];
                                $steps_6_obs               =           nl2br($row["steps_6_obs"]);

                                $hora                             = $row["hora_creacion"];
                                $dia                              = $row["dia_creacion"];
                                $mes                              = strtolower($row["mes_creacion"]);
                                $year                             = $row["year_creacion"];
                                $admision                         = $row["admision"];
                                $id_pacien                        = $row["id_pacien"];
                                $regimen                          = $row["regimen"];
                                $nom_contrato                     = $row["nom_contrato"];
                                $fecha_ingre                      = $row["fecha_ingre"];
                                $servicio_ingre                   = $row["servicio_ingre"];
                                $servicio_actual                  = $row["servicio_actual"];
                                $acc_transito                     = $row["acc_transito"];
                                $nom_paciente                     = $row["nom_paciente"];
                                $revision                         = $row["revision"];
                                $autor                            = $row["autor"];
                                $tipo_hallazgo                    = $row["tipo_hallazgo"];
                                $descripcion_tipo_hallazgo        = nl2br($row["descripcion_tipo_hallazgo"]);

                                if ($steps_3_p2 == 'SI') {

                                  $si_steps_3_p2 = '<div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">21.1.El suministro del material de osteosíntesis por parte de la EPS, se dio dentro de las primeras 48 horas siguientes a la solicitud del mismo.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: <b>'.$steps_4_obs.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                  <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                  </div>
                                                  
                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.1.Valoración pre-quirúrgica.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p2.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.2 Valoración pre-anestésica.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p2a.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.3 Informe quirúrgico (firmado y sellado)</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p3.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.4 Informe de anestesia (firmado y sellado).</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p4.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.5 Hoja de gasto quirúrgico.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p5.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.6 Es pertinente el ordenamiento de procedimientos de acuerdo a la patología.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p6.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.7 Fecha y hora en la que fue solicitado el procedimiento.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_4_p2.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.8 Fecha y hora en la que fue realizado el procedimiento.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_4_p2.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-12 col-sm-3">

                                                      <span class="text-muted">Observación.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_obs_p2.'</b></span>

                                                    </div>

                                                  </div>';
                                }else{
                                  $si_steps_3_p2 = '';
                                }


                                if ($steps_5_p1 == 'SI') {

                                  $si_steps_5_p1 = '<div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">22.1 El envío de la remisión con sus respectivos soportes (evolución y ordenes medicas por especialidad tratante y formato de remisión diligenciado) se hizo de manera oportuna a la EPS.</span>
                                                      <br>
                                                      <span><b>'.$steps_6_p1.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">22.2 Fecha de la orden médica de remisión.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_6_p1.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">22.3 Observación en la oportunidad de remisiones.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_6_p1.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-12 col-sm-3">

                                                      <span>Observación: <b>'.$steps_6_obs.'</b></span>

                                                    </div>
                                                  </div>';
                                }else{
                                  $si_steps_5_p1 = '';
                                }


                            $tabla          = '<div>

                                                  <div class="row">
                                                    <div class="col-xs-3 col-sm-3">
                                                      <div class="grid-item" style="width:130px; height:80px;">
                                                      <img src="img/logo_esperanza.png" width="100%">
                                                      </div>
                                                    </div>

                                                    <div style="font-size:9px;" class="col-xs-3 col-sm-3">

                                                      <span><b>NIT: </b>900005955</span>
                                                      <br>
                                                      <span><b>Dirección: </b>CLL 12 #4-58 Barrio Buenavista</span>
                                                      <br>
                                                      <span><b>Teléfono: </b>(4) 7848903 - 7868207 - 7869501</span>
                                                      <br>
                                                      <span><b>Email: </b>: evaluamosipsltda@hotmail.com</span>

                                                    </div>
                                                    <div class="col-xs-3 col-sm-3">
                                                    </div>
                                                    <div class="col-xs-3 col-sm-3">
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>AUDITORÍA POR ENFERMERÍA</b></p>
                                                    <br>
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>RESULTADO DE AUDITORIA No. '.$parametro.'</b></p>
                                                    <br>
                                                     <span class="pull-right" style="font-size:8px">'.$mes.' '.$dia.' de '.$year.' - '.$hora.'</span>

                                                    </div>

                                                  </div>

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Información del paciente</label>
                                                    
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Nombres y Apellidos: </b>'.$nom_paciente.'</span>
                                                      <br>
                                                      <span><b>Identificación: </b>'.$id_pacien.'</span>
                                                      <br>
                                                      <span><b>Régimen: </b>'.$regimen.'</span>
                                                      <br>
                                                      <span><b>EPS: </b>'.$nom_contrato.'</span>
                                                      <br>
                                                      <span><b>Accidente de tránsito: </b>'.$acc_transito.'</span>

                                                    </div>
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Admisión: </b>'.$admision.'</span>
                                                      <br>
                                                      <span><b>Fecha Ingreso: </b>'.$fecha_ingre.'</span>
                                                      <br>
                                                      <span><b>Servicio Ingreso: </b> '.$servicio_ingre.'</span>
                                                      <br>
                                                      <span><b>Servicio Actual: </b> '.$servicio_actual.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Auditoría</label>
                                                    
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">SEGUIMIENTO POR EL PERSONAL DEL ÁREA DE LA SALUD</label>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">1. Fecha y hora en la que fue solicitada la valoración.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_1_p1.'</b></span>

                                                    </div>

                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">2. Fecha y hora en la que el médico especialista valora al paciente.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_1_p1.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">Observación</span>
                                                      <br>
                                                      <span><b>'.$steps_1_obs.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">3. Seguimiento diario por especialidad a cargo(Anexo).</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: <b>'.$steps_1_obs_p2.'</b></span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">4. Seguimiento diario por medicína general(Anexo).</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: <b>'.$steps_1_obs_p3.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA.</label>
                                                      </div>

                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">5.Registro de medicamento acorde con orden médica.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p4.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">6.Registro de signos vitales.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p5.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">7.Registro de oxigeno.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p6.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">8.Registro de transfusión sanguínea y hemoderivados.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p7.'</b></span>

                                                    </div>
                                                  </div>


                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">9.Notas de enfermería.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p8.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">10.Registro de glucometría.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p9.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">11.Control de líquido.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p10.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px;" class="col-xs-3 col-sm-3">

                                                      <span>Observación: <b>'.$steps_1_obs_p4.'</b></span>

                                                    </div>
                                                  </div>


                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">REALIZACIÓN DE PARACLÍNICOS</label>
                                                  </div>

                                                  <div class="row">
                                                  <label style="font-size:9px;">OPORTUNIDAD EN LA PRÁCTICA DE LOS EXÁMENES REQUERIDOS: MÁXIMO UNA HORA PARA LA REALIZACIÓN DE LABORATORIOS.</label>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">12.Se encuentra reporte de los laboratorios ordenados.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p1.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">13. Fecha y hora en la que fue solicitado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_2_p1.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">14. Fecha y hora en la que fue realizado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_2_p1.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span>Observación: <b>'.$steps_2_obs.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                  <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE IMÁGENES DIAGNOSTICAS BÁSICAS (RADIOGRAFÍAS/ECOGRAFÍAS): MÁXIMO CUATRO HORAS DE ESPERA.</label>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">15.Se encuentra reporte de los laboratorios ordenados.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p2.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">16. Fecha y hora en la que fue solicitado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_2_p2.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">17. Fecha y hora en la que fue realizado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_2_p2.'</b></span>

                                                    </div>
                                                  </div>
                                                  
                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span>Observación: <b>'.$steps_2_obs_p2.'</b></span>

                                                    </div>
                                                  </div>


                                                  <div class="row">
                                                  <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE IMÁGENES DIAGNÓSTICAS ESPECIALIZADAS O DE 3 NIVEL (TAC/RMN): MÁXIMO 24 HORAS DE ESPERA.</label>
                                                  </div>

                                                  <div class="row">

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">18.Se encuentra reporte de los laboratorios ordenados.</span>
                                                      <br>
                                                      <span><b>'.$steps_3_p1.'</b></span>

                                                    </div>

                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">19. Fecha y hora en la que fue solicitado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha1_steps_3_p1.'</b></span>

                                                    </div>
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">20. Fecha y hora en la que fue realizado el examen.</span>
                                                      <br>
                                                      <span><b>'.$fecha2_steps_3_p1.'</b></span>

                                                    </div>
                                                  </div>
                                                  
                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span>Observación: <b>'.$steps_3_obs.'</b></span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">REALIZACIÓN DE PROCEDIMIENTOS QUIRÚRGICOS.</label>
                                                  </div>

                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">21. Al paciente se le realizó algún procedimiento quirúrgico.</span>
                                                      <br>
                                                      <span><b>'.$steps_3_p2.'</b></span>

                                                    </div>

                                                  </div>

                                                  '.$si_steps_3_p2.'

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">OPORTUNIDAD EN LA REMISIONES.</label>
                                                  </div>

                                                  <div class="row">
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">22. El paciente tiene ordenada remisión médica.</span>
                                                      <br>
                                                      <span><b>'.$steps_5_p1.'</b></span>

                                                    </div>
                                                  </div>

                                                  '.$si_steps_5_p1.'

                                                  <div class="row" style="font-size:9px;" >
                                                    <span>Responsable</span>  
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <span>_________________________</span>
                                                    <br>
                                                    <span><b>'.$autor.'</b></span>
                                                    <br>
                                                    <span>Auditor</span>
                                                  </div>
                                                  
                                              </div>';                                                                         

                    // push single product into final response array
                    array_push($response["resultado"], $datos);
                  }
                  $response["success"] = true;
                }else{
                  $fila = 'No se han encontrado resultados';
                  $response["success"] = false;
                  $response["message"] = "No se encontraron registros";
                  // echo no users JSON
                }
      

    }else if ($codigo == '3') {
      $result = pg_query($conn,  "SELECT DISTINCT tadmision.cod_admi AS admision,
                                  tpaciente.id_pacien,
                                  tadmision.fecha_ingre::date as fecha_ingreso,
                                  tpaciente.nom1||' '||tpaciente.nom2||' '||tpaciente.apell1||' '||tpaciente.apell2 AS nom_paciente,
                                  ".$aseguradoras." as nom_contrato,


                                  CASE ((SELECT MIN(wauditoria_enfer.fecha_crea) FROM wauditoria_enfer WHERE wauditoria_enfer.cod_admi = tadmision.cod_admi) = wauditoria_enfer.fecha_crea) 
                                    WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                    CASE tadmision.tipo_enfermedad WHEN 2
                                    THEN 'SI' ELSE 'NO' END AS acc_transito,

                                    wauditoria_enfer.fecha1_steps_1_p1, 
                                   wauditoria_enfer.fecha2_steps_1_p1, 
                                   wauditoria_enfer.steps_1_obs, 
                                  (CASE wauditoria_enfer.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p2,
                                    wauditoria_enfer.steps_1_obs_p2 AS steps_1_obs_p2,
                                  (CASE wauditoria_enfer.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p3,
                                    wauditoria_enfer.steps_1_obs_p3 AS steps_1_obs_p3,
                                  (CASE wauditoria_enfer.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p4,
                                  (CASE wauditoria_enfer.steps_1_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p5,
                                  (CASE wauditoria_enfer.steps_1_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p6,
                                  (CASE wauditoria_enfer.steps_1_p7 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p7,
                                  (CASE wauditoria_enfer.steps_1_p8 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p8,
                                  (CASE wauditoria_enfer.steps_1_p9 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p9,
                                  (CASE wauditoria_enfer.steps_1_p10 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p10, 
                                    wauditoria_enfer.steps_1_obs_p4 AS steps_1_obs_p4,

                                  (CASE wauditoria_enfer.steps_2_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p1, 
                                    wauditoria_enfer.steps_2_obs AS steps_2_obs,
                                    wauditoria_enfer.fecha1_steps_2_p1, 
                                    wauditoria_enfer.fecha2_steps_2_p1,
                                  (CASE wauditoria_enfer.steps_2_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p2,
                                  wauditoria_enfer.fecha1_steps_2_p2, 
                                  wauditoria_enfer.fecha2_steps_2_p2,
                                  wauditoria_enfer.steps_2_obs_p2,
                                  (CASE wauditoria_enfer.steps_3_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_3_p1,
                                  wauditoria_enfer.fecha1_steps_3_p1, 
                                  wauditoria_enfer.fecha2_steps_3_p1,
                                  wauditoria_enfer.steps_3_obs,

                                  (CASE wauditoria_enfer.steps_3_p2 WHEN '1' THEN 'SI' WHEN '2' THEN 'NO' END) AS steps_3_p2,

                                  (CASE wauditoria_enfer.steps_4_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p1,
                                   wauditoria_enfer.steps_4_obs, 
                                   (CASE wauditoria_enfer.steps_4_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p2,
                                   (CASE wauditoria_enfer.steps_4_p2a WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p2a,
                                   (CASE wauditoria_enfer.steps_4_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p3,
                                   (CASE wauditoria_enfer.steps_4_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p4,
                                   (CASE wauditoria_enfer.steps_4_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p5,
                                   (CASE wauditoria_enfer.steps_4_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p6,

                                   wauditoria_enfer.fecha1_steps_4_p2, 
                                   wauditoria_enfer.fecha2_steps_4_p2, 
                                   wauditoria_enfer.steps_4_obs_p2, 

                                   (CASE wauditoria_enfer.steps_5_p1 WHEN '1' THEN 'SI' WHEN '2' THEN 'NO' END) AS steps_5_p1,

                                   (CASE wauditoria_enfer.steps_6_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_6_p1,
                                   wauditoria_enfer.fecha1_steps_6_p1, 
                                   wauditoria_enfer.fecha2_steps_6_p1, 
                                   wauditoria_enfer.steps_6_obs,

                                  (select tplan.nom_plan from tplan WHERE tplan.cod_plan = tcontrato.cod_plan) AS regimen,
                                  tadmision.fecha_ingre::date,
                                  CASE tadmision.via_ingreso WHEN '1'
                                  THEN 'CONSULTA EXTERNA O PROGRAMADA'
                                  WHEN '2'
                                  THEN 'URGENCIAS'
                                  WHEN '3'
                                  THEN 'REMITIDO'
                                  WHEN '4'
                                  THEN 'NACIDO EN INSTITUCION' END AS servicio_ingre,
                                  wauditoria_enfer.servicio AS servicio_actual,
                                  CASE ((SELECT MIN(wauditoria_enfer.fecha_crea) FROM wauditoria_enfer WHERE wauditoria_enfer.cod_admi = tadmision.cod_admi) = wauditoria_enfer.fecha_crea) 
                                  WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                  CASE tadmision.tipo_enfermedad WHEN 2
                                  THEN 'SI' ELSE 'NO' END AS acc_transito,

                                  (SELECT nom_usua FROM tusuario WHERE tusuario.cod_usua = wauditoria_enfer.cod_usua) AS autor,
                                  wauditoria_enfer.fecha_crea AS fecha_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'HH24:MI') AS hora_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'DD') AS dia_creacion,
                                  CASE to_char(wauditoria_enfer.fecha_crea, 'MM')
                                  WHEN '01' THEN 'ENERO'
                                  WHEN '02' THEN 'FEBRERO'
                                  WHEN '03' THEN 'MARZO'
                                  WHEN '04' THEN 'ABRIL'
                                  WHEN '05' THEN 'MAYO'
                                  WHEN '06' THEN 'JUNIO'
                                  WHEN '07' THEN 'JULIO'
                                  WHEN '08' THEN 'AGOSTO'
                                  WHEN '09' THEN 'SEPTIEMBRE'
                                  WHEN '10' THEN 'OCTUBRE'
                                  WHEN '11' THEN 'NOVIEMBRE'
                                  WHEN '12' THEN 'DICIEMBRE'
                                  END AS mes_creacion,
                                  to_char(wauditoria_enfer.fecha_crea, 'YYYY') AS year_creacion,
                                  tipo_hallazgo,
                                  descripcion_tipo_hallazgo
                                  FROM tadmision 
                                  INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                  INNER JOIN wauditoria_enfer ON wauditoria_enfer.cod_admi = tadmision.cod_admi
                                  FULL JOIN waccidente_transito ON waccidente_transito.cod_admi = tadmision.cod_admi
                                  INNER JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                  INNER JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra 
                                  INNER JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad 
                                  WHERE wauditoria_enfer.consec = ".$parametro."
                                  ORDER BY wauditoria_enfer.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();
                            
                                $fecha1_steps_1_p1         =           $row["fecha1_steps_1_p1"];
                                $fecha2_steps_1_p1         =           $row["fecha2_steps_1_p1"];
                                $steps_1_obs               =           nl2br($row["steps_1_obs"]);
                                $steps_1_p2                =           $row["steps_1_p2"];
                                $steps_1_obs_p2            =           nl2br($row["steps_1_obs_p2"]);
                                $steps_1_p3                =           $row["steps_1_p3"];
                                $steps_1_obs_p3            =           nl2br($row["steps_1_obs_p3"]);
                                $steps_1_p4                =           $row["steps_1_p4"];
                                $steps_1_p5                =           $row["steps_1_p5"];
                                $steps_1_p6                =           $row["steps_1_p6"];
                                $steps_1_p7                =           $row["steps_1_p7"];
                                $steps_1_p8                =           $row["steps_1_p8"];
                                $steps_1_p9                =           $row["steps_1_p9"];
                                $steps_1_p10               =           $row["steps_1_p10"];
                                $steps_1_obs_p4            =           nl2br($row["steps_1_obs_p4"]);
                                $steps_2_p1                =           $row["steps_2_p1"];
                                $fecha1_steps_2_p1         =           $row["fecha1_steps_2_p1"];
                                $fecha2_steps_2_p1         =           $row["fecha2_steps_2_p1"];
                                $steps_2_obs               =           nl2br($row["steps_2_obs"]);
                                $steps_2_p2                =           $row["steps_2_p2"];
                                $fecha1_steps_2_p2         =           $row["fecha1_steps_2_p2"];
                                $fecha2_steps_2_p2         =           $row["fecha2_steps_2_p2"];
                                $steps_2_obs_p2            =           nl2br($row["steps_2_obs_p2"]);
                                $steps_3_p1                =           $row["steps_3_p1"];
                                $fecha1_steps_3_p1         =           $row["fecha1_steps_3_p1"];
                                $fecha2_steps_3_p1         =           $row["fecha2_steps_3_p1"];
                                $steps_3_obs               =           nl2br($row["steps_3_obs"]);
                                $steps_3_p2                =           $row["steps_3_p2"];
                                $steps_4_p1                =           $row["steps_4_p1"];
                                $steps_4_obs               =           nl2br($row["steps_4_obs"]);
                                $steps_4_p2                =           $row["steps_4_p2"];
                                $steps_4_p2a               =           $row["steps_4_p2a"];
                                $steps_4_p3                =           $row["steps_4_p3"];
                                $steps_4_p4                =           $row["steps_4_p4"];
                                $steps_4_p5                =           $row["steps_4_p5"];
                                $steps_4_p6                =           $row["steps_4_p6"];
                                $fecha1_steps_4_p2         =           $row["fecha1_steps_4_p2"];
                                $fecha2_steps_4_p2         =           $row["fecha2_steps_4_p2"];
                                $steps_4_obs_p2            =           nl2br($row["steps_4_obs_p2"]);
                                $steps_5_p1                =           $row["steps_5_p1"];
                                $steps_6_p1                =           $row["steps_6_p1"];
                                $fecha1_steps_6_p1         =           $row["fecha1_steps_6_p1"];
                                $fecha2_steps_6_p1         =           $row["fecha2_steps_6_p1"];
                                $steps_6_obs               =           nl2br($row["steps_6_obs"]);

                                $hora                             = $row["hora_creacion"];
                                $dia                              = $row["dia_creacion"];
                                $mes                              = strtolower($row["mes_creacion"]);
                                $year                             = $row["year_creacion"];
                                $admision                         = $row["admision"];
                                $id_pacien                        = $row["id_pacien"];
                                $regimen                          = $row["regimen"];
                                $nom_contrato                     = $row["nom_contrato"];
                                $fecha_ingre                      = $row["fecha_ingre"];
                                $servicio_ingre                   = $row["servicio_ingre"];
                                $servicio_actual                  = $row["servicio_actual"];
                                $acc_transito                     = $row["acc_transito"];
                                $nom_paciente                     = $row["nom_paciente"];
                                $revision                         = $row["revision"];
                                $autor                            = $row["autor"];
                                $tipo_hallazgo                    = $row["tipo_hallazgo"];
                                $descripcion_tipo_hallazgo        = nl2br($row["descripcion_tipo_hallazgo"]);



                            if ($steps_1_p2 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">SEGUIMIENTO POR EL PERSONAL DEL ÁREA DE LA SALUD</label>
                                                    </div>';

                              $content_steps_1_p2 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">3. Seguimiento diario por especialidad a cargo(Anexo).</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p2.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p2 = '';
                            }

                            if ($steps_1_p3 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">SEGUIMIENTO POR EL PERSONAL DEL ÁREA DE LA SALUD</label>
                                                    </div>';

                              $content_steps_1_p3 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">4. Seguimiento diario por medicína general(Anexo).</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p3 = '';
                            }



                            if ($steps_1_p4 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p4 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">5.Registro de medicamento acorde con orden médica.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p4.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p4 = '';
                            }

                            if ($steps_1_p5 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p5 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">6.Registro de signos vitales.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p5.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p5 = '';
                            }

                            if ($steps_1_p6 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p6 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">7.Registro de oxigeno.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p6.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p6 = '';
                            }

                            if ($steps_1_p7 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p7 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">8.Registro de transfusión sanguínea y hemoderivados.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p7.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p7 = '';
                            }

                            if ($steps_1_p8 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p8 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">9.Notas de enfermería.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p8.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p8 = '';
                            }

                            if ($steps_1_p9 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p9 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">10.Registro de glucometría.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p9.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p9 = '';
                            }

                            if ($steps_1_p10 == 'No Cumple') {

                              $hallazgo_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">SEGUIMIENTO DIARIO POR PERSONAL DE ENFERMERÍA</label>
                                                  </div>';

                              $content_steps_1_p10 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">11.Control de líquido.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p10.'</b></span>

                                                    </div>';
                            }else{
                              $content_steps_1_p10 = '';
                            }

                            if ($steps_1_obs_p4 != '') {
                               $content_steps_1_obs_p4  = ' <div class="row">
                                                    
                                                              <div style="font-size:9px; padding:2.5px" class="col-xs-12 col-sm-3">

                                                                <span>Observación: <b>'.$steps_1_obs_p4.'</b></span>

                                                              </div>
                                                            </div>';
                            }else{
                              $content_steps_1_obs_p4 = '';
                            }
                                                    

                            //---------------------------------------------------------------


                            if ($steps_2_p1 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">REALIZACIÓN DE PARACLÍNICOS</label>
                                                  </div>';                

                              $content_steps_2_p1 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA PRÁCTICA DE LOS EXÁMENES REQUERIDOS: MÁXIMO UNA HORA PARA LA REALIZACIÓN DE LABORATORIOS.</label>
                                                    </div>

                                                    <div class="row">

                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">12.Se encuentra reporte de los laboratorios ordenados.</span>
                                                        <br>
                                                        <span><b>'.$steps_2_p1.'</b></span>

                                                      </div>

                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">13. Fecha y hora en la que fue solicitado el examen.</span>
                                                        <br>
                                                        <span><b>'.$fecha1_steps_2_p1.'</b></span>

                                                      </div>
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">14. Fecha y hora en la que fue realizado el examen.</span>
                                                        <br>
                                                        <span><b>'.$fecha2_steps_2_p1.'</b></span>

                                                      </div>
                                                    </div>

                                                    <div class="row">
                                                      
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">Observación.</span>
                                                        <br>
                                                        <span><b>'.$steps_2_obs.'</b></span>

                                                      </div>
                                                      
                                                    </div>';
                            }else{
                              $content_steps_2_p1 = '';
                            }


                            if ($steps_2_p2 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">REALIZACIÓN DE PARACLÍNICOS</label>
                                                  </div>';
                

                              $content_steps_2_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE IMÁGENES DIAGNOSTICAS BÁSICAS (RADIOGRAFÍAS/ECOGRAFÍAS): MÁXIMO CUATRO HORAS DE ESPERA.</label>
                                                      </div>

                                                    <div class="row">

                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">15.Se encuentra reporte de los laboratorios ordenados.</span>
                                                        <br>
                                                        <span><b>'.$steps_2_p2.'</b></span>

                                                      </div>

                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">16. Fecha y hora en la que fue solicitado el examen.</span>
                                                        <br>
                                                        <span><b>'.$fecha1_steps_2_p2.'</b></span>

                                                      </div>
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">17. Fecha y hora en la que fue realizado el examen.</span>
                                                        <br>
                                                        <span><b>'.$fecha2_steps_2_p2.'</b></span>

                                                      </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                      
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">Observación.</span>
                                                        <br>
                                                        <span><b>'.$steps_2_obs_p2.'</b></span>

                                                      </div>
                                                    </div>';
                            }else{
                              $content_steps_2_p2 = '';
                            }

                            if ($steps_3_p1 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">REALIZACIÓN DE PARACLÍNICOS</label>
                                                  </div>';
                

                              $content_steps_3_p1 = '<div class="row">
                                                      <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE IMÁGENES DIAGNÓSTICAS ESPECIALIZADAS O DE 3 NIVEL (TAC/RMN): MÁXIMO 24 HORAS DE ESPERA.</label>
                                                      </div>

                                                      <div class="row">

                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                          <span class="text-muted">18.Se encuentra reporte de los laboratorios ordenados.</span>
                                                          <br>
                                                          <span><b>'.$steps_3_p1.'</b></span>

                                                        </div>

                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">19. Fecha y hora en la que fue solicitado el examen.</span>
                                                          <br>
                                                          <span><b>'.$fecha1_steps_3_p1.'</b></span>

                                                        </div>
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">20. Fecha y hora en la que fue realizado el examen.</span>
                                                          <br>
                                                          <span><b>'.$fecha2_steps_3_p1.'</b></span>

                                                        </div>
                                                      </div>
                                                      
                                                      <div class="row">
                                                        
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                          <span class="text-muted">Observación.</span>
                                                          <br>
                                                          <span><b>'.$steps_3_obs.'</b></span>

                                                        </div>
                                                      </div>';
                            }else{
                              $content_steps_3_p1 = '';
                            }



                            //---------------------------------------------------------------

                            if ($steps_3_p2 == 'SI') {

                              if ($steps_4_p1 == 'No Cumple') {
                                
                                $content_steps_4_p1 = '<div class="row">

                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                          <span class="text-muted">21.1.El suministro del material de osteosíntesis por parte de la EPS, se dio dentro de las primeras 48 horas siguientes a la solicitud del mismo.</span>
                                                          <br>
                                                          <span><b>'.$steps_4_p1.'</b></span>
                                                          <br>
                                                          <span>Observación: <b>'.$steps_4_obs.'</b></span>

                                                        </div>
                                                      </div>';

                              }else{
                                $content_steps_4_p1 = '';
                              }

                              if ($steps_4_p2 == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p2 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">21.1.Valoración pre-quirúrgica.</span>
                                                          <br>
                                                          <span><b>'.$steps_4_p2.'</b></span>

                                                        </div>';

                              }else{
                                $content_steps_4_p2 = '';
                              }


                              if ($steps_4_p2a == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p2a = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">21.2 Valoración pre-anestésica.</span>
                                                          <br>
                                                          <span><b>'.$steps_4_p2a.'</b></span>

                                                        </div>';

                              }else{
                                $content_steps_4_p2a = '';
                              }


                              if ($steps_4_p3 == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p3 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">21.3 Informe quirúrgico (firmado y sellado)</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p3.'</b></span>

                                                    </div>';

                              }else{
                                $content_steps_4_p3 = '';
                              }


                              if ($steps_4_p4 == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p4 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">21.4 Informe de anestesia (firmado y sellado).</span>
                                                        <br>
                                                        <span><b>'.$steps_4_p4.'</b></span>

                                                      </div>';

                              }else{
                                $content_steps_4_p4 = '';
                              }


                              if ($steps_4_p5 == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p5 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                        <span class="text-muted">21.5 Hoja de gasto quirúrgico.</span>
                                                        <br>
                                                        <span><b>'.$steps_4_p5.'</b></span>

                                                      </div>';

                              }else{
                                $content_steps_4_p5 = '';
                              }


                              if ($steps_4_p6 == 'No Cumple') {

                                $titulo_steps_4_p2 = '<div class="row">
                                                        <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE LOS PROCEDIMIENTOS QUIRÚRGICOS NO URGENTES Y/O PRIORITARIOS: 24 HORAS.</label>
                                                        </div>';
                                
                                $content_steps_4_p6 = '<div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">21.6 Es pertinente el ordenamiento de procedimientos de acuerdo a la patología.</span>
                                                          <br>
                                                          <span><b>'.$steps_4_p6.'</b></span>

                                                        </div>
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">21.7 Fecha y hora en la que fue solicitado el procedimiento.</span>
                                                          <br>
                                                          <span><b>'.$fecha1_steps_4_p2.'</b></span>

                                                        </div>
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">21.8 Fecha y hora en la que fue realizado el procedimiento.</span>
                                                          <br>
                                                          <span><b>'.$fecha2_steps_4_p2.'</b></span>

                                                        </div>';

                              $content_steps_4_p6_obs = '<div style="font-size:9px; padding:2.5px" class="col-xs-12 col-sm-3">

                                                          <span>Observación: <b>'.$steps_4_obs_p2.'</b></span>

                                                        </div>';                                                        

                              }else{
                                $content_steps_4_p6 = '';
                              }



                              $hallazgo_steps_3 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">REALIZACIÓN DE PROCEDIMIENTOS QUIRÚRGICOS.</label>
                                                  </div>';

                              $content_steps_3_p2 = '<div class="row">
                                                    
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">21. Al paciente se le realizó algún procedimiento quirúrgico.</span>
                                                        <br>
                                                        <span><b>'.$steps_3_p2.'</b></span>

                                                      </div>

                                                    </div>
                                                    '.$content_steps_4_p1.'
                                                    '.$titulo_steps_4_p2.'
                                                    <div class="row">
                                                    '.$content_steps_4_p2.'
                                                    '.$content_steps_4_p2a.'
                                                    '.$content_steps_4_p3.'
                                                    '.$content_steps_4_p4.'
                                                    </div>
                                                    <div class="row">
                                                    '.$content_steps_4_p5.'
                                                    '.$content_steps_4_p6.'
                                                    </div>
                                                    <div class="row">
                                                    '.$content_steps_4_p6_obs.'
                                                    </div>

                                                    ';
                            }else{
                              $content_steps_3_p2 = '';
                            }

                            //---------------------------------------------------------------

                            if ($steps_5_p1 == 'SI') {

                              if ($steps_6_p1 == 'No Cumple') {

                                $content_steps_6_p1 = '<div class="row">
                                                      <label style="font-size:9px;">OPORTUNIDAD EN LA REALIZACIÓN DE IMÁGENES DIAGNÓSTICAS ESPECIALIZADAS O DE 3 NIVEL (TAC/RMN): MÁXIMO 24 HORAS DE ESPERA.</label>
                                                      </div>

                                                      <div class="row">

                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                          <span class="text-muted">18.Se encuentra reporte de los laboratorios ordenados.</span>
                                                          <br>
                                                          <span><b>'.$steps_3_p1.'</b></span>

                                                        </div>

                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">19. Fecha y hora en la que fue solicitado el examen.</span>
                                                          <br>
                                                          <span><b>'.$fecha1_steps_3_p1.'</b></span>

                                                        </div>
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                          <span class="text-muted">20. Fecha y hora en la que fue realizado el examen.</span>
                                                          <br>
                                                          <span><b>'.$fecha2_steps_3_p1.'</b></span>

                                                        </div>
                                                      </div>
                                                      
                                                      <div class="row">
                                                        
                                                        <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                          <span>Observación: <b>'.$steps_3_obs.'</b></span>

                                                        </div>
                                                      </div>';
                                # code...
                              }

                              $hallazgo_steps_5 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">OPORTUNIDAD EN LA REMISIONES</label>
                                                  </div>';
                

                              $content_steps_5_p1 = '<div class="row">
                                                    
                                                      <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">22. El paciente tiene ordenada remisión médica.</span>
                                                        <br>
                                                        <span><b>'.$steps_3_p2.'</b></span>

                                                      </div>

                                                    </div>
                                                    '.$content_steps_6_p1.'';
                            }else{
                              $content_steps_3_p1 = '';
                            }

                            //--------------------------------------------------------------------


                            $tabla          = '<div>

                                                  <div class="row">
                                                    <div class="col-xs-3 col-sm-3">
                                                      <div class="grid-item" style="width:130px; height:80px;">
                                                      <img src="img/logo_esperanza.png" width="100%">
                                                      </div>
                                                    </div>

                                                    <div style="font-size:9px;" class="col-xs-3 col-sm-3">

                                                      <span><b>NIT: </b>900005955</span>
                                                      <br>
                                                      <span><b>Dirección: </b>CLL 12 #4-58 Barrio Buenavista</span>
                                                      <br>
                                                      <span><b>Teléfono: </b>(4) 7848903 - 7868207 - 7869501</span>
                                                      <br>
                                                      <span><b>Email: </b>: evaluamosipsltda@hotmail.com</span>

                                                    </div>
                                                    <div class="col-xs-3 col-sm-3">
                                                    </div>
                                                    <div class="col-xs-3 col-sm-3">
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>AUDITORÍA POR ENFERMERÍA</b></p>
                                                    <br>
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>RESULTADO DE AUDITORIA No. '.$parametro.'</b></p>
                                                    <br>
                                                     <span class="pull-right" style="font-size:8px">'.$mes.' '.$dia.' de '.$year.' - '.$hora.'</span>

                                                    </div>

                                                  </div>

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Información del paciente</label>
                                                    
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Nombres y Apellidos: </b>'.$nom_paciente.'</span>
                                                      <br>
                                                      <span><b>Identificación: </b>'.$id_pacien.'</span>
                                                      <br>
                                                      <span><b>Régimen: </b>'.$regimen.'</span>
                                                      <br>
                                                      <span><b>EPS: </b>'.$nom_contrato.'</span>
                                                      <br>
                                                      <span><b>Accidente de tránsito: </b>'.$acc_transito.'</span>

                                                    </div>
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Admisión: </b>'.$admision.'</span>
                                                      <br>
                                                      <span><b>Fecha Ingreso: </b>'.$fecha_ingre.'</span>
                                                      <br>
                                                      <span><b>Servicio Ingreso: </b> '.$servicio_ingre.'</span>
                                                      <br>
                                                      <span><b>Servicio Actual: </b> '.$servicio_actual.'</span>

                                                    </div>
                                                  </div>

                                                  <!-------aqui viene la seccion accidente de transito ------------->
                                                  <!------- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx ------------->

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Hallazgo</label>
                                                    
                                                  </div>


                                                  '.$hallazgo_steps_1.'

                                                  <div class="row">
                                                    
                                                  '.$content_steps_1_p2.'
                                                  '.$content_steps_1_p3.'

                                                  </div>

                                                  '.$hallazgo_2 .'

                                                  <div class="row">
                                                    '.$content_steps_1_p4.'
                                                    '.$content_steps_1_p5.'
                                                    '.$content_steps_1_p6.'
                                                    '.$content_steps_1_p7.'
                                                  </div>

                                                  <div class="row">
                                                    '.$content_steps_1_p8.'
                                                    '.$content_steps_1_p9.'
                                                    '.$content_steps_1_p10.'
                                                  </div>
                                                  '.$content_steps_1_obs_p4.'

                                                  '.$hallazgo_steps_2.'

                                                    '.$content_steps_2_p1.'
                                                    '.$content_steps_2_p2.'
                                                    '.$content_steps_3_p1.'

                                                    '.$hallazgo_steps_3.'

                                                    '.$content_steps_3_p2.'

                                                    '.$hallazgo_steps_5.'

                                                    '.$content_steps_5_p1.'

                                                  <div class="row" style="font-size:9px;" >
                                                    <span>Responsable</span>  
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <span>_________________________</span>
                                                    <br>
                                                    <span><b>'.$autor.'</b></span>
                                                    <br>
                                                    <span>Auditor</span>
                                                  </div>
                                                  
                                              </div>';                                                                         

                    // push single product into final response array
                    array_push($response["resultado"], $datos);
                  }
                  $response["success"] = true;
                }else{
                  $fila = 'No se han encontrado resultados';
                  $response["success"] = false;
                  $response["message"] = "No se encontraron registros";
                  // echo no users JSON
                }
      

    }

  }else{
    $html='No hay conexión';
  }
  $html='
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset="utf-8">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title></title>
              <link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
                <style>
                  * {
                        box-sizing: border-box;
                    }

                    [class*="col-"] {
                        float: left;
                        padding: 0px;
                    }
                    html {
                        font-family: "Lucida Sans", sans-serif ;
                    }
                    body{
                      padding:text-align:justify
                    }
                </style>
            </head>
            <body>
            <div class="container style="text-align:justify;">

              '.$tabla.'

            </div>

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
