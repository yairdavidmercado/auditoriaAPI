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

                //parametros de conexion a la base de datos del cliente

			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;

				if ($conn) {
					if($codigo == '1'){
						$result = pg_query($conn, 	"SELECT wusuarios.cod_usua, 
													nombre,
													id_rol,
													(SELECT 													
													id_item
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY 1 ASC LIMIT 1) AS item_principal,
													(SELECT 													
													descripcion
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY id_item ASC LIMIT 1) AS nombre_principal,
													witem_menu.id as id_item,
													witem_menu.descripcion,
													witem_menu.parent
													FROM wusuarios 
													INNER JOIN wpermisos ON wpermisos.cod_usua = wusuarios.cod_usua
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE wusuarios.cod_usua = '$parametro'
													AND witem_menu.parent = '1'
													AND password = MD5('$parametro2') ORDER BY 4;");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();

														$datos["cod_usua"] 			= $row["cod_usua"];
														$datos["nombre"]			= $row["nombre"];
														$datos["id_rol"]			= $row["id_rol"];
														$datos["perfil"] 			= $row["item_principal"];
														$datos["id_item"] 			= $row["id_item"];
														$datos["descripcion"] 		= $row["descripcion"];
														$datos["parent"] 			= $row["parent"];
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Inicio de sesión éxitoso.";
													//echo json_encode($response);
						$valor;
						$data = $response["resultado"];
						for ($x = 0; $x < count($data); $x++) {
							$valor = '{"cod_usua":"'.$data[$x]["cod_usua"].'","nombre":"'.$data[$x]["nombre"].'","rol":"'.$data[$x]["id_rol"].'","perfil":"'.$data[$x]["item_principal"].'","nombre_perfil":"'.$data[$x]["nombre_principal"].'", "perfiles": ['.childrens( $data ).']},';
						}
						$valor = trim($valor, ',');
						$manage = json_decode("[".$valor."]");
						$manage["success"] = true;
						$manage["message"] = "Inicio de sesión éxitoso.";
						echo json_encode($manage);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '2'){
						$result = pg_query($conn, 	"SELECT nombre FROM wusuarios WHERE cod_usua = $parametro");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["nombre"] 			= $row["nombre"];
														
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
						$result = pg_query($conn, 	"SELECT wusuarios.cod_usua, 
													nombre,
													CASE WHEN (SELECT 													
													id_item
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND witem_menu.id::text = '$parametro2'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY 1 ASC LIMIT 1) IS NULL 
													THEN (SELECT 													
													id_item
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY 1 ASC LIMIT 1) 
													ELSE (SELECT 													
													id_item
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND witem_menu.id::text = '$parametro2'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY 1 ASC LIMIT 1) END AS item_principal,
													CASE WHEN (SELECT 													
													descripcion
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND witem_menu.id::text = '$parametro2'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY id_item ASC LIMIT 1) IS NULL 
													THEN (SELECT 													
													descripcion
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY id_item ASC LIMIT 1) 
													ELSE (SELECT 													
													descripcion
													FROM wpermisos 
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE parent = '1' 
													AND cod_usua = '$parametro'
													AND witem_menu.id::text = '$parametro2'
													AND wpermisos.activo = 't' 
													AND witem_menu.activo = 't' ORDER BY id_item ASC LIMIT 1) END AS nombre_principal,
													witem_menu.id as id_item,
													witem_menu.descripcion,
													witem_menu.parent
													FROM wusuarios 
													INNER JOIN wpermisos ON wpermisos.cod_usua = wusuarios.cod_usua
													INNER JOIN witem_menu ON witem_menu.id = wpermisos.id_item 
													WHERE wusuarios.cod_usua = '$parametro'
													AND witem_menu.parent = '1' ORDER BY 4;");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();

														$datos["cod_usua"] 			= $row["cod_usua"];
														$datos["nombre"]			= $row["nombre"];
														$datos["perfil"] 			= $row["item_principal"];
														$datos["id_item"] 			= $row["id_item"];
														$datos["descripcion"] 		= $row["descripcion"];
														$datos["parent"] 			= $row["parent"];
														
														// push single product into final response array
														array_push($response["resultado"], $row);
													}
													$response["success"] = true;
													$response["message"] = "Inicio de sesión éxitoso.";
													//echo json_encode($response);
						$valor;
						$data = $response["resultado"];
						for ($x = 0; $x < count($data); $x++) {
							$valor = '{"cod_usua":"'.$data[$x]["cod_usua"].'","nombre":"'.$data[$x]["nombre"].'","perfil":"'.$data[$x]["item_principal"].'","nombre_perfil":"'.$data[$x]["nombre_principal"].'", "perfiles": ['.childrens( $data ).']},';
						}
						$valor = trim($valor, ',');
						$manage = json_decode("[".$valor."]");
						$manage["success"] = true;
						$manage["message"] = "Inicio de sesión éxitoso.";
						echo json_encode($manage);

						}else{
								$response["success"] = false;
								$response["message"] = "El usuario o contraseña no coincide.";
								// echo no users JSON
								echo json_encode($response);
						}
					}else if($codigo == '4'){
						$result = pg_query($conn, 	"SELECT id, 
													descripcion,
													(SELECT id_item FROM wpermisos WHERE id_item = witem_menu.id AND cod_usua = $parametro) AS permiso 
													FROM witem_menu 
													WHERE parent = 1 
													AND activo = true
													ORDER BY 1 DESC");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["id"] 			= $row["id"];
														$datos["descripcion"] 	= $row["descripcion"];
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
					}else if($codigo == '5'){
						$result = pg_query($conn, 	"select wpermisos_usuario('$codigo1', '$parametro', '$parametro2')");
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
					}else if($codigo == '6'){
						$result = pg_query($conn, 	"SELECT cod_usua,
													nombre
													FROM wusuarios
													WHERE id_rol <> 1
													ORDER BY 1 DESC");
						if(pg_num_rows($result) > 0)
						{	
													$response["resultado"] = array();
													while ($row = pg_fetch_array($result)) {
													$datos = array();
														
														$datos["cod_usua"] 	= $row["cod_usua"];
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