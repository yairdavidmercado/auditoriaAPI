<?php
 include 'conexion.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header('Content-Type: text/html; charset=latin1');
error_reporting(0);

$limit = 10;
$page = isset($_GET['page']) ? $_GET['page']: 1;
$start = ($page -1) * $limit;
$cod_audi = isset($_GET['cod_audi']) ? $_GET['cod_audi']: '';
$cod_usua = isset($_GET['cod_usua']) ? $_GET['cod_usua']: '';
$table = <<<EOT
 (
    SELECT 
    cod_audi,
    (SELECT descripcion FROM witem_menu WHERE witem_menu.id = wdevoluciones.perfil::integer ),
    perfil,
    (SELECT nombre FROM wusuarios WHERE wusuarios.cod_usua = wdevoluciones.cod_usua ) as autor, 
    wdevoluciones.cod_usua,
    (SELECT count(cod_audi) FROM (SELECT 
    cod_audi,
    (SELECT descripcion FROM witem_menu WHERE witem_menu.id = wdevoluciones.perfil::integer ),
    perfil,
    (SELECT nombre FROM wusuarios WHERE wusuarios.cod_usua = wdevoluciones.cod_usua ) as autor, 
    wdevoluciones.cod_usua
    FROM 
    wdevoluciones 
    WHERE cod_audi::text LIKE '%$cod_audi%' AND cod_crea = $cod_usua group by 1,2,3,4,5) AS tabla) AS totalrows
    FROM 
    wdevoluciones 
    WHERE cod_audi::text LIKE '%$cod_audi%' AND cod_crea = $cod_usua group by 1,2,3,4,5
    ORDER BY cod_audi DESC 
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
        'db' => 'cod_audi',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'cod_audi',  'dt' => 'cod_audi' ),
    array( 'db' => 'descripcion',  'dt' => 'descripcion' ),      
    array( 'db' => 'autor',     'dt' => 'autor' ),
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
