<?php
 include 'conexion.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

error_reporting(0);

$perfil = isset($_GET['perfil']) ? $_GET['perfil']: 3;

$limit = 10;
$page = isset($_GET['page']) ? $_GET['page']: 1;
$start = ($page -1) * $limit;
$nombre = isset($_GET['nombre']) ? $_GET['nombre']: '';
$table = <<<EOT
 (
    SELECT cod_usua, 
    nombre,
    fechacrea,
    (SELECT count(id) FROM wusuarios
    WHERE nombre LIKE '%$nombre%') AS totalrows
    FROM wusuarios
    WHERE nombre LIKE '%$nombre%'
    ORDER BY cod_usua DESC 
    OFFSET $start limit $limit
 ) temp
EOT;
 
// Table's primary key
$primaryKey = '1';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array(
        'db' => 'cod_usua',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'cod_usua',  'dt' => 'cod_usua' ),
    array( 'db' => 'nombre',  'dt' => 'nombre' ),      
    array( 'db' => 'fechacrea',     'dt' => 'fechacrea' ),
    array( 'db' => 'totalrows',     'dt' => 'totalrows' )
);
 
 //pgsql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME, DB_USER, DB_PASS);
// SQL server connection information
$sql_details = array(
    'user' => DB_USER,
    'pass' => DB_PASS,
    'db'   => DB_NAME,
    'host' => DB_HOST,
    'charset' => 'utf8'
);


require( 'ssp.class.pg.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
