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
        $this->usuario = 'UWS_SOLUTION';
        $this->password = '323b089e927c90f8b11f213ec356934f248f7ae4';
        //$this->url = 'http://app.urbanoexpress.cl:8000';
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
		
        $query = " Select a.*, b.*, c.nombre_sucursal as nom_origen, d.nombre_sucursal as nom_destino,
					d.direccion as destino, c.ubigeo as ubiorigen, d.ubigeo as ubidest, 
					c.usr_transporte_origen, c.pass_transporte_origen
					from CALENDARIZACION a, TRANSPORTE_DETALLE b, SUCURSALES c, SUCURSALES d
					where a.id_calendario = b.id_calendario
					and a.id_sucursal_origen = c.id_sucursal and a.id_sucursal_destino = d.id_sucursal 
					and a.id_calendario = $did  ";

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

		//echo $did;
		//exit();
        //Recupera todos los datos de MSSQL
        $arraData = $this->CallLogitech($did);
		//var_dump($arraData);
        foreach ($arraData as $key => $fila) {
            $despacho_id[$key]  = $fila['id_calendario']; 
            $caja_id_despacho[$key] = $fila['codigo_producto'];
        }
        //ordenamos ascendente por la columna elegida
        array_multisort($despacho_id, SORT_ASC,$caja_id_despacho,SORT_ASC, $arraData);

        $registro = 0;
        $cnt = 0;
        $final = array();
        $despacho_id = 0;
		//echo "<PRE>";
		//var_dump($arraData);
        foreach ($arraData as $key => $value) {
            //if ($key < 4){
				$usrtransp = $value['usr_transporte_origen'];
				$pastransp = $value['pass_transporte_origen'];
                if ($cnt == 0){
                        $final[$registro]['linea'] = '3';
						$final[$registro]['id_contrato'] = '1042';
                        $final[$registro]['cod_rastreo'] = $value['codigo_producto'];
                        $final[$registro]['nom_empresa'] = $value['nom_origen'];//
                        $final[$registro]['cod_cliente'] = $value['id_sucursal_destino'];//
                        $final[$registro]['nom_cliente'] = "Logytech";//
                        $final[$registro]['nom_autorizado'] = $value['id_sucursal_origen'];
                        $final[$registro]['fecha_despachado'] = $value['fecha'];
                        $final[$registro]['nro_o_compra'] = $value['id_calendario'];
                        $final[$registro]['nro_factura'] = $value['id_calendario'];//
                        $final[$registro]['factura_cellstar'] = $value['id_calendario'];
                        $final[$registro]['factura_otros'] = $value['id_calendario'];
                        $final[$registro]['cliente_dominio'] = $value['id_sucursal_origen'];
                        $final[$registro]['folio_externo'] = $value['id_calendario'];
                        $final[$registro]['cod_sku'] = $value['codigo_producto'];//
                        $final[$registro]['dir_entrega'] = utf8_encode($value['destino']);//
                        $final[$registro]['ref_direc'] = utf8_encode($value['destino']);
                        $final[$registro]['ubi_direc'] = $value['ubidest'];//
					$cnt=1;		
                }

                        $final[$registro]['productos'][$cnt]['caja_id_despacho'] = $value['id_calendario'];
                        $final[$registro]['productos'][$cnt]['vp_peso_sku'] = 9;//
                        $final[$registro]['productos'][$cnt]['marca'] = $value['codigo_producto'];
                        $final[$registro]['productos'][$cnt]['modelo'] = $value['descripcion_producto'];
                        $final[$registro]['productos'][$cnt]['vp_valor_sku'] = 0;
                        $final[$registro]['productos'][$cnt]['vp_cantidad_sku'] = 1;//
                        //$registro++;
 
                    //$caja_id_despacho = $value['id_calendario']; 

            //}
        }
        
        foreach ($final as $key => $value) {
            $this->apiWS($value,$did,$usrtransp,$pastransp);
        }
    }
    public function apiWS($params,$did,$usrtransp,$pastransp){
        $this->conect();
		//$usuario = $this->usuario;
		//$password = $this->password;
        $usuario = $usrtransp;
        $password = $pastransp;
	//	var_dump($params);

        $data = array('json' => json_encode($params));
		//echo "------------ data----------------";
		//var_dump($data);
        $postdata = http_build_query($data);
		//var_dump($postdata);
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
		//echo "------------ header----------------";
		//var_dump($headers);
        $context = @stream_context_create($headers);
        $response = @file_get_contents($url, false, $context);
        $get = @json_decode($response,true);
        if($_SESSION['usuario'] == 'admin'){
            var_dump($get);

        }
        if ($get['error'] < 0){
            echo $get['mensaje'].'<br>';
            echo $params['vp_cod_rastreo'].'<br>';
			$idtrack_err = $get['mensaje'];
			$query = " update calendarizacion set guia_despacho_error =  '$idtrack_err' where id_calendario =$did " ;
			$data = mssql_query($query);
        }else{
           // echo $get['guia'].'<br>';
			$idtrack = $get['guia'];
            $query = " update calendarizacion set guia_despacho =  '$idtrack' where id_calendario =$did " ;
            $data = mssql_query($query);
        }
    }
}
//$did = $_POST['did'];
//$did =2034;
//$L = new logytechController();
//$L->ws($did);

?>
