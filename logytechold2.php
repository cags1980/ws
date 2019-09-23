<?php
/**
 * @link
 * @author  Robert Salvatierra Q robertsalvatierraq@gmail.com
 * @version 1.0 para Chile
 **/
//array(2) { ["error"]=> int(-1) ["mensaje"]=> string(35) "Tienes que indicar la Linea (linea)" } 
//array(1) { [0]=> array(2) { ["sql_error"]=> string(2) "-1" ["msg_error"]=> string(45) "Ocurrio un error al tratar validar el usuario" } } 
class logytechController {
    public $conn;
    public $serverName;
    public $connectionInfo;
    public $conex;
    public $db;
    public $usuario;
    public $password;
    public $url;
    public function __construct(){
        $this->usuario = 'LOGY_WS_001';
        $this->password = '7c222fb2927d828af22f592134e8932480637c0d';
		//$this->password = '12345678';
        $this->url = 'http://app.urbanoexpress.cl';
    }
    public function conect(){
        $serverName = "10.51.129.28"; //serverName\instanceName, portNumber (1433 by default)
        //$connectionInfo = array( "Database"=>"Fullstar_data", "UID"=>"Urbano", "PWD"=>"Sq1Urb2017");
        $conn = mssql_connect( $serverName, 'cellstaradm','cellstaradm');
        $this->conex = $conn;
                            
        if (!$this->conex){
            exit();
        }else{
            $this->db = mssql_select_db('FullStar_Data', $this->conex);
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
    public function CallLogitech(){
		//echo "aca";
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $this->conect();
        $query = " Execute SP_Despachos_Transporte_Detallado_test ";
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
    public function ws(){
        //Recupera todos los datos de MSSQL
        $arraData = $this->CallLogitech();
		
        foreach ($arraData as $key => $fila) {
            $despacho_id[$key]  = $fila['despacho_id']; 
            $caja_id_despacho[$key] = $fila['caja_id_despacho'];
        }
        //ordenamos ascendente por la columna elegida
        array_multisort($despacho_id, SORT_ASC,$caja_id_despacho,SORT_ASC, $arraData);

        $registro = 0;
        $cnt = 0;
        $final = array();
        $despacho_id = 0;

        foreach ($arraData as $key => $value) {
            //if ($key < 4){
                if ($key == 0){
                    $despacho_id = $value['despacho_id'];
                    $caja_id_despacho = $value['caja_id_despacho'];
                }

                if ($value['despacho_id'] != $despacho_id){
                    $despacho_id = $value['despacho_id'];
                    $registro++;
                    $cnt = 0;
                   //echo 'change';
                } 
                if ($value['despacho_id'] == $despacho_id){
                    if ($value['caja_id_despacho'] != $caja_id_despacho){
                        $caja_id_despacho = $value['caja_id_despacho'];
                        $cnt = 0;
                        $registro++;
                    }

                    if ($value['caja_id_despacho'] == $caja_id_despacho){
						
                        $final[$registro]['linea'] = '3';
						$final[$registro]['id_contrato'] = '980';
                        $final[$registro]['cod_rastreo'] = $value['despacho_id'];
						$final[$registro]['cod_barra'] = $value['despacho_id'];
                        $final[$registro]['nom_empresa'] = $value['cliente'];//
                        $final[$registro]['cod_cliente'] = $value['subcliente'];//
                        $final[$registro]['nom_cliente'] = $value['subclienterso'];//
                        $final[$registro]['nom_autorizado'] = $value['cli_rut'];
                        $final[$registro]['fecha_despachado'] = $value['fecha_despachado'];
                        $final[$registro]['nro_o_compra'] = $value['guia_numero'];
                        $final[$registro]['nro_factura'] = $value['factura_numero'];//
                        $final[$registro]['factura_cellstar'] = $value['factura_cellstar'];
                        $final[$registro]['factura_otros'] = $value['factura_otros'];
                        $final[$registro]['cliente_dominio'] = $value['cliente_dominio'];
                        $final[$registro]['folio_externo'] = $value['folio_externo'];
                        $final[$registro]['cod_sku'] = $value['tipo_producto'];//
						if ($value['dir_despacho'] == "") {
						$final[$registro]['dir_entrega'] = $value['dir_despacho2'];//	
						}else {
						$final[$registro]['dir_entrega'] = $value['dir_despacho'];//	
						}                      
                        $final[$registro]['ref_direc'] = $value['observacion'];
                        $final[$registro]['ubi_direc'] = '13101';//

                        //$final[$registro]['productos'][$cnt]['caja_id_despacho'] = $value['caja_id_despacho'];
						$final[$registro]['productos'][$cnt]['cod_sku'] = $value['caja_id_despacho'];
                        $final[$registro]['productos'][$cnt]['vp_peso_sku'] = $value['peso_caja'];//
                        $final[$registro]['productos'][$cnt]['marca'] = $value['marca'];
                        $final[$registro]['productos'][$cnt]['modelo'] = $value['modelo'];
                        $final[$registro]['productos'][$cnt]['vp_valor_sku'] = $value['mod_escrip'];
                        $final[$registro]['productos'][$cnt]['vp_cantidad_sku'] = $value['qtyunit'];//
                        $cnt++;
                    }else{
                        $registro++;
                    } 
                    $caja_id_despacho = $value['caja_id_despacho']; 
                }
            //}
        }
		echo "Objeto final". "<BR>";
        var_dump($final);
		echo "<BR>";
        foreach ($final as $key => $value) {
			echo "cada caja". "<BR>";
			var_dump($value);
			echo "<BR>";
            $this->apiWS($value);
        }
    }
    public function apiWS($params){
        $this->conect();
        $usuario = $this->usuario;
        $password = $this->password;

        $data = array('json' => json_encode($params));
        $postdata = http_build_query($data);
        $url = $this->url.'/ws/ue/ge/';
        $headers = array(
            'http' => array(
                'method' => "POST",
                'header' => "Content-type: application/x-www-form-urlencoded" . "\r\n" .
                            "user: " . $usuario . "\r\n" .
                            "pass: " . $password . "\r\n" ,
                'content' => $postdata
            )
        );
        $context = stream_context_create($headers);
        //$response = file_get_contents($url, false, $context);
        $get = json_decode($response,true);
		var_dump($get);
		echo "<BR>";
		$did = $params['cod_rastreo'];
        if ($get[0]['error'] < 0){
            //echo $get[0]['msg_error'].'<br>';
            //echo $params['vp_cod_rastreo'].'<br>';
			$mes = $get['mensaje'];
            $query = " update Despacho_Header set Rta_WS = '$mes'
					 where Despacho_ID = $did";
            $data = mssql_query($query);
        }else{
			
            //echo $get['guia'].'<br>';
			$gui = $get['guia'];
			
            $query = " update Despacho_Header set Courrier_Tracking = '$gui', Tracking_id = '$gui'
					 where Despacho_ID = $did";
            $data = mssql_query($query);
        }
    }
}
$L = new logytechController();
$L->ws();

?>
