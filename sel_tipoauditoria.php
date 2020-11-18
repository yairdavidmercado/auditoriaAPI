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
				$codigo1 	= $data["codigo1"];
				$parametro 	= $data["parametro"]; 
				$parametro2 = $data["parametro2"]; 
				$parametro3 = $data["parametro3"];

                //parametros de conexion a la base de datos del cliente

			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;

				if ($conn) {
					if($codigo == '1'){
						$result = pg_query($conn, 	"   SELECT id, 
                                                        nombre,
                                                        finalizar,
                                                        fecha_egre,
														fechas
                                                        FROM wtipo_auditoria
														WHERE activo = true
                                                        ORDER BY id DESC;");
						if(pg_num_rows($result) > 0)
						{	
							$response["resultado"] = array();
							while ($row = pg_fetch_array($result)) {
							$datos = array();

								$datos["id"] 			= $row["id"];
								$datos["nombre"]		= $row["nombre"];
								$datos["finalizar"]		= $row["finalizar"];
								$datos["fecha_egre"] 	= $row["fecha_egre"];
								$datos["fechas"] 		= $row["fechas"];
								
								// push single product into final response array
								array_push($response["resultado"], $row);
							}
							$response["success"] = true;
							$response["message"] = "Inicio de sesión éxitoso.";
							echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '2'){
						$result = pg_query($conn, 	"SELECT id, 
													nombre,
													(SELECT wperfil_tipo_auditoria.id_tipo_auditoria FROM wperfil_tipo_auditoria WHERE id_tipo_auditoria = wtipo_auditoria.id AND id_perfil = $parametro) AS permiso 
													FROM wtipo_auditoria 
													WHERE activo = true
													ORDER BY 1 DESC");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["id"] 			= $row["id"];
														$datos["nombre"] 	= $row["nombre"];
														$datos["permiso"] 		= $row["permiso"];
														
														// push single product into final response array
														array_push($response["resultado"], $datos);
													}
													$response["success"] = true;
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "No se encontraron registros";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '3'){
						$result = pg_query($conn, 	"select wpermisos_perfiles('$codigo1', '$parametro', '$parametro2')");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["id"] 			= $row["wpermisos_usuario"];
														
														// push single product into final response array
														array_push($response["resultado"], $datos);
													}
													$response["success"] = true;
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "No se encontraron registros";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '4'){
						$result = pg_query($conn, 	"SELECT wtipo_auditoria.id, 
													wtipo_auditoria.nombre 
													FROM wtipo_auditoria 
													INNER JOIN wperfil_tipo_auditoria 
													ON wperfil_tipo_auditoria.id_tipo_auditoria = wtipo_auditoria.id
													WHERE id_perfil = $parametro 
													AND wtipo_auditoria.activo = true
													ORDER BY 1 ASC");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["id"] 		= $row["id"];
														$datos["nombre"] 	= $row["nombre"];
														
														// push single product into final response array
														array_push($response["resultado"], $datos);
													}
													$response["success"] = true;
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "No se encontraron registros";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '5'){
						$result = pg_query($conn, 	"   SELECT id, 
                                                        nombre,
                                                        finalizar,
                                                        fecha_egre,
														fechas
                                                        FROM wtipo_auditoria
														WHERE id = $parametro
														AND activo = true
                                                        ORDER BY id DESC;");
						if(pg_num_rows($result) > 0)
						{	
							$response["resultado"] = array();
							while ($row = pg_fetch_array($result)) {
							$datos = array();

								$datos["id"] 			= $row["id"];
								$datos["nombre"]		= $row["nombre"];
								$datos["finalizar"]		= $row["finalizar"];
								$datos["fecha_egre"] 	= $row["fecha_egre"];
								$datos["fechas"] 		= $row["fechas"];
								
								// push single product into final response array
								array_push($response["resultado"], $row);
							}
							$response["success"] = true;
							$response["message"] = "Inicio de sesión éxitoso.";
							echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '6'){
						$result = pg_query($conn, 	"select wguardar_actualizar_tipoauditoria('$codigo1', '$parametro', '$parametro2', '$parametro3')");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["id"] 			= $row["wguardar_actualizar_tipoauditoria"];
														
														// push single product into final response array
														array_push($response["resultado"], $datos);
													}
													$response["success"] = true;
													echo json_encode($response);

						}else{
								$response["success"] = false;
								$response["message"] = "No se encontraron registros";
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