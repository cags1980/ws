<?php

class CallCargoEX{
public $conex;
public $db;
Public $usuario;
public $password;
public $token;
public $token_operador;
public $content;
public $date;
public $data;
public $method;
public $curl;



public function __construct(){
   
   $this->date=date("Y-m-d");
   $this->token = 'c935757c-4e89-4574-9ba6-c3348d11cb7a';
   $this->content = 'application/json';
   $this->url1 = "https://partner-connect-uat.apple.com/gsx/api/authenticate/token";
   $this->url = "https://partner-connect-uat.apple.com/gsx/api/authenticate/token";
   $this->curl = '';
   $this->method = 'POST';
   $this->data = '';

}

public function conect(){
   $this->conex = mssql_connect('10.51.128.185', 'cellstaradm', 'cellstaradm');
 
   if (!$this->conex){
      echo 'Error de Conexion';
       exit();
   }else{
       $this->db = mssql_select_db('Despachos', $this->conex);
      //  echo $this->db;
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
 
   $query = " Select 
 a.*, b.*,
   
   c.nombre_sucursal as nom_origen ,d.nombre_sucursal as nom_destino   , c1.iata as  iata_origen,c2.iata as iata_destino  ,
             d.direccion as destino, c.ubigeo as ubiorigen, d.ubigeo as ubidest, 
             c.usr_transporte_origen, c.pass_transporte_origen  
             from CALENDARIZACION a
             --, TRANSPORTE_DETALLE b, SUCURSALES c, SUCURSALES d
             left join TRANSPORTE_DETALLE b on a.id_calendario = b.id_calendario
             left join sucursales c on a.id_sucursal_origen = c.id_sucursal
             left join sucursales d on a.id_sucursal_destino = d.id_sucursal
            left join COMUNAS c1 on c1.id_comuna=c.id_comuna 
            left join COMUNAS c2 on c2.id_comuna=d.id_comuna
             where a.id_calendario = $did  ";

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

function callAPI($data){
    $this->data=$data;
   $this->curl = curl_init();
//print_r($this->curl);
   switch ($this->method){

    case "POST":
         curl_setopt($this->curl, CURLOPT_POST, 1);
         if ($this->data)
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
         break;
       case "GET":
         curl_setopt($this->curl, CURLOPT_GET, 1);
         if ($this->data)
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
         break;
      
      case "PUT":
         curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
               if ($this->method)
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);			 					
         break;
      default:
         if ($this->data)
         $this->url = sprintf("%s?%s", $this->curl, http_build_query($this->data));
   }

   // OPTIONS:
   curl_setopt($this->curl, CURLOPT_URL, $this->url);
   curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
      'X-Apple-SoldTo:0000643298',
      'X-Apple-ShipT:0000643298',
      'X-Operator-User-ID:claro',
      'Content-Type:application/json',
      'Accept:application/json',
   ));
     curl_setopt ($this->curl, CURLOPT_SSLCERT, getcwd().'/AppleCare-Partner-0000643298.Test.apple.com.chain.pem');
   curl_setopt ($this->curl, CURLOPT_SSLKEY,getcwd().'/privatekey.pem');
   curl_setopt($this->curl, CURLOPT_CAINFO, "/AppleCare-Partner-0000643298.Test.apple.com.cert.pem");
   curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, 'Logytech2019');
   curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1);
   curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($this->curl);
   $info2 =curl_errno($this->curl)>0 ? array("curl_error_".curl_errno($this->curl)=>curl_error($this->curl)) : curl_getinfo($this->curl);
   print_r($info);
   if(!$result){die("Connection Failure");}
   curl_close($this->curl);
   return $result;
}

function setTracking($did){
    $data='{"userAppleId":"claro_tech_aprvlnotreqrd@claro.coms",
        "authToken": "f755d811-6397-4273-8ce2-817614fb28ba"}';

    $result=$this->callAPI($data);


    print_r($result);
//$DatosDespachos=$this->CallLogitech($did);

/*
var_dump($DatosDespachos);
echo "<br><br>";
echo "<br><br>";
print_r($DatosDespachos[0]);
echo "<br><br>";
*/


//$consultae=json_decode($respuestae,true);
//$consultar=json_decode($respuestar,true);





//print_r($consulta);


return $consulta;

}

}
$id_consulta='24092';
$consulta_r=new  CallCargoEX();


$respuesta3=$consulta_r->setTracking($id_consulta);
print_r($respuesta3);

/*
//7420-24092
$id_consulta='24092';



 $consulta_r=new  CallCargoEX();


 $respuesta3=$consulta_r->setTracking($id_consulta);
echo "<br><br>";
echo "<br><br>";
echo "<br><br>";
 //var_dump(json_decode($respuesta));
$consulta=json_decode($respuesta,true);
$consultax=json_decode($respuesta);
 print_r($$respuesta3) ;

 echo $consulta['id'];
 echo $consultax->id;


 print_r($respuesta2);
*/
 
?>