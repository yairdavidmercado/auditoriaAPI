<?php 
 include 'conexion.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);
error_reporting(0); 

                $codigo 	    = $data["codigo"]; 
                $etiqueta1      = $data["etiqueta1"]; 
                $clase1         = $data["clase1"]; 
                $dependencia1   = $data["dependencia1"]; 
                $value11        = $data["value11"]; 
                $type11         = $data["type11"]; 
                $ranking1       = $data["ranking1"]; 
                $perfil1        = $data["perfil1"];
                $id_componente1 = $data["id_componente1"]; 


                //parametros de conexion a la base de datos del cliente
			    $conn = pg_connect("user=".DB_USER." password=".DB_PASS." port=".DB_PORT." dbname=".DB_NAME." host=".DB_HOST);
				$arr = null;
                $dependencia = '';
				if ($conn) {
                    if ($codigo == '1') {// CARD COMPLETO
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'card', '$dependencia1', '', '', '$ranking1', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $dependencia1 = '';
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'card-header', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                $dependencia1 = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'h3', 'card-title', '$dependencia1', 'PREGUNTAS', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia1 = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'card-body', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                        $dependencia1 = $response["resultado"][0]["wcrear_sub_control"];
                                        $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'row', '$dependencia1', '', '', '1', '$perfil1', '$id_componente1')");
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
                                            $dependencia1 = $response["resultado"][0]["wcrear_sub_control"];
                                            $dependencia2 = '';
                                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '1', '$perfil1', '$id_componente1')");
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
                                                $dependencia2 = $response["resultado"][0]["wcrear_sub_control"];
                                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia2', 'Lorem ipsum Titulo general', '', '1', '$perfil1', '$id_componente1')");
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

                                            }

                                        }

                                    }

                                }

                            }

						}
                    }else if($codigo == '2'){// PREGUNTA COMPLETA SI NO
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha final', '', '4', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '5', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Observación', '', '6', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'textarea', 'form-control', '$dependencia', '', '', '7', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'SI', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO', 'radio', '2', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '3'){// PREGUNTA SIN FECHAS SI NO
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Observación', '', '6', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'textarea', 'form-control', '$dependencia', '', '', '7', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'SI', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO', 'radio', '2', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '4'){// PREGUNTA SIN FECHAS NI OBSERVACION SI NO
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'SI', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO', 'radio', '2', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '5'){// PREGUNTA CON FECHAS Y SI NO
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha final', '', '4', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '5', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'SI', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO', 'radio', '2', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '6'){// PREGUNTA COMPLETA CUMPLE NO CUMPLE NO APLICA
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha final', '', '4', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '5', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Observación', '', '6', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'textarea', 'form-control', '$dependencia', '', '', '7', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'CUMPLE', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO CUMPLE', 'radio', '2', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO APLICA', 'radio', '3', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '7'){// PREGUNTA SIN FECHAS CUMPLE NO CUMPLE NO APLICA
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Observación', '', '6', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'textarea', 'form-control', '$dependencia', '', '', '7', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'CUMPLE', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO CUMPLE', 'radio', '2', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO APLICA', 'radio', '3', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '8'){// PREGUNTA SIN FECHAS NI OBSERVACION CUMPLE NO CUMPLE NO APLICA
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-12', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'CUMPLE', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO CUMPLE', 'radio', '2', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO APLICA', 'radio', '3', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '9'){// PREGUNTA CON UNA FECHA Y SI NO
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-6', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'SI', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO', 'radio', '2', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '10'){// PREGUNTA CON FECHAS Y CUMPLE NO CUMPLE NO APLICA SIN OBSERVACION
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-6', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha final', '', '4', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '5', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'CUMPLE', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO CUMPLE', 'radio', '2', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO APLICA', 'radio', '3', '$perfil1', '$id_componente1')");
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

                                }

                            }

                        }
					}else if($codigo == '11'){// PREGUNTA CON FECHA Y CUMPLE NO CUMPLE NO APLICA SIN OBSERVACION
                        $level = 0;
						$result = pg_query($conn, 	"SELECT ranking+1 as ranking FROM wsubcomponentehtml WHERE dependencia = $dependencia1 AND perfil = $perfil1 order by ranking desc limit 1");
						if(pg_num_rows($result) > 0)
						{	
                            $response["resultado"] = array();
                            while ($row = pg_fetch_array($result)) {
                            $datos = array();
                                
                                $datos["ranking"] 			= $row["ranking"];
                                
                                // push single product into final response array
                                array_push($response["resultado"], $datos);
                            }
                            $response["success"] = true;
                            $level = $response["resultado"][0]["ranking"];
                            
						}else{
                            $response["success"] = false;
                            $response["message"] = "No se encontraron registros";
                            $level = 1;
                            // echo no users JSON
                            //echo json_encode($response);
                        }
                        
                        $result = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'col-sm-6', '$dependencia1', '', '', '$level', '$perfil1', '$id_componente1')");
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
                            $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Lorem ipsum Pregunta', '', '1', '$perfil1', '$id_componente1')");
                            $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'form-group clearfix', '$dependencia', '', '', '2', '$perfil1', '$id_componente1')");
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
                                $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'p', '', '$dependencia', 'Fecha inicial', '', '2', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', 'form-control', '$dependencia', '', 'date', '3', '$perfil1', '$id_componente1')");
                                $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'div', 'icheck-primary d-inline', '$dependencia', '', '', '1', '$perfil1', '$id_componente1')");
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
                                    $dependencia = $response["resultado"][0]["wcrear_sub_control"];
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'CUMPLE', 'radio', '1', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO CUMPLE', 'radio', '2', '$perfil1', '$id_componente1')");
                                    $result1 = pg_query($conn, "SELECT wcrear_sub_control('1', 'input', '', '$dependencia', 'NO APLICA', 'radio', '3', '$perfil1', '$id_componente1')");
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

                                }

                            }

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
?>