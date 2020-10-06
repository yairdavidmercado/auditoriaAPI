<?php 
 include 'conexion.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0); 

                $codigo 	= $data["codigo"] ? $data["codigo"] : '0'; 
                $parametro 	= $data["parametro"] ? $data["parametro"] :'0' ;
                $parametro2	= $data["parametro2"] ? $data["parametro2"] :'0' ;
                $cod_admi	= $data["cod_admi"] ? $data["cod_admi"] :'0' ;
                $cod_audi	= $data["cod_audi"] ? $data["cod_audi"] :'0' ;

                //parametros de conexion a la base de datos del cliente

			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;

				if ($conn) {
					if ($codigo == '1') {
                        $result = pg_query($conn, "SELECT *, 
                                                wverificar_respuesta(wcomponentehtml.id, $cod_audi) as respuesta,
                                                (select anulado from wauditorias WHERE cod_audi = $cod_audi and perfil = wcomponentehtml.perfil limit 1 ) as anulado,
                                                (select terminado from wauditorias WHERE cod_audi = $cod_audi and perfil = wcomponentehtml.perfil limit 1 ) as terminado,
                                                (select id_componente from wsubcomponentehtml 
                                                WHERE wcomponentehtml.id = wsubcomponentehtml.id_componente 
                                                and wsubcomponentehtml.perfil = wsubcomponentehtml.perfil 
                                                and wsubcomponentehtml.estado = true limit 1 ) as id_especial,
                                                 wverificacion_finalizacion('1','$cod_admi', '0', '$parametro', 'FINALIZADO') as finalizado 
                                                FROM wcomponentehtml 
                                                WHERE perfil = $parametro 
                                                and estado = true
                                                and (select cod_admi from wauditorias WHERE cod_audi = $cod_audi and perfil = wcomponentehtml.perfil limit 1 ) = $cod_admi
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
                        $valor;
                        $data = $response["resultado"];
                        for ($x = 0; $x < count($data); $x++) {
                            if ($data[$x]["dependencia"] == 0) {
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","finalizado":"'.$data[$x]["finalizado"].'","terminado":"'.$data[$x]["terminado"].'","anulado":"'.$data[$x]["anulado"].'","respuesta":"'.$data[$x]["respuesta"].'","id_especial":"'.$data[$x]["id_especial"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                            }
                            
                        }
                        $valor = trim($valor, ',');
                        $manage["data"] = json_decode("[".$valor."]");
                        $manage["datall"] = $data;
                        echo json_encode($manage);
						}else{
                                $manage = json_decode("[]");
								// echo no users JSON
								echo json_encode($manage);
						}
                    }else if ($codigo == '2') {
                        $result1 = pg_query($conn, "SELECT wcrear_control('2', '$parametro', '', '', '$parametro2', '', '', '')");
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
                            echo json_encode($response);

                        }
                    }else if ($codigo == '3') {
                        $result = pg_query($conn, "SELECT * FROM wcomponentehtml 
                                                WHERE perfil = $parametro 
                                                and estado = true
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
                        $valor;
                        $data = $response["resultado"];
                        for ($x = 0; $x < count($data); $x++) {
                            if ($data[$x]["dependencia"] == 0) {
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'", "child": ['.childs($data[$x]["id"], $data ).']},';
                            }
                            
                        }
                        $valor = trim($valor, ',');
                        $manage = json_decode("[".$valor."]");
                        echo json_encode($manage);
						}else{
                                $manage = json_decode("[]");
								// echo no users JSON
								echo json_encode($manage);
						}
                    }else if ($codigo == '4') {
                        $result1 = pg_query($conn, "SELECT cod_audi FROM wfinalizacion_auditoria WHERE id_bloqueo = '$cod_admi'::numeric and perfil::integer = '$parametro'::integer AND tipo = 'FINALIZADO' limit 1;");
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
                            echo json_encode($response);

                        }else{
                            $response["success"] = false;
                            $response["message"] = "No existen regístros.";
                            echo json_encode($response);
                        }
                    }else if ($codigo == '5') {
                        $result = pg_query($conn, "SELECT * FROM wsubcomponentehtml 
                                                WHERE perfil = $parametro 
                                                AND id_componente = $parametro2
                                                and estado = true
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
                        $valor;
                        $data = $response["resultado"];
                        for ($x = 0; $x < count($data); $x++) {
                            if ($data[$x]["dependencia"] == 0) {
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'", "child": ['.childs($data[$x]["id"], $data ).']},';
                            }
                            
                        }
                        $valor = trim($valor, ',');
                        $manage = json_decode("[".$valor."]");
                        echo json_encode($manage);
						}else{
                                $manage = json_decode("[]");
								// echo no users JSON
								echo json_encode($manage);
						}
                    }else if ($codigo == '6') {
                        $result1 = pg_query($conn, "SELECT wcrear_sub_control('2', '$parametro', '', '', '$parametro2', '', '', '', '')");
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
                            echo json_encode($response);

                        }
                    }else if ($codigo == '7') {
                        $result = pg_query($conn, "SELECT *, 
                                                wverificar_sub_respuesta(wsubcomponentehtml.id, $cod_audi) as respuesta
                                                FROM wsubcomponentehtml 
                                                WHERE perfil = $parametro 
                                                and id_componente = $parametro2
                                                and estado = true
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
                        $valor;
                        $data = $response["resultado"];
                        for ($x = 0; $x < count($data); $x++) {
                            if ($data[$x]["dependencia"] == 0) {
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","finalizado":"'.$data[$x]["finalizado"].'","terminado":"'.$data[$x]["terminado"].'","anulado":"'.$data[$x]["anulado"].'","respuesta":"'.$data[$x]["respuesta"].'","id_especial":"'.$data[$x]["id_especial"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                            }
                            
                        }
                        $valor = trim($valor, ',');
                        $manage["data"] = json_decode("[".$valor."]");
                        $manage["datall"] = $data;
                        echo json_encode($manage);
						}else{
                                $manage = json_decode("[]");
								// echo no users JSON
								echo json_encode($manage);
						}
                    }else if ($codigo == '8') {
                        $result1 = pg_query($conn, "SELECT (SELECT value1 as pregunta FROM wcomponentehtml as d 
                        WHERE d.dependencia = (SELECT dependencia from wcomponentehtml as c WHERE c.id = (SELECT dependencia from wcomponentehtml as b WHERE b.id = a.dependencia )
                         ) AND etiqueta = 'p' limit 1 ),
                        a.value1 as respuesta,
                        a.id as id_resp,
                        'PREGUNTA' as tipo
                        FROM wcomponentehtml as a WHERE 
                        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'PREGUNTA' ) IS NULL
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
                            WHERE a.perfil = $parametro
                            AND a.estado = true
                            AND wverificar_respuesta(h.id, $cod_audi) <> ''
                            AND (h.value1 = 'NO' OR h.value1 = 'NO CUMPLE' )group by 2 order by 2) AS tabla)
                        UNION ALL
                        SELECT (SELECT value1 as pregunta FROM wsubcomponentehtml as d 
                        WHERE d.dependencia = (SELECT dependencia from wsubcomponentehtml as c WHERE c.id = (SELECT dependencia from wsubcomponentehtml as b WHERE b.id = a.dependencia )
                         ) AND etiqueta = 'p' limit 1 ),
                        a.value1 as respuesta,
                        a.id as id_resp,
                        'SUBPREGUNTA' as tipo   
                        FROM wsubcomponentehtml as a WHERE 
                        (SELECT observacion FROM wjustificaciones WHERE a.id = wjustificaciones.id_componente AND cod_audi = $cod_audi AND tipo = 'SUBPREGUNTA' ) IS NULL
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
                            WHERE a.perfil = $parametro
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
                            $response["success"] = true;
                            $response["message"] = "Existen registros.";
                            echo json_encode($response);

                        }else {
                            $response["success"] = false;
                            $response["resultado"] = [];
                            echo json_encode($response);
                        }
                    }
				}
				else
					{
				$response["success"] = false;
				$response["message"] = "No se pudo establecer conexion con el servidor";
				// echo no users JSON
				echo json_encode($response);
			}
            pg_close($conn);

            function childrens($id = null, $data = []){
                if (isset($id) ) {
                    
                    $valor;
                    for ($x = 0; $x < count($data); $x++) {
                        if ($id == $data[$x]["dependencia"]) {
                            $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","dependencia":"'.$data[$x]["dependencia"].'","value":"'.$data[$x]["value1"].'","type":"'.$data[$x]["type1"].'", "respuesta":"'.$data[$x]["respuesta"].'", "id_especial":"'.$data[$x]["id_especial"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                        }
                    }
                    $valor = trim($valor, ',');

                    return $valor;
                }else{
                    return '';
                }
            }

            function childs($id = null, $data = []){
                if (isset($id) ) {
                    
                    $valor;
                    for ($x = 0; $x < count($data); $x++) {
                        if ($id == $data[$x]["dependencia"]) {
                            $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","dependencia":"'.$data[$x]["dependencia"].'","value":"'.$data[$x]["value1"].'","type":"'.$data[$x]["type1"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
                        }
                    }
                    $valor = trim($valor, ',');

                    return $valor;
                }else{
                    return '';
                }
            }
?>