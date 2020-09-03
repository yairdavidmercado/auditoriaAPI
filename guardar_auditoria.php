
<?php 

include 'conexion.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0);       
          
 		$cod_admi                     = $data['cod_admi'];
 		$cod_usua                     = $data['cod_usua'];
        $servicio                     = $data['servicio'];
        $eps                          = $data['eps'];
        $cama                         = $data['cama'];
        $perfil                       = $data['perfil'];
 
 $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);

	try{
		if($conn){
		$result = pg_query($conn, "SELECT wguardar_auditorias('".$cod_admi."', '".$cod_usua."', '".$servicio."', '".$eps."', '".$cama."', '".$perfil."' );");
		$fch = pg_fetch_row($result);
		
		$response["success"] = true;
		$response["message"] = $fch[0];
		echo json_encode($response);
		}
		else{
			$response["success"] = false;
			$response["message"] = "Ocurrio un error en la conexion";
			echo json_encode($response);
		}
	}catch(Exception $e){
		$response["success"] = false;
		$response["message"] = $e->getMessage();
		echo json_encode($response);
	}
	pg_close($conn);

?>
