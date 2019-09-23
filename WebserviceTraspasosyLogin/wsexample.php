<?php
/*
    Ejemplo de REST para conectarse a Webservice de Trotter S.A.
    Este ejemplo usa la librería httpful de la cual se puede encontrar 
    documentacion en la url: http://phphttpclient.com/#install
    Metodo: Traspaso Interno
    El traspaso interno consiste en traspasar entre bodegas virtuales
    como por ejemplo de la bodega de pendiente de preparación a la bodega
    de producto terminado.
*/

include "httpful.phar";
//Datos de productos a traspasar entre bodegas.
$data = array(
    array(
        "extuid"=> 6546789, //ID FullStar
        "bodegadestino"=> "1000", //Bodega Fullstar Destino
        "subbodegadestino"=> "500", //SubBodega FullStar Destino
        "bodegaorigen"=> "1000", //Bodega Fullstar Origen
        "subbodegaorigen"=> "400",//SubBodega FullStar Origen
        "producto"=> "0380090012", //Producto
        "cantidad"=> 1, //Cantidad
        "comentario"=> "Solicitud 636548" //Comentario en caso de no haber enviar ""
    ),
    array(
        "extuid"=> 9656775, //ID FullStar
        "bodegadestino"=> "1000", //Bodega Fullstar Destino
        "subbodegadestino"=> "400", //SubBodega FullStar Destino
        "bodegaorigen"=> "1000", //Bodega Fullstar Origen
        "subbodegaorigen"=> "400",//SubBodega FullStar Origen
        "producto"=> "0830040003", //Producto
        "cantidad"=> 1, //Cantidad
        "comentario"=> "Solicitud 927652" //Comentario en caso de no haber enviar ""
    ),
    );
//Credenciales
echo(hash("sha256", "logytech1234567890") . '<br>');

$login = array("data" => hash("sha256", "logytech1234567890"));

//Inicio de sesion
$cookie = "";

$responce = \Httpful\Request::post('http://troser.atrotter.com/login/wslogin/wslogin/login')
->sendsJson()//fija el tipo de contenido.
->expectsJson()//fija el contenido esperado de respuesta.
->body(json_encode($login))//escribe el cuerpo la lista traducida a JSON.
->send();//Envía la información al servidor.
print_r($responce->headers["set-cookie"]);
$cookie = $responce->headers["set-cookie"];
print_r($responce->body);
echo("<br>");

//Traspaso de mercaderia de acuerdo a lo almacenado en $data.
$responce = \Httpful\Request::post("http://troser.atrotter.com/bodegas/traspasos/traspasos/traspasoWS")
->sendsJson()//fija el tipo de contenido.
->expectsJson()//fija el contenido esperado de respuesta.
->body(json_encode($data))//escribe el cuerpo la lista traducida a JSON.
->addHeader("cookie", $cookie)//se agrega el codigo cookie para mantener la sesión.
->send();//Envía la información al servidor.


//Imprime la respuesta de nuestro servidor.
foreach($responce->body as $vals){
    print_r($vals);
    echo("<br>");
}
?>