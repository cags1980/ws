<?php


function cancela($guia){

    $sql = "select s.usr_transporte_origen from CALENDARIZACION c
            inner join SUCURSALES s ON c.id_sucursal_origen = s.id_sucursal
            WHERE c.guia_despacho = '".$guia."'";
    $result_usr = trae_array($sql);
    $usuario = $result_usr[0]['usr_transporte_origen'];
    $pass = "7c222fb2927d828af22f592134e8932480637c0d";

    $params = array(
        'linea' => '3',
        'guia' => $guia,
        'docref' => '',
        'vp_linea' => '3'
    );

    $data = array('json' => json_encode($params));
    $postdata = http_build_query($data);
    $url = "https://app.urbanoexpress.cl/ws/ue/cancela_ge";
    $headers = array(
        'http' => array(
            'method' => "POST",
            'header' => "Content-type: application/x-www-form-urlencoded" . "\r\n" .
                "user: " . $usuario . "\r\n" .
                "pass: " . $pass . "\r\n",
            'content' => $postdata
        )
    );
    $context = stream_context_create($headers);
    $response = @file_get_contents($url, false, $context);
    return $response;
}
?>