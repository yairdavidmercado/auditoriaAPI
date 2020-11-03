<?php
session_start(); 
set_time_limit(30000);
//error_reporting(0);
ini_set('memory_limit', '128M');
include '../conexion.php';
require_once 'dompdf/autoload.inc.php';
date_default_timezone_set('America/Bogota');
use Dompdf\Dompdf;
//$dia=date(Y);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0); 

                $codigo 	= $_GET["codigo"];
                $perfil 	= $_GET["perfil"];
                $cod_admi	= $_GET["cod_admi"];
                $cod_audi	= $_GET["cod_audi"];

                //parametros de conexion a la base de datos del cliente

			  $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
        $arr = null;
        $justificaciones = justificaciones($conn, $cod_audi, $perfil);
				if ($conn) {
					if ($codigo == '1') {
            $result = pg_query($conn, "SELECT *, 
                          wverificar_respuesta(wcomponentehtml.id, $cod_audi) as respuesta , 
                          (SELECT descripcion FROM witem_menu WHERE id = wcomponentehtml.perfil limit 1) as nom_auditoria,
                          (SELECT wusuarios.nombre FROM wusuarios INNER JOIN wdevoluciones ON wusuarios.cod_usua = wdevoluciones.cod_usua 
                          WHERE wdevoluciones.cod_audi = $cod_audi
                          ORDER BY consec DESC LIMIT 1) AS responsable,
                          (SELECT wusuarios.nombre FROM wusuarios INNER JOIN wdevoluciones ON wusuarios.cod_usua = wdevoluciones.cod_crea 
                          WHERE wdevoluciones.cod_audi = $cod_audi
                          ORDER BY consec DESC LIMIT 1) AS autor,
                          (SELECT to_char(wauditorias.fechacrea, 'YYYY-MM-DD') AS fecha_solicitud FROM wauditorias WHERE perfil = $perfil AND cod_audi = $cod_audi limit 1),
                                      (SELECT to_char(wauditorias.fechacrea, 'HH24:MI:SS') AS hora_creacion FROM wauditorias WHERE perfil = $perfil AND cod_audi = $cod_audi limit 1),
                          (SELECT to_char(MAX(fechacrea), 'YYYY-MM-DD') FROM wrespuestas WHERE perfil = $perfil AND wrespuestas.cod_audi = $cod_audi LIMIT 1 ) as fecha_final,
                          (SELECT to_char(MAX(fechacrea), 'HH24:MI:SS') FROM wrespuestas WHERE perfil = $perfil AND wrespuestas.cod_audi = $cod_audi LIMIT 1 ) as hora_final,
                          wvalidar_hallazgo(1, $cod_audi, $perfil, wcomponentehtml.id) as hallazgo  
                          FROM wcomponentehtml 
                                    WHERE perfil = $perfil 
                                    order by 4,7");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Existen registros.";
                                                    //echo json_encode($response);

                            $hallazgo = [];
                            $html_body_sub;
                            $hallazgo = hallazgo($conn, $cod_audi, $perfil);
                            $data_card;
                            $cambio = '0';
                            for ($x = 0; $x < count($hallazgo); $x++) {
                                if ($hallazgo[$x]["id_card"] != $cambio) {
                                    $data_card .= '{"id":"'.$hallazgo[$x]["id_card"].'","class":"'.$hallazgo[$x]["class_card"].'"},';
                                }
                                $cambio = $hallazgo[$x]["id_card"];
                            }
                            $data_card = trim($data_card, ',');
                            $data_card = json_decode("[".$data_card."]", true);


                            $nom_perfil = $response["resultado"][0]["nom_auditoria"];
                            $fecha_solicitud = $response["resultado"][0]["fecha_solicitud"];
                            $hora = $response["resultado"][0]["hora_creacion"];

                            $fecha_final = $response["resultado"][0]["fecha_final"];
                            $hora_final = $response["resultado"][0]["hora_final"];

                            $autor = $response["resultado"][0]["autor"];
                            $responsable = $response["resultado"][0]["responsable"];

                            $valor;
                            $data = $response["resultado"];
                            for ($x = 0; $x < count($data); $x++) {
                                $html_body_sub .= sub_card($conn, $cod_audi, $perfil, $data[$x]["id"]);
                                if ($data[$x]["dependencia"] == 0) {
                                    $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","respuesta":"'.$data[$x]["respuesta"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                                }
                            }

                            $valor = trim($valor, ',');
                            $manage = json_decode("[".$valor."]");
                            //$manage = json_encode($manage);

                            $html_body;
                            for ($x = 0; $x < count($data_card); $x++) {
                              for ($x1 = 0; $x1 < count($data); $x1++) {
                                if ($data[$x1]["id"] == $data_card[$x]["id"]) {
                                    $html_body .= '<div class="card" style="font-size:10px;" >'.estructurahtml($data[$x1]["id"], $data ).'</div>';
                                }
                              }
                            }

						}else{
                            $manage = json_decode("[]");
								// echo no users JSON
								//$manage = json_encode($manage);
						}
          }
				}else{
          $response["success"] = false;
          $response["message"] = "No se pudo establecer conexion con el servidor";
          // echo no users JSON
          echo json_encode($response);
        }
         
    pg_close($conn);

    function sub_card($conn, $cod_audi, $perfil, $id_componente){
      $result = pg_query($conn, "SELECT *, 
        wverificar_sub_respuesta(wsubcomponentehtml.id, $cod_audi) as respuesta,
        wvalidar_sub_hallazgo(1, $cod_audi, $perfil,wsubcomponentehtml.id) as hallazgo
        FROM wsubcomponentehtml 
        WHERE perfil = $perfil
        AND id_componente = $id_componente
        order by 4,1,7");
        if(pg_num_rows($result) > 0)
        {	
                      $response["resultado"] = array();
                      while ($row = pg_fetch_array($result)) {
                      $datos = array();
                        
                        // push single product into final response array
                        array_push($response["resultado"], $row);
                      }
                      $response["success"] = true;
                      $response["message"] = "Existen registros.";
                                                //echo json_encode($response);

                        $hallazgo = [];
                        $hallazgo = subhallazgo($conn, $cod_audi, $perfil);

                        $data_card;
                        $cambio = '0';
                        for ($x = 0; $x < count($hallazgo); $x++) {
                            if ($hallazgo[$x]["id_card"] != $cambio) {
                                $data_card .= '{"id":"'.$hallazgo[$x]["id_card"].'","class":"'.$hallazgo[$x]["class_card"].'"},';
                            }
                            $cambio = $hallazgo[$x]["id_card"];
                        }
                        $data_card = trim($data_card, ',');
                        $data_card = json_decode("[".$data_card."]", true);

                        $valor;
                        $data = $response["resultado"];
                        for ($x = 0; $x < count($data); $x++) {
                            if ($data[$x]["dependencia"] == 0) {
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","respuesta":"'.$data[$x]["respuesta"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                            }
                        }

                        $valor = trim($valor, ',');
                        $manage = json_decode("[".$valor."]");
                        //$manage = json_encode($manage);

                        $html_body1 = '';

                        for ($x = 0; $x < count($data_card); $x++) {
                          for ($x1 = 0; $x1 < count($data); $x1++) {
                            if ($data[$x1]["id"] == $data_card[$x]["id"]) {
                                $html_body1 .= '<div class="card" style="font-size:10px;" >'.estructurahtml_sub($data[$x1]["id"], $data ).'</div>';
                            }
                          }
                        }
                      return $html_body1;
                        
        }else{
                        $manage = json_decode("[]");
            // echo no users JSON
            //$manage = json_encode($manage);
        }
    }

    function subhallazgo($conn, $cod_audi, $perfil)
    {
        $result1 = pg_query($conn, "SELECT a.clase as class_card, 
                                    a.id as id_card, 
                                    b.clase as class_card_body , 
                                    b.id id_body, 
                                    c.clase as classrow, 
                                    c.id as id_row, 
                                    d.clase as class_colsm6, 
                                    d.id as id_colsm6
                                    FROM wsubcomponentehtml as a
                                    INNER JOIN wsubcomponentehtml as b on a.id = b.dependencia
                                    INNER JOIN wsubcomponentehtml as c on b.id = c.dependencia
                                    INNER JOIN wsubcomponentehtml as d on c.id = d.dependencia
                                    INNER JOIN wsubcomponentehtml as e on d.id = e.dependencia
                                    INNER JOIN wsubcomponentehtml as f on e.id = f.dependencia
                                    INNER JOIN wsubcomponentehtml as g on f.id = g.dependencia
                                    INNER JOIN wsubcomponentehtml as h on f.id = h.dependencia
                                    WHERE a.perfil = $perfil
                                    AND a.estado = true
                                    AND wverificar_sub_respuesta(h.id,  $cod_audi) <> ''
                                    AND (h.value1 = 'NO' OR h.value1 = 'NO CUMPLE' )group by 1,2,4,6,7,8 order by 2,8");
        if(pg_num_rows($result1) > 0)
        {	
            $response["resultado"] = array();
            while ($row = pg_fetch_array($result1)) {
            $datos = array();
                
                // push single product into final response array
                array_push($response["resultado"], $row);
            }
            $response["success"] = true;
            $response["message"] = "Existen registros.";
            return $response["resultado"];

        }else {
            return [];
        }
    }

    function hallazgo($conn, $cod_audi, $perfil)
    {
        $result1 = pg_query($conn, "SELECT a.clase as class_card, 
                                    a.id as id_card, 
                                    b.clase as class_card_body , 
                                    b.id id_body, 
                                    c.clase as classrow, 
                                    c.id as id_row, 
                                    d.clase as class_colsm6, 
                                    d.id as id_colsm6
                                    FROM wcomponentehtml as a
                                    INNER JOIN wcomponentehtml as b on a.id = b.dependencia
                                    INNER JOIN wcomponentehtml as c on b.id = c.dependencia
                                    INNER JOIN wcomponentehtml as d on c.id = d.dependencia
                                    INNER JOIN wcomponentehtml as e on d.id = e.dependencia
                                    INNER JOIN wcomponentehtml as f on e.id = f.dependencia
                                    INNER JOIN wcomponentehtml as g on f.id = g.dependencia
                                    INNER JOIN wcomponentehtml as h on f.id = h.dependencia
                                    WHERE a.perfil = $perfil
                                    AND a.estado = true
                                    AND wverificar_respuesta(h.id,  $cod_audi) <> ''
                                    AND (h.value1 = 'NO' OR h.value1 = 'NO CUMPLE' )group by 1,2,4,6,7,8 order by 2,8");
        if(pg_num_rows($result1) > 0)
        {	
            $response["resultado"] = array();
            while ($row = pg_fetch_array($result1)) {
            $datos = array();
                
                // push single product into final response array
                array_push($response["resultado"], $row);
            }
            $response["success"] = true;
            $response["message"] = "Existen registros.";
            return $response["resultado"];

        }else {
            return [];
        }
    }

    function justificaciones($conn, $cod_audi, $perfil)
    {
        $result1 = pg_query($conn, "SELECT (SELECT value1 as pregunta FROM wcomponentehtml as d 
        WHERE d.dependencia = (SELECT dependencia from wcomponentehtml as c WHERE c.id = (SELECT dependencia from wcomponentehtml as b WHERE b.id = a.dependencia )
         ) AND etiqueta = 'p' limit 1 ),
        a.value1 as respuesta,
        a.id as id_resp,
        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'PREGUNTA' ) as justificacion,
        'PREGUNTA' as tipo
        FROM wcomponentehtml as a WHERE 
        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'PREGUNTA' ) IS NOT NULL
        AND
        id IN(
        SELECT id_colsm6 FROM (SELECT  
            h.value1 as value_colsm6, 
            h.id as id_colsm6
            FROM wcomponentehtml as a
            INNER JOIN wcomponentehtml as b on a.id = b.dependencia
            INNER JOIN wcomponentehtml as c on b.id = c.dependencia
            INNER JOIN wcomponentehtml as d on c.id = d.dependencia
            INNER JOIN wcomponentehtml as e on d.id = e.dependencia
            INNER JOIN wcomponentehtml as f on e.id = f.dependencia
            INNER JOIN wcomponentehtml as g on f.id = g.dependencia
            INNER JOIN wcomponentehtml as h on f.id = h.dependencia
            WHERE a.perfil = $perfil
            AND a.estado = true
            AND wverificar_respuesta(h.id, $cod_audi) <> ''
            AND (h.value1 = 'NO' OR h.value1 = 'NO CUMPLE' )group by 2 order by 2) AS tabla)
        UNION ALL
        SELECT (SELECT value1 as pregunta FROM wsubcomponentehtml as d 
        WHERE d.dependencia = (SELECT dependencia from wsubcomponentehtml as c WHERE c.id = (SELECT dependencia from wsubcomponentehtml as b WHERE b.id = a.dependencia )
         ) AND etiqueta = 'p' limit 1 ),
        a.value1 as respuesta,
        a.id as id_resp,
        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'SUBPREGUNTA' ) as justificacion,
        'SUBPREGUNTA' as tipo   
        FROM wsubcomponentehtml as a WHERE 
        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'SUBPREGUNTA' ) IS NOT NULL
        AND
        id IN(
        SELECT id_colsm6 FROM (SELECT  
            h.value1 as value_colsm6, 
            h.id as id_colsm6
            FROM wsubcomponentehtml as a
            INNER JOIN wsubcomponentehtml as b on a.id = b.dependencia
            INNER JOIN wsubcomponentehtml as c on b.id = c.dependencia
            INNER JOIN wsubcomponentehtml as d on c.id = d.dependencia
            INNER JOIN wsubcomponentehtml as e on d.id = e.dependencia
            INNER JOIN wsubcomponentehtml as f on e.id = f.dependencia
            INNER JOIN wsubcomponentehtml as g on f.id = g.dependencia
            INNER JOIN wsubcomponentehtml as h on f.id = h.dependencia
            WHERE a.perfil = $perfil
            AND a.estado = true
            AND wverificar_respuesta(h.id, $cod_audi) <> ''
            AND (h.value1 = 'NO' OR h.value1 = 'NO CUMPLE' )group by 2 order by 2) AS tabla)");
        if(pg_num_rows($result1) > 0)
        {	
            $response["resultado"] = array();
            while ($row = pg_fetch_array($result1)) {
            $datos = array();
                
                // push single product into final response array
                array_push($response["resultado"], $row);
            }
            $data = $response["resultado"];
            $html_justificacion = '';
            for ($x = 0; $x < count($data); $x++) {
              $html_justificacion .= '<p style="font-size:11px;" ><b>'.$data[$x]["pregunta"].'</b><br>'.$data[$x]["respuesta"].'<br>'.$data[$x]["justificacion"].'</p>';
            }
            $response["success"] = true;
            $response["message"] = "Existen registros.";
            return '<h5>Justificaciones</h5>
            '.$html_justificacion;

        }else {
            return "";
        }
    }

    function childrens($id = null, $data = []){
        if (isset($id) ) {
          
          $valor;
          for ($x = 0; $x < count($data); $x++) {
              if ($id == $data[$x]["dependencia"]) {
                  $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","dependencia":"'.$data[$x]["dependencia"].'","value":"'.$data[$x]["value1"].'","type":"'.$data[$x]["type1"].'", "respuesta":"'.$data[$x]["respuesta"].'", "hallazgo":"'.$data[$x]["hallazgo"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
              }
          }
          $valor = trim($valor, ',');

          return $valor;
      }else{
          return '';
      }
  }

  function estructurahtml_sub($id = null, $data = []){

    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["clase"] == 'card-header') {
                  $valor .= '<div style="background-color:#EFF0F1; margin-left:-12px">
                              <label style="font-size:9px">'.htmldetalleparrafo($data[$x]["id"], $data ).'</label>
                            </div>
                            <div style="background-color:#137DED; height:1.5px; margin-left:-12px" ></div>';
                }elseif ($data[$x]["clase"] == 'card-body') {
                  $valor .= '<div class="'.$data[$x]["clase"].'">'.htmldetalle1_sub($data[$x]["id"], $data ).'</div>';
                }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmldetalle1_sub($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                $valor .= '<div style="margin-left:-15px" class="'.$data[$x]["clase"].'">'.htmldetallerow_sub($data[$x]["id"], $data ).'</div>';
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmldetallerow_sub($id = null, $data = []){
    if (isset($id) ) {
      $fila = '';
      $contador = 0;
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["clase"] == 'col-sm-12') {
                  if ($data[$x]["hallazgo"] != '0') {
                    $valor .= htmlcols_sub($data[$x]["id"], $data );
                  }
                }
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlcols_sub($id = null, $data = []){
    global $data_colsm6;
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
              if ($data[$x]["etiqueta"] == 'p') {
                $valor .= '<p>'.$data[$x]["value1"].'</p>
                <div style="background-color:#6b9923; height:1px; width:350px;"></div><br>';
              }else{
                $valor .= '<div>'.htmlformgroup_sub($data[$x]["id"], $data ).'</div>';
              }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlformgroup_sub($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
              if ( $data[$x]["clase"] =='icheck-primary d-inline') {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.htmlcontrol1_sub($data[$x]["id"], $data ).'</div>';
              }else if ( $data[$x]["etiqueta"] =='p') {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.$data[$x]["value1"].'</div>';
              }else if ( $data[$x]["clase"] =='form-control') {
                $valor .= '<div >'.$data[$x]["respuesta"].'</div>';
              }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlcontrol1_sub($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["etiqueta"] == 'input') {
                  if ($data[$x]["value1"] == $data[$x]["respuesta"]) {
                    $valor .= '<div >'.$data[$x]["respuesta"].'</div>';
                  }
                 
                }
            }
        }

        return $valor;
    }else{
        return '';
    }
  }
  //-----------------------------------------------------------------------------------------

  function estructurahtml($id = null, $data = []){

    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["clase"] == 'card-header') {
                  $valor .= '<div style="background-color:#EFF0F1; margin-left:-12px">
                              <label style="font-size:9px">'.htmldetalleparrafo($data[$x]["id"], $data ).'</label>
                            </div>
                            <div style="background-color:#137DED; height:1.5px; margin-left:-12px" ></div>';
                }elseif ($data[$x]["clase"] == 'card-body') {
                  $valor .= '<div class="'.$data[$x]["clase"].'">'.htmldetalle1($data[$x]["id"], $data ).'</div>';
                }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmldetalleparrafo($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                $valor .= '<p  class="'.$data[$x]["clase"].'"><b>'.$data[$x]["value1"].'</b></p>';
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmldetalle1($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                $valor .= '<div style="margin-left:-15px" class="'.$data[$x]["clase"].'">'.htmldetallerow($data[$x]["id"], $data ).'</div>';
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmldetallerow($id = null, $data = []){
    if (isset($id) ) {
      $fila = '';
      $contador = 0;
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["clase"] == 'col-sm-12') {
                  $valor .= '<div>
                              <table>
                                <tr>
                                    <td style="width:400px;">
                                        '.htmldetalleparrafo($data[$x]["id"], $data ).'
                                    </td>
                                </tr>
                              </table>
                            </div>';
                }elseif ($data[$x]["clase"] == 'col-sm-6') {
                  if ($data[$x]["hallazgo"] != '0') {
                    $contador = $contador+1;
                    if ($contador == 1) {
                      $valor .= '<div> 
                                  <table>
                                    <tr>
                                        <td style="width:370px; margin-right:12px;">
                                          '.htmlcols($data[$x]["id"], $data ).'
                                        </td>';
      
                    }else if ($contador == 2) {
      
                      $valor .= '<td style="width:370px;">
                                    '.htmlcols($data[$x]["id"], $data ).'
                                </td>
                            </tr>
                        </table>
                      </div>';
                        $contador = 0;
                    }
                  }  
                }
            }
        }

        return $valor.'</td>
                    </tr>
                  </table>
                </div>';
    }else{
        return '';
    }
  }

  function htmlcols($id = null, $data = []){
    global $data_colsm6;
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
              if ($data[$x]["etiqueta"] == 'p') {
                $valor .= '<p>'.$data[$x]["value1"].'</p>
                <div style="background-color:#6b9923; height:1px; width:350px;"></div><br>';
              }else{
                $valor .= '<div>'.htmlformgroup($data[$x]["id"], $data ).'</div>';
              }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlformgroup($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
              if ( $data[$x]["clase"] =='icheck-primary d-inline') {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.htmlcontrol1($data[$x]["id"], $data ).'</div>';
              }else if ( $data[$x]["etiqueta"] =='p') {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.$data[$x]["value1"].'</div>';
              }else if ( $data[$x]["clase"] =='form-control') {
                $valor .= '<div >'.$data[$x]["respuesta"].'</div>';
              }
                
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlcontrol1($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                if ($data[$x]["etiqueta"] == 'input') {
                  if ($data[$x]["value1"] == $data[$x]["respuesta"]) {
                    $valor .= '<div >'.$data[$x]["respuesta"].'</div>';
                  }
                 
                }
            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlcontrolradio($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.$data[$x]["etiqueta"].'</div>';

            }
        }

        return $valor;
    }else{
        return '';
    }
  }

  function htmlcontrolvalores($id = null, $data = []){
    if (isset($id) ) {
        
        for ($x = 0; $x < count($data); $x++) {
            if ($id == $data[$x]["dependencia"]) {
              if ($data[$x]["etiqueta"] == 'input') {
                $valor .= '<div class="'.$data[$x]["clase"].'">'.$data[$x]["value1"].'</div>';
              }

            }
        }

        return $valor;
    }else{
        return '';
    }
  }


  function obtenerFechaEnLetra($fecha){
    $dia= conocerDiaSemanaFecha($fecha);
    $num = date("j", strtotime($fecha));
    $anno = date("Y", strtotime($fecha));
    $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    return $dia.', '.$num.' de '.$mes.', año '.$anno;
}
 
function conocerDiaSemanaFecha($fecha) {
    $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    $dia = $dias[date('w', strtotime($fecha))];
    return $dia;
}

$iformacionpaciente = file_get_contents('http://localhost/APIExterno/sel_info_paciente.php?codigo=1&parametro='.$cod_admi);
$iformacionpaciente = json_decode($iformacionpaciente, TRUE);
//var_dump($iformacionpaciente["resultado"][0]["cod_admi"]);

$info_paciente = '<div style="font-size:9px; margin-left:-12px">
                    <table border="0" style="width:100%">

                      <tr>

                        <td style="height:-2px;" colspan="3">
                          <span><b>IDENTIFICACIÓN: </b>'.$iformacionpaciente["data"][0]["id_pacien"].'</span>
                        </td>

                        <td style="height:-2px;">
                          <span><b>N° ADMISÍON: </b>'.$iformacionpaciente["data"][0]["cod_admi"].'</span>
                        </td>

                      </tr>
                      <tr>

                        <td style="height:-2px;" colspan="3">
                          <span><b>NOMBRES Y APELLIDOS: </b>'.$iformacionpaciente["data"][0]["nom_usua"].'</span>
                        </td>

                        <td style="height:-2px;">
                          <span><b>TIPO DE AFILIACIÓN: </b> '.$iformacionpaciente["data"][0]["tipoafiliado"].'</span>
                        </td>

                      </tr>
                      <tr>

                        <td style="width:170px;">
                          <span><b>FECHA DE NACIMIENTO: </b>'.$iformacionpaciente["data"][0]["fecha_nac"].'</span>
                        </td>

                        <td style="width:120px;">
                          <span><b>EDAD: </b>'.$iformacionpaciente["data"][0]["edad"].'</span>
                        </td>

                        <td style="width:100px;">
                          <span><b>SEXO: </b>'.$iformacionpaciente["data"][0]["sexo_pacien"].'</span>
                        </td>
                        <td style="height:-2px;">
                          <span><b>NOMBRE DE ACOMPAÑANTE: </b> '.$iformacionpaciente["data"][0]["nom_acom"].'</span>
                        </td>

                      </tr>
                      <tr>

                        <td style="height:-2px;" colspan="3">
                          <span><b>TELÉFONO: </b>'.$iformacionpaciente["data"][0]["tel_pacien"].'</span>
                        </td>
                        
                        <td style="height:-2px;">

                        </td>

                      </tr>
                      <tr>

                        <td style="height:-2px;" colspan="3">
                          <span><b>ASEGURADORA: </b>'.$iformacionpaciente["data"][0]["nom_contrato"].'</span>
                        </td>

                        <td style="height:-2px;">

                        </td>

                      </tr>

                    </table>

</div>';

  $header = '<table border="0" style="width:100%; font-size:9px">
              <tr>
                <td style="width:100px;"  rowspan="2">
                  <div class="grid-item" style="width:150px; height:60px;">
                  <img src="img/logo.png" width="100%">
                  </div>
                </td>

                <td style="font-size:12px;" colspan="2">
                  <span class="title"><b>Hospital César Uribe Piedrahita<b></span>
                </td>

                <td style="font-size:12px; text-align:right;">

                  <span class="text-info"><b>'.$nom_perfil.'</b></span>
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
                    <span>890980757</span>
                    <br>
                    <span>Caucasia – Antioquia</span>
                    <br>
                    <span>8392161</span>
                    <br>
                    <span>informacion@hucp.gov.co</span>
                </td>
                <td style="width:300px; text-align:right;">
                  <span style="font-size:11px"><b>RESULTADO DE AUDITORIA No. '.$cod_audi.'</b></span>
                  <br>
                  <br>
                  <span style="font-size:9px;"><b>Inicio</b>: '.obtenerFechaEnLetra($fecha_solicitud).' - '.$hora.'</span>
                  <br>
                  <span style="font-size:9px;"><b>Final</b>: '.obtenerFechaEnLetra($fecha_final).' - '.$hora_final.'</span>
                </td>
              </tr>
            </table>';
$firma = '<table style="font-size:10px">
            <tbody>
                <tr>
                    <td style="width:370px; margin-right:12px;">
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
                      <span>Auditor</span>
                    </td>
                    <td style="width:370px; margin-right:12px;">
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
                      <span>Responsable</span>
                    </td>
                </tr>
            </tbody>
          </table>';

  $html='<!DOCTYPE html>
            <html>
            <head>
              <meta charset="utf-8">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title></title>
              <!-- <link rel="stylesheet" type="text/css" href="bootstrap-3.3.7-dist/css/bootstrap.min.css"> -->
                <style>
                  body{
                    font-family: "Tangerine", serif;
                    margin-top: 1cm;
                  }
                  @page {
                    margin: 100px 40px;
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
                </style>
            </head>
            <body> 
              <header>
                '.$header.'
              </header>
              <div style="background-color:#EFF0F1; margin-left:-12px">
                <label class="text-info" style="font-size:9px"><b>INFORMACION DEL PACIENTE</b></label>
              </div>
              <div style="background-color:#137DED; height:1.5px;margin-left:-12px"></div>
            '.$info_paciente.'
            '.$html_body.'
            '.$html_body_sub.'
            '.$justificaciones.'
            '.$firma.'
            </body>
            </html>';
//echo $html_body_sub;
// instantiate and use the dompdf class

$dompdf = new Dompdf();
ob_end_clean();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();
$time = time();

$fecha_php = date("d-m-Y");
$hora_php = date("h:i a");

$dompdf->get_canvas()->page_text(20, 760, "{$nom_perfil}  |  AUDITORIA No. {$cod_audi}", '', 6, array(0,0,0));

$dompdf->get_canvas()->page_text(20, 770, "Auditoría | Sistema de auditoría concurrente | www.auditoria.com.co", '', 6, array(0,0,0));

$dompdf->get_canvas()->page_text(380, 760, "Fecha y hora de Impresión: ".obtenerFechaEnLetra($fecha_php)." - ".$hora_php, '', 6, array(0,0,0));

$dompdf->get_canvas()->page_text(380, 770, " Página: {PAGE_NUM} / {PAGE_COUNT}", '', 6, array(0,0,0));

// Output the generated PDF to Browser
$dompdf->stream("Auditoría.pdf", array("Attachment" => 0));
?>