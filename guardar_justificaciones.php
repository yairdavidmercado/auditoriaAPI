
<?php 

include 'conexion.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0);       

$cod_audi            	= $data['cod_audi'];
$id_componente          = $data['id_componente'];
$observacion            = $data['observacion'];	
$cod_crea   			   = $data['cod_crea'];
$perfil              	= $data['perfil'];
$tipo              	   = $data['tipo'];

$conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);

try{
if($conn){
$result = pg_query($conn, "SELECT wguardar_wjustificaciones('".$cod_audi."', '".$id_componente."', '".$observacion."', '".$cod_crea."', '".$perfil."', '".$tipo."');");
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
