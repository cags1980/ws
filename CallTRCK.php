<?php
/**
 * @link
 * @author  Robert Salvatierra Q robertsalvatierraq@gmail.com
 * @version 1.0 para Chile
 **/

class logytechController {

    public $conex;
    public $db;
    public $usuario;
    public $password;
    public $url;
    public function __construct(){
        $this->usuario = 'LOGY_WS_33';
        $this->password = '7c222fb2927d828af22f592134e8932480637c0d';
        $this->url = 'https://app.urbanoexpress.cl';
    }
    public function conect(){
        $this->conex = mssql_connect('10.51.128.248', 'cellstaradm', 
'cellstaradm');
        if (!$this->conex){
            exit();
        }else{
            $this->db = mssql_select_db('Despachos', $this->conex);
        }
    }
    public static function iaLower($array_) {
        $array = array();
        foreach($array_ as $index => $value){
            foreach($value as $index01 => $value01){
                $array[$index][strtolower($index01)] = $value01;
            }
        }
        return $array;
    }
    public function CallLogitech($did){
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $this->conect();
		
        $query = " Select * from TRANSPORTE_DETALLE where id_calendario =4  ";
        $data = mssql_query($query);
        $num_rows = mssql_num_rows($data);
        if (!empty($num_rows) || intval($num_rows) > 0){
            mssql_data_seek($data, 0);
            while ($row = mssql_fetch_array($data, MSSQL_BOTH)) {
                $arrayData[] = $row;
            }
            $arrayData = $this->iaLower($arrayData);
        }
        return $arrayData;
    }
    
	public function ws($did){
		$guia = $did;
		//echo $guia;
        $params = array(
			'guia' => $did,
			'docref' => '',
			'vp_linea' => 3);
        $this->apiWS($params);

    }
    
	public function apiWS($params){
        $this->conect();
        $usuario = $this->usuario;
        $password = $this->password;
		
        $data = array('json' => json_encode($params));
		
        $getdata = http_build_query($data);
		//echo "<pre>";
		//print_r($getdata);
        $url = $this->url.'/ws/ue/tracking/?'.$getdata;
        $headers = array(
            'http' => array(
                'method' => "GET",
                'header' => "Content-type: application/x-www-form-urlencoded" . "\r\n" .
                            "user: " . $usuario . "\r\n" .
                            "pass: " . $password . "\r\n"
            )
        );
        $context = stream_context_create($headers);
        $response = file_get_contents($url, false, $context);
        $get = json_decode($response,true);
		//echo $response;
		//var_dump($get);
        if ($get['error'] < 0){
            echo $get['mensaje'].'<br>';
            echo $params['vp_cod_rastreo'].'<br>';
        }else{
            //echo $get['guia'].'<br>';
			//(print_r($get);
				$idcal = 0;
				$guia = $get[0]['guia'];

				
				$query = " Select id_calendario from CALENDARIZACION where guia_despacho = '$guia' ";
				$data = mssql_query($query);
				$num_rows = mssql_num_rows($data);
				if (intval($num_rows) > 0){
					while ($row = mssql_fetch_array($data))//loop through the retrieved values
					{
						$idcal=$row['id_calendario'];
					}					
				}  

			foreach($get[0]['movimientos'] as $movim) {
				 $ch = trim($movim["chk"]); 				 
				 $fec = $movim["fecha"];
				 echo $fec2 = implode('-', array_reverse(explode('/', $fec)));
                    echo "<br>";
				 echo $hor = $movim["hora"];
				 $estad = $movim["estado"];
				 $subestad = $movim["sub_estado"];
				 $apunt = $movim["apunts"]; 
				 $agenci = $movim["agencia"];
				 $gps_px1 = $movim["gps_px"] == '' ? '0' : $movim["gps_px"]; 
				 $gps_py1 = $movim["gps_py"] == '' ? '0' : $movim["gps_py"];				
				
				$query = "Select * from CALENDARIZACION_TRACK_TRANSP where id_calendario = $idcal and chk = '$ch' and fecha = '$fec2' and hora = '$hor'";
				$data = mssql_query($query);
				$num_rows = mssql_num_rows($data);
			//	echo trim($movim["chk"]) . "<BR>";
			//	echo intval($num_rows);
				if (intval($num_rows) == 0){
					$query = " Insert into CALENDARIZACION_TRACK_TRANSP (id_calendario, guia_despacho, 
					chk, fecha, hora,estado, subestado, apunts, agencia, gps_px, gps_py) 
					VALUES ($idcal,'$guia','$ch', '$fec2', '$hor','$estad', '$subestad', '$apunt', '$agenci', '$gps_px1', '$gps_py1')";
					//echo $query;
					$data = mssql_query($query);						
				}

                if(intval(count($get[0]['img'])) > 0) {
                    foreach ($get[0]['img'] as $img) {
                        $url_img = $img['img_path'];
                        $fecha = implode('-', array_reverse(explode('/', $img['img_fecha']))).' '.$img['img_hora'];
                        $query_img = " Select * from CALENDARIZACION_IMAGEN where id_calendario = $idcal and url_imagen = '$url_img' ";
                        $data_img = mssql_query($query_img);
                        $num_rows_img = mssql_num_rows($data_img);
                        if($num_rows_img == 0){
                            $sql_insert = "INSERT INTO CALENDARIZACION_IMAGEN (id_calendario,guia_despacho,url_imagen,fecha) VALUES ('".$idcal."','".$guia."','".$url_img."','".$fecha."')";
                            mssql_query($sql_insert);
                        }
                    }
                }
				//echo $movim["sub_estado"]."<BR>";	
			
			}
			//insertar con los campos de la tabla el contenido del objeto
			
            //$query = " Insert into CALENDARIZACION_TRACK_TRANSP '".$params['vp_cod_rastreo']."','".$get['guia']."' ";
            //$data = mssql_query($query);
        }
    }
}
$did = $_POST['did'];
//$did = 'WYB46205121';
$L = new logytechController();
$L->ws($did);

?>
