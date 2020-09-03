<?php 
 include 'conexion.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0); 

                $codigo 	= $data["codigo"]; 
                $parametro 	= $data["parametro"];
                $parametro2	= $data["parametro2"];
                $cod_admi	= $data["cod_admi"];
                $cod_audi	= $data["cod_audi"];

                //parametros de conexion a la base de datos del cliente

			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;

				if ($conn) {
					if ($codigo == '1') {
                        $result = pg_query($conn, "SELECT *, wverificar_respuesta(wcomponentehtml.id, $cod_audi) as respuesta FROM wcomponentehtml 
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
                                $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","respuesta":"'.$data[$x]["respuesta"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
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
                            $valor .= '{"id":"'.$data[$x]["id"].'","etiqueta":"'.$data[$x]["etiqueta"].'","class":"'.$data[$x]["clase"].'","ultimo_ranking":"'.$data[$x]["ranking"].'","dependencia":"'.$data[$x]["dependencia"].'","value":"'.$data[$x]["value1"].'","type":"'.$data[$x]["type1"].'", "respuesta":"'.$data[$x]["respuesta"].'", "child": ['.childrens($data[$x]["id"], $data ).']},';
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