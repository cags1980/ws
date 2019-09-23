<?php

class callAppleApi
{

    public $token;
    public $token_operador;
    public $url;
    public $data;

    public function __construct()
    {
        $this->url = 'https://partner-connect-uat.apple.com/gsx/api/';

        $this->data="";

    }

    public function consulta(){




    }

}

/* $data = array('userAppleId'=>'claro_tech_aprvlnotreqrd@claro.com',
'authToken'=>'a850e04d-a2e3-49dd-a97d-7aadd5d62aba'
); */
$data = '{"userAppleId":"claro_tech_aprvlnotreqrd@claro.com",
    "authToken": "898353b3-fe56-488c-b01d-783558ac4b2a"}';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://partner-connect-uat.apple.com/gsx/api/authenticate/token");
curl_setopt($ch, CURLOPT_PORT, 443);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Apple-ShipTo: 0000643298',
    'X-Apple-SoldTo: 0000643298',
    'X-Operator-User-ID: claro',
    'Content-Type: application/json',
    'Accept: application/json',
));
curl_setopt($ch, CURLOPT_SSLCERT, getcwd() . "/AppleCare-Partner-0000643298.Test.apple.com.chain.pem");
curl_setopt($ch, CURLOPT_SSLKEY, getcwd() . "/privatekey.pem");
//curl_setopt($ch, CURLOPT_CAINFO, "/AppleCare-Partner-0000643298.Test.apple.com.cert.pem");
curl_setopt($ch, CURLOPT_SSLCERTPASSWD, 'Logytech2019');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
$info = curl_errno($ch) > 0 ? array("curl_error_" . curl_errno($ch) => curl_error($ch)) : curl_getinfo($ch);
print_r($info);
curl_close($ch);
echo $response;
$infocurl = curl_version();
echo 'Version cURL: ', $infocurl['version'];
$shell = "curl --globoff -X  POST\
https://partner-connect-uat.apple.com/gsx/api/authenticate/token \
-H 'X-Apple-ShipTo: 0000643298' \
-H 'X-Apple-SoldTo: 0000643298' \
-H 'X-Operator-User-ID: Claro' \
-H 'Content-Type: application/json' \
-H 'Accept: application/json' \
--cert 'AppleCare-Partner-0000643298.Test.apple.com.chain.pem' \
--key 'privatekey.pem' \
--pass 'Logytech2019' \
-d '{
\"userAppleId\":\"claro_tech_aprvlnotreqrd@claro.com\",
\"authToken\": \"1af5e95c-a75b-4b0e-80b9-03444fdb785a\"
}'";

//echo $shell;
/* $reponses=shell_exec ( $shell);

print_r($reponses);

exec("ls", $o);
print_r ($o);
$cmd=$shell;

echo $cmd;
echo exec ($cmd);
?> */
