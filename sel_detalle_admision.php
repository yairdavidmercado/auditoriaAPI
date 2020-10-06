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
				$parametro2 = $data["parametro2"]; 
				$comentario = $data["comentario"] ? $data["comentario"]: '';
				$cod_admi 	= $data["cod_admi"] ? $data["cod_admi"]: '';
				$cod_usua 	= $data["cod_usua"] ? $data["cod_usua"]: '';                

                //parametros de conexion a la base de datos del cliente

			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;

				if ($conn) {
					if($codigo == '1'){
						$result = pg_query($conn, 	"SELECT *,
                                                    (SELECT nombre FROM wusuarios WHERE cod_usua = wauditorias.cod_usua ) as usuario_creador, 
                                                    SUBSTRING(wauditorias.fechacrea::text FROM 0 FOR 11) AS fecha_solicitud 
                                                    FROM wauditorias 
													WHERE cod_admi = $parametro 
													AND perfil = $parametro2 
													AND anulado = false 
													AND terminado = true order by 1 desc ;");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Exitoso.";
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '2'){
						$result = pg_query($conn, 	"SELECT wguardar_actualizar('1', '$parametro2', '$parametro', '', '', '');");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Exitoso.";
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '3'){
						$result = pg_query($conn, 	"SELECT wguardar_actualizar('2', '$parametro2', '$parametro', '0', '$comentario', '$cod_usua');");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Exitoso.";
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '4'){
						$result = pg_query($conn, 	"SELECT wguardar_actualizar('3', '$parametro2', '$parametro', '$cod_admi', '$comentario', '$cod_usua');");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Exitoso.";
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
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

			function childrens($data = []){
                    $valor;
                    for ($x = 0; $x < count($data); $x++) {
                        $valor .= '{"perfil":"'.$data[$x]["id_item"].'","descripcion":"'.$data[$x]["descripcion"].'"},';
                    }
                    $valor = trim($valor, ',');

                    return $valor;
            }
?>