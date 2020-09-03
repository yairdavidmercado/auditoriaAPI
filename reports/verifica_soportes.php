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
                                  $aseguradoras as nom_contrato,

                                  CASE ((SELECT MIN(wauditoria_admin.fecha_crea) FROM wauditoria_admin WHERE wauditoria_admin.cod_admi = tadmision.cod_admi) = wauditoria_admin.fecha_crea) 
                                    WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,
                                    CASE tadmision.tipo_enfermedad WHEN 2
                                    THEN 'SI' ELSE 'NO' END AS acc_transito,

                                    (CASE waccidente_transito.steps_1_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p1, 
                                    (CASE waccidente_transito.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p2, 
                                    (CASE waccidente_transito.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p3, 
                                    (CASE waccidente_transito.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p4,  
                                    (CASE waccidente_transito.steps_1_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p5, 
                                    (CASE waccidente_transito.steps_1_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS waccidente_transito_steps_1_p6, 
                                    waccidente_transito.steps_1_obs AS waccidente_transito_steps_1_obs,

                                    (CASE wauditoria_admin.steps_1a_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1a_p1, 
                                    wauditoria_admin.steps_1a_obs_p1 AS steps_1a_obs_p1,
                                    (CASE wauditoria_admin.steps_1_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p1, 
                                    wauditoria_admin.steps_1_obs_p1 AS steps_1_obs_p1,
                                    (CASE wauditoria_admin.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p2,
                                    wauditoria_admin.steps_1_obs_p2 AS steps_1_obs_p2,
                                    (CASE wauditoria_admin.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p3,
                                    wauditoria_admin.steps_1_obs_p3 AS steps_1_obs_p3,
                                    (CASE wauditoria_admin.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p4, 
                                    wauditoria_admin.steps_1_obs_p4 AS steps_1_obs_p4,
                                    (CASE wauditoria_admin.steps_2_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p1, 
                                    wauditoria_admin.steps_2_obs_p1 AS steps_2_obs_p1,
                                    (CASE wauditoria_admin.steps_2_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p2,
                                    wauditoria_admin.steps_2_obs_p2 AS steps_2_obs_p2,
                                    (CASE wauditoria_admin.steps_2_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p3,
                                    wauditoria_admin.steps_2_obs_p3 AS steps_2_obs_p3, 
                                    (CASE wauditoria_admin.steps_2_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p4,
                                    wauditoria_admin.steps_2_obs_p4 AS steps_2_obs_p4,
                                    (CASE wauditoria_admin.steps_2_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p5, 
                                    wauditoria_admin.steps_2_obs_p5 AS steps_2_obs_p5, 
                                    (CASE wauditoria_admin.steps_2_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p6,
                                    wauditoria_admin.steps_2_obs_p6 AS steps_2_obs_p6,
                                    (CASE wauditoria_admin.steps_3_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_3_p1,
                                    wauditoria_admin.steps_3_obs_p1 AS steps_3_obs_p1, 
                                    (CASE wauditoria_admin.steps_4_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p1,
                                    wauditoria_admin.steps_4_obs_p1 AS steps_4_obs_p1, 
                                    (CASE wauditoria_admin.steps_4_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p2,
                                    wauditoria_admin.steps_4_obs_p2 AS steps_4_obs_p2, 
                                    (CASE wauditoria_admin.steps_4_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p3,
                                    wauditoria_admin.steps_4_obs_p3 AS steps_4_obs_p3, 
                                    (CASE wauditoria_admin.steps_4_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p4, 
                                    wauditoria_admin.steps_4_obs_p4 AS steps_4_obs_p4,
                                    (CASE wauditoria_admin.steps_4_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_4_p5, 
                                    wauditoria_admin.steps_4_obs_p5 AS steps_4_obs_p5,
                                    (CASE wauditoria_admin.fin_auditoria WHEN '1' THEN 'SI' WHEN '2' THEN 'NO' END) AS fin_auditoria, 
                                    wauditoria_admin.fin_auditoria_obs AS fin_auditoria_obs,

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
                                  AND wauditoria_admin.cod_usua = 180
                                  ORDER BY wauditoria_admin.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();
                            $waccidente_transito_steps_1_p1   = $row["waccidente_transito_steps_1_p1"];
                            $waccidente_transito_steps_1_p2   = $row["waccidente_transito_steps_1_p2"];
                            $waccidente_transito_steps_1_p3   = $row["waccidente_transito_steps_1_p3"];
                            $waccidente_transito_steps_1_p4   = $row["waccidente_transito_steps_1_p4"];
                            $waccidente_transito_steps_1_p6   = $row["waccidente_transito_steps_1_p6"];
                            $waccidente_transito_steps_1_obs  = $row["waccidente_transito_steps_1_obs"];
                            $steps_1a_p1                      = $row["steps_1a_p1"];
                            $steps_1a_obs_p1                  = nl2br($row["steps_1a_obs_p1"]);
                            $steps_1_p1                       = $row["steps_1_p1"];
                            $steps_1_obs_p1                   = nl2br($row["steps_1_obs_p1"]);
                            $steps_1_p2                       = $row["steps_1_p2"];
                            $steps_1_obs_p2                   = nl2br($row["steps_1_obs_p2"]);
                            $steps_1_p3                       = $row["steps_1_p3"];
                            $steps_1_obs_p3                   = nl2br($row["steps_1_obs_p3"]);
                            $steps_1_p4                       = $row["steps_1_p4"];                            
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_2_p1                       = $row["steps_2_p1"];
                            $steps_2_obs_p1                   = nl2br($row["steps_2_obs_p1"]);
                            $steps_2_p2                       = $row["steps_2_p2"];
                            $steps_2_obs_p2                   = nl2br($row["steps_2_obs_p2"]);
                            $steps_2_p3                       = $row["steps_2_p3"];
                            $steps_2_obs_p3                   = nl2br($row["steps_2_obs_p3"]);
                            $steps_2_p4                       = $row["steps_2_p4"];
                            $steps_2_obs_p4                   = nl2br($row["steps_2_obs_p4"]);
                            $steps_2_p5                       = $row["steps_2_p5"];
                            $steps_2_obs_p5                   = nl2br($row["steps_2_obs_p5"]);
                            $steps_2_p6                       = $row["steps_2_p6"];
                            $steps_2_obs_p6                   = nl2br($row["steps_2_obs_p6"]);
                            $steps_3_p1                       = $row["steps_3_p1"];
                            $steps_3_obs_p1                   = nl2br($row["steps_3_obs_p1"]);
                            $steps_4_p1                       = $row["steps_4_p1"];
                            $steps_4_obs_p1                   = nl2br($row["steps_4_obs_p1"]);
                            $steps_4_p2                       = $row["steps_4_p2"];
                            $steps_4_obs_p2                   = nl2br($row["steps_4_obs_p2"]);
                            $steps_4_p3                       = $row["steps_4_p3"];
                            $steps_4_obs_p3                   = nl2br($row["steps_4_obs_p3"]);
                            $steps_4_p4                       = $row["steps_4_p4"];
                            $steps_4_obs_p4                   = nl2br($row["steps_4_obs_p4"]);
                            $steps_4_p5                       = $row["steps_4_p5"];
                            $steps_4_obs_p5                   = nl2br($row["steps_4_obs_p5"]);
                            $fin_auditoria                    = $row["fin_auditoria"];
                            $fin_auditoria_obs                = nl2br($row["fin_auditoria_obs"]);

                            $hora                             = $row["hora_creacion"];
                            $dia                              = $row["dia_creacion"];
                            $mes                              = strtolower($row["mes_creacion"]);
                            $year                             = $row["year_creacion"];
                            $admision                         = $row["admision"];
                            $n_auditoria                      = $row["n_auditoria"];
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


                            if ($steps_1a_p1 == 'No Cumple') {

                              $hallazgo_steps_1a = '<div class="row">
                                                      <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                      <!-- Add the extra clearfix for only the required viewport -->
                                                      <div class="clearfix visible-xs-block"></div>
                                                    </div>
                                                    <div class="row">
                                                      <label class="text-info" style="font-size:10px;">VERIFICACIÓN DEL PAQUETE ADMINISTRATIVO</label>
                                                    </div>';

                              $content_steps_1a_p1 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                        <span class="text-muted">1. Verificar que exista el paquete administrativo en la Historia clínica.</span>
                                                        <br>
                                                        <span><b>'.$steps_1a_p1.'</b></span>
                                                        <br>
                                                        <span>Observación: '.$steps_1a_obs_p1.'</span>

                                                      </div>';
                            }else{
                              $content_steps_1a_p1 = '';
                              $hallazgo_steps_1a = '';
                            } 




                            if ($steps_1_p1 == 'No Cumple' || $steps_1_p2 == 'No Cumple' || $steps_1_p3 == 'No Cumple' || $steps_1_p4 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">VERIFICACIÓN ATENCIÓN DE URGENCIAS</label>
                                                  </div>';
                              
                            }else{
                              $hallazgo_steps_1 = '';
                            }


                            if ($steps_1_p1 == 'No Cumple') {


                              $content_steps_1_p1 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">2.Verificar que exista el Anexo 2 informando LA ATENCIÓN INICIAL DE URGENCIAS.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p1.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p1 = '';
                            }

                            if ($steps_1_p2 == 'No Cumple') {

                              $content_steps_1_p2 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">3.Verificar que haya sido enviado el Anexo 2 a los correos y portales correspondientes a cada EPS, acorde a lo establecido en la Resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p2.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p2 = '';
                            }

                            if ($steps_1_p3 == 'No Cumple') {


                              $content_steps_1_p3 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">4.Verificar que los envíos del Anexo 2 se encuentren en la historia clínica del paciente.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p3 = '';
                            }

                            if ($steps_1_p4 == 'No Cumple') {

                              $content_steps_1_p4 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">5.Verificar que el seguimiento del Anexo 2 se encuentre enviado según resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p4.'</b></span>
                                                      <br>
                                                      <span>'.$steps_1_obs_p4.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p4 = '';
                            }

                            //---------------------------------------------------------------

                            if ($steps_2_p1 == 'No Cumple' || $steps_2_p2 == 'No Cumple' || $steps_2_p3 == 'No Cumple' || $steps_2_p4 == 'No Cumple' || $steps_2_p5 == 'No Cumple' || $steps_2_p6 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">SOLICITUD DE AUTORIZACIÓN DE SERVICIOS POSTERIORES A LA ATENCIÓN INICIAL DE URGENCIAS</label>
                                                  </div>';
                              
                            }else{
                              $hallazgo_steps_2 = '';
                            }

                            if ($steps_2_p1 == 'No Cumple') {

                              $content_steps_2_p1 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">6.Verificar que exista el Anexo 3 solicitando la AUTORIZACIÓN DE SERVICIOS POSTERIORES A LA ATENCIÓN INICIAL DE URGENCIAS.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p1.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p1.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p1 = '';
                            }

                            if ($steps_2_p2 == 'No Cumple') {

                              $content_steps_2_p2 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">7. Verificar que el o los Anexos 3 hayan sido enviados a los correos y portales correspondientes a cada EPS, acorde a lo establecido en la Resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p2.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p2.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p2 = '';
                            }

                            if ($steps_2_p3 == 'No Cumple') {


                              $content_steps_2_p3 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">8. Verificar que el o los Anexos 3 se encuentren en la historia clínica del paciente.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p3.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p3 = '';
                            }

                            if ($steps_2_p4 == 'No Cumple') {


                              $content_steps_2_p4 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">9. Verificar que el seguimiento del Anexo 3 se encuentre enviado según resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p4.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p4.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p4 = '';
                            }

                            if ($steps_2_p5 == 'No Cumple') {


                              $content_steps_2_p5 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">10. Solicitud de prórroga hospitalaria.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p5.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p5.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p5 = '';
                            }

                            if ($steps_2_p6 == 'No Cumple') {


                              $content_steps_2_p6 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">11. Solicitud de cambio de servicio.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p6.'</b></span>
                                                      <br>
                                                      <span>'.$steps_2_obs_p6.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p6 = '';
                            }

                            //---------------------------------------------------------------

                            if ($steps_3_p1 == 'No Cumple') {

                              $hallazgo_steps_3 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">AUTORIZACIÓN DE SERVICIOS DE SALUD</label>
                                                  </div>';

                              $content_steps_3_p1 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">12. Verificar que exista autorización de servicio (ANEXO 4).</span>
                                                      <br>
                                                      <span><b>'.$steps_3_p1.'</b></span>
                                                      <br>
                                                      <span>'.$steps_3_obs_p1.'</span>

                                                    </div>';
                            }else{
                              $content_steps_3_p1 = '';
                              $hallazgo_steps_3 = '';
                            }


                            //-------------------------------------------------------------

                            if ($steps_4_p1 == 'No Cumple' || $steps_4_p2 == 'No Cumple' || $steps_4_p3 == 'No Cumple' || $steps_4_p4 == 'No Cumple' || $steps_4_p5 == 'No Cumple') {

                              $hallazgo_steps_4 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                    <label class="text-info" style="font-size:10px">AUTORIZACIÓN DE SERVICIOS ADICIONALES</label>
                                                  </div>';

                            }else{
                              $hallazgo_steps_4 = '';
                            }

                            if ($steps_4_p1 == 'No Cumple') {


                              $content_steps_4_p1 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">13. Verificar si existe orden médica de servicios adicionales (materiales quirúrgicos, hospitalización en casa, Cotización de procedimientos especiales) y  hayan sido enviados a los correos y portales correspondientes a cada EPS, acorde a lo establecido en la Resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p1.'</b></span>
                                                      <br>
                                                      <span>'.$steps_4_obs_p1.'</span>

                                                    </div>';
                            }else{
                              $content_steps_4_p1 = '';
                            }

                            if ($steps_4_p2 == 'No Cumple') {

                              $content_steps_4_p2 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">14. Verificar que exista autorización de servicios adicionales(ANEXO 4).</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p2.'</b></span>
                                                      <br>
                                                      <span>'.$steps_4_obs_p2.'</span>

                                                    </div>';
                            }else{
                              $content_steps_4_p2 = '';
                            }

                            if ($steps_4_p3 == 'No Cumple') {


                              $content_steps_4_p3 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">15. Verificar que el o los servicios adicionales se encuentren en la historia clínica del paciente.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p3.'</b></span>
                                                      <br>
                                                      <span>'.$steps_4_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_4_p3 = '';
                            }

                            if ($steps_4_p4 == 'No Cumple') {


                              $content_steps_4_p4 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">16. Verificar que el seguimiento del servicios adicionales se encuentre enviado según resolución 3047.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p4.'</b></span>
                                                      <br>
                                                      <span>'.$steps_4_obs_p4.'</span>

                                                    </div>';
                            }else{
                              $content_steps_4_p4 = '';
                            }

                            if ($steps_4_p5 == 'No Cumple') {


                              $content_steps_4_p5 = '<div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span class="text-muted">17. Verificar que los servicios adicionales se encuentren autorizados por la EPS y repose en la historia clínica.</span>
                                                      <br>
                                                      <span><b>'.$steps_4_p5.'</b></span>
                                                      <br>
                                                      <span>'.$steps_4_obs_p5.'</span>

                                                    </div>';
                            }else{
                              $content_steps_4_p5 = '';
                            }

                            //--------------------------------------------------------------------

                            if ($steps_1a_p1 == 'No Cumple' || $steps_1_p1 == 'No Cumple' || $steps_1_p2 == 'No Cumple' || $steps_1_p3 == 'No Cumple' || $steps_1_p4 == 'No Cumple' || $steps_2_p1 == 'No Cumple' || $steps_2_p2 == 'No Cumple' || $steps_2_p3 == 'No Cumple' || $steps_2_p4 == 'No Cumple' || $steps_2_p5 == 'No Cumple' || $steps_2_p6 == 'No Cumple' || $steps_3_p1 == 'No Cumple' || $steps_4_p1 == 'No Cumple' || $steps_4_p2 == 'No Cumple' || $steps_4_p3 == 'No Cumple' || $steps_4_p4 == 'No Cumple' || $steps_4_p5 == 'No Cumple') {

                            $content          .= '<div class="row" style="background-color:#f9f9fc">
                                                    <div class="col-xs-12 col-sm-3">
                                                      <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>RESULTADO DE AUDITORIA No. '.$n_auditoria.'</b></p>
                                                      <br>
                                                       <span class="pull-right" style="font-size:8px">'.$mes.' '.$dia.' de '.$year.' - '.$hora.'</span>
                                                    </div>
                                                  </div>

                                                  <div>

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


                                                  '.$hallazgo_steps_1a.'
                                                        

                                                  <div class="row">
                                                    '.$content_steps_1a_p1.'
                                                  </div>

                                                  '.$hallazgo_steps_1.'

                                                  <div class="row">
                                                    
                                                  '.$content_steps_1_p1.'
                                                  '.$content_steps_1_p2.'

                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p3.'
                                                    '.$content_steps_1_p4.'
                                                    
                                                  </div>

                                                  '.$hallazgo_steps_2.'

                                                  <div class="row">

                                                    '.$content_steps_2_p1.'
                                                    '.$content_steps_2_p2.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_2_p3.'
                                                    '.$content_steps_2_p4.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_2_p5.'
                                                    '.$content_steps_2_p6.'
                                                    
                                                  </div>

                                                    '.$hallazgo_steps_3.'

                                                  <div class="row">
                                                    
                                                    '.$content_steps_3_p1.'

                                                  </div>

                                                  '.$hallazgo_steps_4.'

                                                  <div class="row">

                                                    '.$content_steps_4_p1.'
                                                    '.$content_steps_4_p2.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_4_p3.'
                                                    '.$content_steps_4_p4.'

                                                  </div>
                                                  <div class="row">
                                                    
                                                    '.$content_steps_4_p5.'                                                    
                                                    
                                                  </div>'; 
                              }else{
                                $content = '';
                              }

                              $tabla            = '<div class="row">
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

                                                  </div>

                                                  '.$content.'

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
                                                    </br>
                                                    </br>
                                                    </br>    
                                                    </br>
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
      $result = pg_query($conn,  "SELECT DISTINCT wauditoria_verifica_soportes.consec AS consec,
                                  tadmision.cod_admi AS admision,
                                  tpaciente.id_pacien,
                                  tpaciente.nom1||' '||tpaciente.nom2||' '||tpaciente.apell1||' '||tpaciente.apell2 AS nom_paciente,
                                  tatiene.num_factu AS numero_factura,
                                  CASE tatiene.num_factu WHEN '' THEN 'NO FACTURADO' ELSE 'FACTURADO' END AS estado,
                                  $aseguradoras as nom_contrato,
                                  tadmision.fecha_ingre::date,
                                  max(tsalidas.fecha_egre)::date as fecha_egreso,
                                  (SELECT MIN(tcaad.fecha_entra) FROM tcaad WHERE tcaad.cod_admi = tadmision.cod_admi)::date AS servicio_ingre_servicio,
                                  CASE tadmision.via_ingreso WHEN '1'
                                  THEN 'CONSULTA EXTERNA O PROGRAMADA'
                                  WHEN '2'
                                  THEN 'URGENCIAS'
                                  WHEN '3'
                                  THEN 'REMITIDO'
                                  WHEN '4'
                                  THEN 'NACIDO EN INSTITUCION' END AS servicio_ingre,
                                  wauditoria_verifica_soportes.servicio AS servicio_actual,
                                  CASE ((SELECT MIN(wauditoria_verifica_soportes.fecha_crea) FROM wauditoria_verifica_soportes WHERE wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi) = wauditoria_verifica_soportes.fecha_crea) 
                                  WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,

                                  (SELECT count(wauditoria_verifica_soportes.cod_admi) FROM wauditoria_verifica_soportes WHERE wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi ) AS count_revision,

                                  (CASE wauditoria_verifica_soportes.steps_1_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p1, 
                                  wauditoria_verifica_soportes.steps_1_obs_p1, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p2, 
                                   wauditoria_verifica_soportes.steps_1_obs_p2, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p3, 
                                   wauditoria_verifica_soportes.steps_1_obs_p3, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p4, 
                                   wauditoria_verifica_soportes.steps_1_obs_p4, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p5, 
                                   wauditoria_verifica_soportes.steps_1_obs_p5, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p6, 
                                   wauditoria_verifica_soportes.steps_1_obs_p6, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p7 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p7, 
                                   wauditoria_verifica_soportes.steps_1_obs_p7, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p8 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p8, 
                                   wauditoria_verifica_soportes.steps_1_obs_p8, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p9 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p9, 
                                   wauditoria_verifica_soportes.steps_1_obs_p9, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p10 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p10, 
                                   wauditoria_verifica_soportes.steps_1_obs_p10, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p11 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p11, 
                                   wauditoria_verifica_soportes.steps_1_obs_p11, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p12 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p12, 
                                   wauditoria_verifica_soportes.steps_1_obs_p12, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p13 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p13, 
                                   wauditoria_verifica_soportes.steps_1_obs_p13, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p14 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p14, 
                                   wauditoria_verifica_soportes.steps_1_obs_p14, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p15 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p15, 
                                   wauditoria_verifica_soportes.steps_1_obs_p15, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p16 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p16, 
                                   wauditoria_verifica_soportes.steps_1_obs_p16, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p17 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p17, 
                                   wauditoria_verifica_soportes.steps_1_obs_p17, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p18 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p18, 
                                   wauditoria_verifica_soportes.steps_1_obs_p18, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p19 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p19, 
                                   wauditoria_verifica_soportes.steps_1_obs_p19, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p20 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p20, 
                                   wauditoria_verifica_soportes.steps_1_obs_p20, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p21 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p21, 
                                   wauditoria_verifica_soportes.steps_1_obs_p21, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p22 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p22, 
                                   wauditoria_verifica_soportes.steps_1_obs_p22, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p23 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p23, 
                                   wauditoria_verifica_soportes.steps_1_obs_p23, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p24 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p24, 
                                   wauditoria_verifica_soportes.steps_1_obs_p24, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p25 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p25, 
                                   wauditoria_verifica_soportes.steps_1_obs_p25, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p26 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p26, 
                                   wauditoria_verifica_soportes.steps_1_obs_p26, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p27 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p27, 
                                   wauditoria_verifica_soportes.steps_1_obs_p27, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p28 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p28, 
                                   wauditoria_verifica_soportes.steps_1_obs_p28, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p29 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p29, 
                                   wauditoria_verifica_soportes.steps_1_obs_p29, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p30 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p30, 
                                   wauditoria_verifica_soportes.steps_1_obs_p30, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p1  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p1,
                                   wauditoria_verifica_soportes.steps_2_obs_p1, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p2  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p2,
                                   wauditoria_verifica_soportes.steps_2_obs_p2, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p3  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p3,
                                   wauditoria_verifica_soportes.steps_2_obs_p3,

                                  (SELECT nom_usua FROM tusuario WHERE tusuario.cod_usua = wauditoria_verifica_soportes.cod_usua) AS autor,
                                  wauditoria_verifica_soportes.fecha_crea AS fecha_creacion,
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'HH24:MI') AS hora_creacion,
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'DD') AS dia_creacion,
                                  CASE to_char(wauditoria_verifica_soportes.fecha_crea, 'MM')
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
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'YYYY') AS year_creacion,
                                  (CASE wauditoria_verifica_soportes.anulado WHEN 't' THEN 'NO' WHEN 'f' THEN 'SI' END) AS anulado
                                  FROM tadmision 
                                  INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                  INNER JOIN wauditoria_verifica_soportes ON wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi
                                  FULL JOIN waccidente_transito ON waccidente_transito.cod_admi = tadmision.cod_admi
                                  INNER JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                  INNER JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra 
                                  INNER JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad
                                  LEFT JOIN tsalidas ON tsalidas.cod_admi = tadmision.cod_admi 
                                  WHERE tatiene.primer_admi
                                  AND wauditoria_verifica_soportes.consec = ".$parametro."
                                  GROUP BY wauditoria_verifica_soportes.consec, 
                                  tadmision.cod_admi, 
                                  tpaciente.nom1, 
                                  tpaciente.nom2, 
                                  tpaciente.apell1, 
                                  tpaciente.apell2, 
                                  tpaciente.id_pacien, 
                                  tentidad.id_ase,
                                  tatiene.num_factu
                                  ORDER BY wauditoria_verifica_soportes.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();
                            
                            $steps_1_p1                       = $row["steps_1_p1"];
                            $steps_1_obs_p1                   = nl2br($row["steps_1_obs_p1"]);
                            $steps_1_p2                       = $row["steps_1_p2"];
                            $steps_1_obs_p2                   = nl2br($row["steps_1_obs_p2"]);
                            $steps_1_p3                       = $row["steps_1_p3"];
                            $steps_1_obs_p3                   = nl2br($row["steps_1_obs_p3"]);
                            $steps_1_p4                       = $row["steps_1_p4"];                            
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_1_p4                       = $row["steps_1_p4"];                            
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_1_p5                       = $row["steps_1_p5"];                            
                            $steps_1_obs_p5                   = nl2br($row["steps_1_obs_p5"]);
                            $steps_1_p6                       = $row["steps_1_p6"];                            
                            $steps_1_obs_p6                   = nl2br($row["steps_1_obs_p6"]);
                            $steps_1_p7                       = $row["steps_1_p7"];                            
                            $steps_1_obs_p7                   = nl2br($row["steps_1_obs_p7"]);
                            $steps_1_p8                       = $row["steps_1_p8"];                            
                            $steps_1_obs_p8                   = nl2br($row["steps_1_obs_p8"]);
                            $steps_1_p9                       = $row["steps_1_p9"];                            
                            $steps_1_obs_p9                   = nl2br($row["steps_1_obs_p9"]);
                            $steps_1_p10                       = $row["steps_1_p10"];                            
                            $steps_1_obs_p10                   = nl2br($row["steps_1_obs_p10"]);
                            $steps_1_p11                       = $row["steps_1_p11"];                            
                            $steps_1_obs_p11                   = nl2br($row["steps_1_obs_p11"]);
                            $steps_1_p12                       = $row["steps_1_p12"];                            
                            $steps_1_obs_p12                   = nl2br($row["steps_1_obs_p12"]);
                            $steps_1_p13                       = $row["steps_1_p13"];                            
                            $steps_1_obs_p13                   = nl2br($row["steps_1_obs_p13"]);
                            $steps_1_p14                       = $row["steps_1_p14"];                            
                            $steps_1_obs_p14                   = nl2br($row["steps_1_obs_p14"]);
                            $steps_1_p15                       = $row["steps_1_p15"];                            
                            $steps_1_obs_p15                   = nl2br($row["steps_1_obs_p15"]);
                            $steps_1_p16                       = $row["steps_1_p16"];                            
                            $steps_1_obs_p16                   = nl2br($row["steps_1_obs_p16"]);
                            $steps_1_p17                       = $row["steps_1_p17"];                            
                            $steps_1_obs_p17                   = nl2br($row["steps_1_obs_p17"]);
                            $steps_1_p18                       = $row["steps_1_p18"];                            
                            $steps_1_obs_p18                   = nl2br($row["steps_1_obs_p18"]);
                            $steps_1_p19                       = $row["steps_1_p19"];                            
                            $steps_1_obs_p19                   = nl2br($row["steps_1_obs_p19"]);
                            $steps_1_p20                       = $row["steps_1_p20"];                            
                            $steps_1_obs_p20                   = nl2br($row["steps_1_obs_p20"]);
                            $steps_1_p21                       = $row["steps_1_p21"];                            
                            $steps_1_obs_p21                   = nl2br($row["steps_1_obs_p21"]);
                            $steps_1_p22                       = $row["steps_1_p22"];                            
                            $steps_1_obs_p22                   = nl2br($row["steps_1_obs_p22"]);
                            $steps_1_p23                       = $row["steps_1_p23"];                            
                            $steps_1_obs_p23                   = nl2br($row["steps_1_obs_p23"]);
                            $steps_1_p24                       = $row["steps_1_p24"];                            
                            $steps_1_obs_p24                   = nl2br($row["steps_1_obs_p24"]);
                            $steps_1_p25                       = $row["steps_1_p25"];                            
                            $steps_1_obs_p25                   = nl2br($row["steps_1_obs_p25"]);
                            $steps_1_p26                       = $row["steps_1_p26"];                            
                            $steps_1_obs_p26                   = nl2br($row["steps_1_obs_p26"]);
                            $steps_1_p27                       = $row["steps_1_p27"];                            
                            $steps_1_obs_p27                   = nl2br($row["steps_1_obs_p27"]);
                            $steps_1_p28                       = $row["steps_1_p28"];                            
                            $steps_1_obs_p28                   = nl2br($row["steps_1_obs_p28"]);
                            $steps_1_p29                       = $row["steps_1_p29"];                            
                            $steps_1_obs_p29                   = nl2br($row["steps_1_obs_p29"]);
                            $steps_1_p30                       = $row["steps_1_p30"];                            
                            $steps_1_obs_p30                   = nl2br($row["steps_1_obs_p30"]);

                            $steps_2_p1                       = $row["steps_2_p1"];
                            $steps_2_obs_p1                   = nl2br($row["steps_2_obs_p1"]);
                            $steps_2_p2                       = $row["steps_2_p2"];
                            $steps_2_obs_p2                   = nl2br($row["steps_2_obs_p2"]);
                            $steps_2_p3                       = $row["steps_2_p3"];
                            $steps_2_obs_p3                   = nl2br($row["steps_2_obs_p3"]);

                            $hora                             = $row["hora_creacion"];
                            $dia                              = $row["dia_creacion"];
                            $mes                              = strtolower($row["mes_creacion"]);
                            $year                             = $row["year_creacion"];
                            $admision                         = $row["admision"];
                            $id_pacien                        = $row["id_pacien"];
                            $nom_contrato                     = $row["nom_contrato"];
                            $fecha_ingre                      = $row["fecha_ingre"];
                            $fecha_egreso                     = $row["fecha_egreso"];
                            $estado                           = $row["estado"];
                            $numero_factura                   = $row["numero_factura"];
                            $servicio_ingre                   = $row["servicio_ingre"];
                            $servicio_actual                  = $row["servicio_actual"];
                            $acc_transito                     = $row["acc_transito"];
                            $nom_paciente                     = $row["nom_paciente"];
                            $revision                         = $row["revision"];
                            $autor                            = $row["autor"];


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
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>VERIFICACIÓN DE SOPORTES</b></p>
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
                                                      <span><b>EPS: </b>'.$nom_contrato.'</span>
                                                      <br>
                                                      <span><b>Estado: </b>'.$estado.'</span>
                                                      <br>
                                                      <span><b>Servicio Actual: </b> '.$servicio_actual.'</span>

                                                    </div>
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Admisión: </b>'.$admision.'</span>
                                                      <br>
                                                      <span><b>Fecha Ingreso: </b>'.$fecha_ingre.'</span>
                                                      <br>
                                                      <span><b>Fecha Egreso: </b> '.$fecha_egreso.'</span>
                                                      <br>
                                                      <span><b>Número de Factura: </b>'.$numero_factura.'</span>
                                                      <br>
                                                      <span><b>Servicio Ingreso: </b> '.$servicio_ingre.'</span>

                                                    </div>
                                                  </div>

                                                    '.$content_accidente.'

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Auditoría</label>
                                                    
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1. Verificar que exista el paquete administrativo en la Historia clínica.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p1.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.2 - Admisión del paciente (Obligatoria)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p2.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.3 - Que tenga Autorización del servicio prestado (Contrato)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p3.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.4 - Verificar que tenga Anexos técnicos y sus envíos que cubran el servicio prestado</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p4.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p4.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.5 - Verificar que los Documentos de atención de Accidentes de Transito estén completos( SOAT y FURRIPS.)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p5.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p5.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.6 - Registro de Triage (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p6.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p6.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.7 - Historia clínica de urgencias (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p7.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p7.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.8 - Ingreso de internación hospitalaria</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p8.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p8.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.9 - Ordenes medicas desde el ingreso hasta el egreso (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p9.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p9.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.10 - Evoluciones medicas desde el ingreso hasta el egreso (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p10.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p10.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.11 - Interconsultas realizadas durante la estancia</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p11.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p11.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.12 - Evoluciones de terapias físicas, respiratorias y nebulizaciones</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p12.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p12.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.13 - Declaración de consentimiento informado (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p13.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p13.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.14 - Valoración pre-quirúrgica (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p14.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p14.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.15 - Valoración pre-anestésica (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p15.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p15.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.16 - Record de anestesia (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p16.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p16.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.17 - Informe quirúrgico (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p17.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p17.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.18 - Hoja de gasto quirúrgico (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p18.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p18.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.19 - Justificación de tecnologías NO POS</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p19.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p19.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.20 - Acta de aprobación de tecnologías NO POS o los soportes de la radicación de la solicitud de la aprobación (Obligatorio si hay tecnologías NO POS)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p20.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p20.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.21 - Registro de transfusión sanguínea</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p21.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p21.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.22 - Reporte (lectura) de imágenes diagnosticas</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p22.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p22.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.23 - Reporte de laboratorios clínicos</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p23.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p23.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.24 - Reporte de otros estudios de apoyo diagnósticos especializados</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p24.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p24.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.25 - Reporte de administración de medicamentos (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p25.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p25.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.26 - Reporte de utilización de dispositivos médicos e insumos (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p26.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p26.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.27 - Reporte de administración de oxigeno</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p27.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p27.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.28 - Hoja de traslado de ambulancia y remisión si fue remitido el paciente</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p28.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p28.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.29 - Epicrísis completa (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p29.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p29.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.30 - Orden de salida administrativa(Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p30.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p30.'</span>

                                                    </div>
                                                  </div>

                                                  <div class="row">
                                                    <div style="background-color:#137DED; height:1.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>

                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">2. REVISIÓN DE MEDICAMENTO DE ALTO COSTO Y NO POS (ORDENADOS V.S REPORTADOS)</label>
                                                  </div>

                                                  <div class="row">
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.1 - Verificar que los medicamentos facturados estén debidamente soportados en el registro de medicamentos, y descritos en las órdenes médicas.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p1.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.2 - Si se ordenan medicamentos NO POS, verificar que el soporte del acta de aprobación del CTC se encuentre adjunta a la factura.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p2.'</span>

                                                    </div>
                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.3 - De acuerdo con la resolución 1479 de 2015 la factura del homologo y la factura de la diferencia del NO POS, deben presentarse conjuntamente y a nombre de la EPS y el ente territorial respectivamente.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p3.'</span>

                                                    </div>
                                                  </div>
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
      $result = pg_query($conn,  "SELECT DISTINCT wauditoria_verifica_soportes.consec AS consec,
                                  tadmision.cod_admi AS admision,
                                  tpaciente.id_pacien,
                                  tpaciente.nom1||' '||tpaciente.nom2||' '||tpaciente.apell1||' '||tpaciente.apell2 AS nom_paciente,
                                  tatiene.num_factu AS numero_factura,
                                  CASE tatiene.num_factu WHEN '' THEN 'NO FACTURADO' ELSE 'FACTURADO' END AS estado,
                                  $aseguradoras as nom_contrato,
                                  tadmision.fecha_ingre::date,
                                  max(tsalidas.fecha_egre)::date as fecha_egreso,
                                  (SELECT MIN(tcaad.fecha_entra) FROM tcaad WHERE tcaad.cod_admi = tadmision.cod_admi)::date AS servicio_ingre_servicio,
                                  CASE tadmision.via_ingreso WHEN '1'
                                  THEN 'CONSULTA EXTERNA O PROGRAMADA'
                                  WHEN '2'
                                  THEN 'URGENCIAS'
                                  WHEN '3'
                                  THEN 'REMITIDO'
                                  WHEN '4'
                                  THEN 'NACIDO EN INSTITUCION' END AS servicio_ingre,
                                  wauditoria_verifica_soportes.servicio AS servicio_actual,
                                  CASE ((SELECT MIN(wauditoria_verifica_soportes.fecha_crea) FROM wauditoria_verifica_soportes WHERE wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi) = wauditoria_verifica_soportes.fecha_crea) 
                                  WHEN true THEN 'PRIMERA VEZ' ELSE 'SEGUIMIENTO' END revision,

                                  (SELECT count(wauditoria_verifica_soportes.cod_admi) FROM wauditoria_verifica_soportes WHERE wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi ) AS count_revision,

                                  (CASE wauditoria_verifica_soportes.steps_1_p1 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p1, 
                                  wauditoria_verifica_soportes.steps_1_obs_p1, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p2 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p2, 
                                   wauditoria_verifica_soportes.steps_1_obs_p2, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p3 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p3, 
                                   wauditoria_verifica_soportes.steps_1_obs_p3, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p4 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p4, 
                                   wauditoria_verifica_soportes.steps_1_obs_p4, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p5 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p5, 
                                   wauditoria_verifica_soportes.steps_1_obs_p5, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p6 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p6, 
                                   wauditoria_verifica_soportes.steps_1_obs_p6, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p7 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p7, 
                                   wauditoria_verifica_soportes.steps_1_obs_p7, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p8 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p8, 
                                   wauditoria_verifica_soportes.steps_1_obs_p8, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p9 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p9, 
                                   wauditoria_verifica_soportes.steps_1_obs_p9, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p10 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p10, 
                                   wauditoria_verifica_soportes.steps_1_obs_p10, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p11 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p11, 
                                   wauditoria_verifica_soportes.steps_1_obs_p11, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p12 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p12, 
                                   wauditoria_verifica_soportes.steps_1_obs_p12, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p13 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p13, 
                                   wauditoria_verifica_soportes.steps_1_obs_p13, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p14 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p14, 
                                   wauditoria_verifica_soportes.steps_1_obs_p14, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p15 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p15, 
                                   wauditoria_verifica_soportes.steps_1_obs_p15, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p16 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p16, 
                                   wauditoria_verifica_soportes.steps_1_obs_p16, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p17 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p17, 
                                   wauditoria_verifica_soportes.steps_1_obs_p17, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p18 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p18, 
                                   wauditoria_verifica_soportes.steps_1_obs_p18, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p19 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p19, 
                                   wauditoria_verifica_soportes.steps_1_obs_p19, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p20 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p20, 
                                   wauditoria_verifica_soportes.steps_1_obs_p20, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p21 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p21, 
                                   wauditoria_verifica_soportes.steps_1_obs_p21, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p22 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p22, 
                                   wauditoria_verifica_soportes.steps_1_obs_p22, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p23 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p23, 
                                   wauditoria_verifica_soportes.steps_1_obs_p23, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p24 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p24, 
                                   wauditoria_verifica_soportes.steps_1_obs_p24, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p25 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p25, 
                                   wauditoria_verifica_soportes.steps_1_obs_p25, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p26 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p26, 
                                   wauditoria_verifica_soportes.steps_1_obs_p26, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p27 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p27, 
                                   wauditoria_verifica_soportes.steps_1_obs_p27, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p28 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p28, 
                                   wauditoria_verifica_soportes.steps_1_obs_p28, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p29 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p29, 
                                   wauditoria_verifica_soportes.steps_1_obs_p29, 
                                   (CASE wauditoria_verifica_soportes.steps_1_p30 WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_1_p30, 
                                   wauditoria_verifica_soportes.steps_1_obs_p30, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p1  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p1,
                                   wauditoria_verifica_soportes.steps_2_obs_p1, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p2  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p2,
                                   wauditoria_verifica_soportes.steps_2_obs_p2, 
                                   (CASE wauditoria_verifica_soportes.steps_2_p3  WHEN '1' THEN 'Cumple' WHEN '2' THEN 'No Cumple' WHEN '3' THEN 'No Aplica' END) AS steps_2_p3,
                                   wauditoria_verifica_soportes.steps_2_obs_p3,

                                  (SELECT nom_usua FROM tusuario WHERE tusuario.cod_usua = wauditoria_verifica_soportes.cod_usua) AS autor,
                                  wauditoria_verifica_soportes.fecha_crea AS fecha_creacion,
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'HH24:MI') AS hora_creacion,
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'DD') AS dia_creacion,
                                  CASE to_char(wauditoria_verifica_soportes.fecha_crea, 'MM')
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
                                  to_char(wauditoria_verifica_soportes.fecha_crea, 'YYYY') AS year_creacion,
                                  (CASE wauditoria_verifica_soportes.anulado WHEN 't' THEN 'NO' WHEN 'f' THEN 'SI' END) AS anulado,
                                  (SELECT tusuario.nom_usua FROM tusuario INNER JOIN wdevoluciones ON tusuario.cod_usua = wdevoluciones.cod_usua 
                                  ORDER BY consec DESC LIMIT 1) AS responsable
                                  FROM tadmision 
                                  INNER JOIN tpaciente ON tadmision.cod_pacien = tpaciente.cod_pacien
                                  INNER JOIN wauditoria_verifica_soportes ON wauditoria_verifica_soportes.cod_admi = tadmision.cod_admi
                                  FULL JOIN waccidente_transito ON waccidente_transito.cod_admi = tadmision.cod_admi
                                  INNER JOIN tatiene ON tadmision.cod_admi = tatiene.cod_admi
                                  INNER JOIN tcontrato ON tatiene.cod_contra = tcontrato.cod_contra 
                                  INNER JOIN tentidad ON tcontrato.codentidad = tentidad.codentidad
                                  LEFT JOIN tsalidas ON tsalidas.cod_admi = tadmision.cod_admi 
                                  WHERE tatiene.primer_admi
                                  AND wauditoria_verifica_soportes.consec = ".$parametro."
                                  GROUP BY wauditoria_verifica_soportes.consec, 
                                  tadmision.cod_admi, 
                                  tpaciente.nom1, 
                                  tpaciente.nom2, 
                                  tpaciente.apell1, 
                                  tpaciente.apell2, 
                                  tpaciente.id_pacien, 
                                  tentidad.id_ase,
                                  tatiene.num_factu
                                  ORDER BY wauditoria_verifica_soportes.fecha_crea DESC;");

      if (pg_num_rows($result) > 0)
                {
                  $response["resultado"] = array();
                  while ($row = pg_fetch_array($result)) {
                    $datos = array();
                            $steps_1_p1                       = $row["steps_1_p1"];
                            $steps_1_obs_p1                   = nl2br($row["steps_1_obs_p1"]);
                            $steps_1_p2                       = $row["steps_1_p2"];
                            $steps_1_obs_p2                   = nl2br($row["steps_1_obs_p2"]);
                            $steps_1_p3                       = $row["steps_1_p3"];
                            $steps_1_obs_p3                   = nl2br($row["steps_1_obs_p3"]);
                            $steps_1_p4                       = $row["steps_1_p4"];                            
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_1_p4                       = $row["steps_1_p4"];                            
                            $steps_1_obs_p4                   = nl2br($row["steps_1_obs_p4"]);
                            $steps_1_p5                       = $row["steps_1_p5"];                            
                            $steps_1_obs_p5                   = nl2br($row["steps_1_obs_p5"]);
                            $steps_1_p6                       = $row["steps_1_p6"];                            
                            $steps_1_obs_p6                   = nl2br($row["steps_1_obs_p6"]);
                            $steps_1_p7                       = $row["steps_1_p7"];                            
                            $steps_1_obs_p7                   = nl2br($row["steps_1_obs_p7"]);
                            $steps_1_p8                       = $row["steps_1_p8"];                            
                            $steps_1_obs_p8                   = nl2br($row["steps_1_obs_p8"]);
                            $steps_1_p9                       = $row["steps_1_p9"];                            
                            $steps_1_obs_p9                   = nl2br($row["steps_1_obs_p9"]);
                            $steps_1_p10                       = $row["steps_1_p10"];                            
                            $steps_1_obs_p10                   = nl2br($row["steps_1_obs_p10"]);
                            $steps_1_p11                       = $row["steps_1_p11"];                            
                            $steps_1_obs_p11                   = nl2br($row["steps_1_obs_p11"]);
                            $steps_1_p12                       = $row["steps_1_p12"];                            
                            $steps_1_obs_p12                   = nl2br($row["steps_1_obs_p12"]);
                            $steps_1_p13                       = $row["steps_1_p13"];                            
                            $steps_1_obs_p13                   = nl2br($row["steps_1_obs_p13"]);
                            $steps_1_p14                       = $row["steps_1_p14"];                            
                            $steps_1_obs_p14                   = nl2br($row["steps_1_obs_p14"]);
                            $steps_1_p15                       = $row["steps_1_p15"];                            
                            $steps_1_obs_p15                   = nl2br($row["steps_1_obs_p15"]);
                            $steps_1_p16                       = $row["steps_1_p16"];                            
                            $steps_1_obs_p16                   = nl2br($row["steps_1_obs_p16"]);
                            $steps_1_p17                       = $row["steps_1_p17"];                            
                            $steps_1_obs_p17                   = nl2br($row["steps_1_obs_p17"]);
                            $steps_1_p18                       = $row["steps_1_p18"];                            
                            $steps_1_obs_p18                   = nl2br($row["steps_1_obs_p18"]);
                            $steps_1_p19                       = $row["steps_1_p19"];                            
                            $steps_1_obs_p19                   = nl2br($row["steps_1_obs_p19"]);
                            $steps_1_p20                       = $row["steps_1_p20"];                            
                            $steps_1_obs_p20                   = nl2br($row["steps_1_obs_p20"]);
                            $steps_1_p21                       = $row["steps_1_p21"];                            
                            $steps_1_obs_p21                   = nl2br($row["steps_1_obs_p21"]);
                            $steps_1_p22                       = $row["steps_1_p22"];                            
                            $steps_1_obs_p22                   = nl2br($row["steps_1_obs_p22"]);
                            $steps_1_p23                       = $row["steps_1_p23"];                            
                            $steps_1_obs_p23                   = nl2br($row["steps_1_obs_p23"]);
                            $steps_1_p24                       = $row["steps_1_p24"];                            
                            $steps_1_obs_p24                   = nl2br($row["steps_1_obs_p24"]);
                            $steps_1_p25                       = $row["steps_1_p25"];                            
                            $steps_1_obs_p25                   = nl2br($row["steps_1_obs_p25"]);
                            $steps_1_p26                       = $row["steps_1_p26"];                            
                            $steps_1_obs_p26                   = nl2br($row["steps_1_obs_p26"]);
                            $steps_1_p27                       = $row["steps_1_p27"];                            
                            $steps_1_obs_p27                   = nl2br($row["steps_1_obs_p27"]);
                            $steps_1_p28                       = $row["steps_1_p28"];                            
                            $steps_1_obs_p28                   = nl2br($row["steps_1_obs_p28"]);
                            $steps_1_p29                       = $row["steps_1_p29"];                            
                            $steps_1_obs_p29                   = nl2br($row["steps_1_obs_p29"]);
                            $steps_1_p30                       = $row["steps_1_p30"];                            
                            $steps_1_obs_p30                   = nl2br($row["steps_1_obs_p30"]);

                            $steps_2_p1                       = $row["steps_2_p1"];
                            $steps_2_obs_p1                   = nl2br($row["steps_2_obs_p1"]);
                            $steps_2_p2                       = $row["steps_2_p2"];
                            $steps_2_obs_p2                   = nl2br($row["steps_2_obs_p2"]);
                            $steps_2_p3                       = $row["steps_2_p3"];
                            $steps_2_obs_p3                   = nl2br($row["steps_2_obs_p3"]);

                            $hora                             = $row["hora_creacion"];
                            $dia                              = $row["dia_creacion"];
                            $mes                              = strtolower($row["mes_creacion"]);
                            $year                             = $row["year_creacion"];
                            $admision                         = $row["admision"];
                            $id_pacien                        = $row["id_pacien"];
                            $nom_contrato                     = $row["nom_contrato"];
                            $fecha_ingre                      = $row["fecha_ingre"];
                            $fecha_egreso                     = $row["fecha_egreso"];
                            $estado                           = $row["estado"];
                            $numero_factura                   = $row["numero_factura"];
                            $servicio_ingre                   = $row["servicio_ingre"];
                            $servicio_actual                  = $row["servicio_actual"];
                            $acc_transito                     = $row["acc_transito"];
                            $nom_paciente                     = $row["nom_paciente"];
                            $revision                         = $row["revision"];
                            $autor                            = $row["autor"];
                            $responsable                      = $row["responsable"];



                            if ($steps_1_p1 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p1 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1. Verificar que exista el paquete administrativo en la Historia clínica.</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p1.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p1 = '';
                            }

                            if ($steps_1_p2 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p2 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.2 - Admisión del paciente (Obligatoria)</span>
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
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p3 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.3 - Que tenga Autorización del servicio prestado (Contrato)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p3 = '';
                            }

                            if ($steps_1_p4 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p4 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.4 - Verificar que tenga Anexos técnicos y sus envíos que cubran el servicio prestado</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p4.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p4.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p4 = '';
                            }

                            if ($steps_1_p5 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p5 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.5 - Verificar que los Documentos de atención de Accidentes de Transito estén completos( SOAT y FURRIPS.)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p5.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p5.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p5 = '';
                            }

                            if ($steps_1_p6 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p6 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.6 - Registro de Triage (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p6.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p6.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p6 = '';
                            }

                            if ($steps_1_p7 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p7 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.7 - Historia clínica de urgencias (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p7.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p7.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p7 = '';
                            }

                            if ($steps_1_p8 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p8 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.8 - Ingreso de internación hospitalaria</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p8.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p8.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p8 = '';
                            }

                            if ($steps_1_p9 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p9 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.9 - Ordenes medicas desde el ingreso hasta el egreso (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p9.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p9.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p9 = '';
                            }

                            if ($steps_1_p10 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p10 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.10 - Evoluciones medicas desde el ingreso hasta el egreso (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p10.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p10.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p10 = '';
                            }

                            if ($steps_1_p11 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p11 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.11 - Interconsultas realizadas durante la estancia</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p11.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p11.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p11 = '';
                            }

                            if ($steps_1_p12 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p12 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.12 - Evoluciones de terapias físicas, respiratorias y nebulizaciones</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p12.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p12.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p12 = '';
                            }

                            if ($steps_1_p13 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p13 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.13 - Declaración de consentimiento informado (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p13.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p13.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p13 = '';
                            }

                            if ($steps_1_p14 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p14 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.14 - Valoración pre-quirúrgica (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p14.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p14.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p14 = '';
                            }

                            if ($steps_1_p15 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p15 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.15 - Valoración pre-anestésica (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p15.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p15.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p15 = '';
                            }

                            if ($steps_1_p16 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p16 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.16 - Record de anestesia (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p16.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p16.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p16 = '';
                            }

                            if ($steps_1_p17 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p17 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.17 - Informe quirúrgico (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p17.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p17.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p17 = '';
                            }


                            if ($steps_1_p18 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p18 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.18 - Hoja de gasto quirúrgico (Obligatorio si hay procedimientos)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p18.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p18.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p18 = '';
                            }

                            if ($steps_1_p19 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p19 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.19 - Justificación de tecnologías NO POS</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p19.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p19.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p19 = '';
                            }

                            if ($steps_1_p20 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p20 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.20 - Acta de aprobación de tecnologías NO POS o los soportes de la radicación de la solicitud de la aprobación (Obligatorio si hay tecnologías NO POS)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p20.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p20.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p20 = '';
                            }

                            if ($steps_1_p21 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p21 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.21 - Registro de transfusión sanguínea</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p21.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p21.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p21 = '';
                            }

                            if ($steps_1_p22 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p22 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.22 - Reporte (lectura) de imágenes diagnosticas</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p22.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p22.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p22 = '';
                            }

                            if ($steps_1_p23 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p23 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.23 - Reporte de laboratorios clínicos</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p23.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p23.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p23 = '';
                            }

                            if ($steps_1_p24 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p24 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.24 - Reporte de otros estudios de apoyo diagnósticos especializados</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p24.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p24.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p24 = '';
                            }

                            if ($steps_1_p25 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p25 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.25 - Reporte de administración de medicamentos (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p25.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p25.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p25 = '';
                            }

                            if ($steps_1_p26 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p26 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.26 - Reporte de utilización de dispositivos médicos e insumos (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p26.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p26.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p26 = '';
                            }

                            if ($steps_1_p27 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p27 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.27 - Reporte de administración de oxigeno</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p27.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p27.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p27 = '';
                            }

                            if ($steps_1_p28 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p28 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.28 - Hoja de traslado de ambulancia y remisión si fue remitido el paciente</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p28.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p28.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p28 = '';
                            }

                            if ($steps_1_p29 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p29 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.29 - Epicrísis completa (Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p29.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p29.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p29 = '';
                            }

                            if ($steps_1_p30 == 'No Cumple') {

                              $hallazgo_steps_1 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                  <label class="text-info" style="font-size:10px">1. REVISIÓN DE SOPORTES COMPARANDO LO ORDENADO VERSUS LO FACTURADO</label>
                                                  </div>';

                              $content_steps_1_p30 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">1.30 - Orden de salida administrativa(Obligatorio)</span>
                                                      <br>
                                                      <span><b>'.$steps_1_p30.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_1_obs_p30.'</span>

                                                    </div>';
                            }else{
                              $content_steps_1_p30 = '';
                            }

                            //---------------------------------------------------------------

                            if ($steps_2_p1 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">2. REVISIÓN DE MEDICAMENTO DE ALTO COSTO Y NO POS (ORDENADOS V.S REPORTADOS)</label>
                                                  </div';

                              $content_steps_2_p1 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.1 - Verificar que los medicamentos facturados estén debidamente soportados en el registro de medicamentos, y descritos en las órdenes médicas.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p1.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p1.'</span>

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
                                                        <label class="text-info" style="font-size:10px;">2. REVISIÓN DE MEDICAMENTO DE ALTO COSTO Y NO POS (ORDENADOS V.S REPORTADOS)</label>
                                                  </div';

                              $content_steps_2_p2 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.2 - Si se ordenan medicamentos NO POS, verificar que el soporte del acta de aprobación del CTC se encuentre adjunta a la factura.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p2.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p2.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p2 = '';
                            }

                            if ($steps_2_p3 == 'No Cumple') {

                              $hallazgo_steps_2 = '<div class="row">
                                                    <div style="background-color:#137DED; height:2.5px;" class="col-xs-12 col-sm-3"></div>
                                                    <!-- Add the extra clearfix for only the required viewport -->
                                                    <div class="clearfix visible-xs-block"></div>
                                                  </div>
                                                  <div class="row">
                                                        <label class="text-info" style="font-size:10px;">2. REVISIÓN DE MEDICAMENTO DE ALTO COSTO Y NO POS (ORDENADOS V.S REPORTADOS)</label>
                                                  </div';

                              $content_steps_2_p3 = '<div style=" font-size:9px; padding:2.5px" class="col-xs-3 col-sm-3">

                                                      <span class="text-muted">2.3 - De acuerdo con la resolución 1479 de 2015 la factura del homologo y la factura de la diferencia del NO POS, deben presentarse conjuntamente y a nombre de la EPS y el ente territorial respectivamente.</span>
                                                      <br>
                                                      <span><b>'.$steps_2_p3.'</b></span>
                                                      <br>
                                                      <span>Observación: '.$steps_2_obs_p3.'</span>

                                                    </div>';
                            }else{
                              $content_steps_2_p3 = '';
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
                                                    <p class="font-weight-bold text-info pull-right" style="font-size:10px"><b>VERIFICACIÓN DE SOPORTES</b></p>
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
                                                      <span><b>EPS: </b>'.$nom_contrato.'</span>
                                                      <br>
                                                      <span><b>Estado: </b>'.$estado.'</span>
                                                      <br>
                                                      <span><b>Servicio Actual: </b> '.$servicio_actual.'</span>

                                                    </div>
                                                    
                                                    <div style="font-size:9px; padding:2.5px" class="col-xs-6 col-sm-3">

                                                      <span><b>Admisión: </b>'.$admision.'</span>
                                                      <br>
                                                      <span><b>Fecha Ingreso: </b>'.$fecha_ingre.'</span>
                                                      <br>
                                                      <span><b>Fecha Egreso: </b> '.$fecha_egreso.'</span>
                                                      <br>
                                                      <span><b>Número de Factura: </b>'.$numero_factura.'</span>
                                                      <br>
                                                      <span><b>Servicio Ingreso: </b> '.$servicio_ingre.'</span>

                                                    </div>
                                                  </div>

                                                  <!-------aqui viene la seccion accidente de transito ------------->
                                                  <!------- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx ------------->

                                                  <div class="row" style="background-color:#f9f9fc">
                                                    <label class="text-info" style="font-size:10px">Hallazgo</label>
                                                    
                                                  </div>

                                                  '.$hallazgo_steps_1.'

                                                  <div class="row">
                                                    
                                                  '.$content_steps_1_p1.'
                                                  '.$content_steps_1_p2.'
                                                  '.$content_steps_1_p3.'
                                                  '.$content_steps_1_p4.'

                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p5.'
                                                    '.$content_steps_1_p6.'
                                                    '.$content_steps_1_p7.'
                                                    '.$content_steps_1_p8.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p9.'
                                                    '.$content_steps_1_p10.'
                                                    '.$content_steps_1_p11.'
                                                    '.$content_steps_1_p12.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p13.'
                                                    '.$content_steps_1_p14.'
                                                    '.$content_steps_1_p15.'
                                                    '.$content_steps_1_p16.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p17.'
                                                    '.$content_steps_1_p18.'
                                                    '.$content_steps_1_p19.'
                                                    '.$content_steps_1_p20.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p21.'
                                                    '.$content_steps_1_p22.'
                                                    '.$content_steps_1_p23.'
                                                    '.$content_steps_1_p24.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p25.'
                                                    '.$content_steps_1_p26.'
                                                    '.$content_steps_1_p27.'
                                                    '.$content_steps_1_p28.'
                                                    
                                                  </div>

                                                  <div class="row">

                                                    '.$content_steps_1_p29.'
                                                    '.$content_steps_1_p30.'
                                                    
                                                  </div>

                                                  '.$hallazgo_steps_2.'

                                                  <div class="row">

                                                    '.$content_steps_2_p1.'
                                                    '.$content_steps_2_p2.'
                                                    '.$content_steps_2_p3.'
                                                    
                                                  </div>

                                                  <div class="row" style="font-size:9px;" >

                                                    <div style=" font-size:9px; padding:2.5px" class="col-xs-4 col-sm-3">

                                                    <br>
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
                                                  <div style=" font-size:9px; padding:2.5px" class="col-xs-4 col-sm-3">

                                                  </div>
                                                  <div style=" font-size:9px; padding:2.5px" class="col-xs-4 col-sm-3">

                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <br>
                                                    <span>_________________________</span>
                                                    <br>
                                                    <span><b>'.$responsable.'</b></span>
                                                    <br>
                                                    <span>Responsable</span>

                                                  </div>
                                                    
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
                        font-family: "Lucida Sans", sans-serif;
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
