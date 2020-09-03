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
$admision = isset($_GET['admision']) ? $_GET['admision']: '';

$table = <<<EOT
 (
    SELECT cod_audi,
    cod_admi,
    (SELECT nombre FROM wusuarios WHERE cod_usua = wauditorias.cod_usua limit 1 ) as nom_usua,
    eps,
    servicio,
    cama,
    fechacrea,
        (SELECT count(*) FROM
        (SELECT *
        FROM wauditorias 
        WHERE perfil = $perfil
        AND anulado = false 
        AND consec IN (SELECT MAX(consec) FROM wauditorias GROUP BY cod_admi) 
        AND cod_admi::text LIKE '%$admision%'
        ORDER BY consec DESC) as tabla) as totalrows 
    FROM wauditorias 
    WHERE perfil = $perfil
    AND anulado = false 
    AND consec IN (SELECT MAX(consec) FROM wauditorias GROUP BY cod_admi) 
    AND cod_admi::text LIKE '%$admision%'
    ORDER BY consec DESC OFFSET $start limit $limit
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
        'db' => 'cod_admi',
        'dt' => 'DT_RowId',
        'formatter' => function( $d, $row ) {
            // Technically a DOM id cannot start with an integer, so we prefix
            // a string. This can also be useful if you have multiple tables
            // to ensure that the id is unique with a different prefix
            return 'row_'.$d;
        }
    ),
    array( 'db' => 'cod_audi',  'dt' => 'cod_audi' ),
    array( 'db' => 'cod_admi',  'dt' => 'cod_admi' ),      
    array( 'db' => 'nom_usua',   'dt' => 'nom_usua' ),
    array( 'db' => 'eps',     'dt' => 'eps' ),
    array( 'db' => 'servicio',     'dt' => 'servicio' ),
    array( 'db' => 'cama',     'dt' => 'cama' ),
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
