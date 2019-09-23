<?php

class callAppleApi
{

    public $token;
    public $token_operador;
    public $url;
    public $url_findMyIphone;
    public $data;
    public $curl;
    public $method;


    public function __construct()
    {
        $this->url_findMyIphone= 'https://partner-connect-uat.apple.com/gsx/api/repair/eligibility';
        $this->data='{
            "device": {
                "id": "C06Y1AAAJCLJ"
            }
        }';
        $this->curl='';
        $this->method = 'GET';
        $this->token="c935757c-4e89-4574-9ba6-c3348d11cb7a";

    }

    public function consulta(){
//TODO:  ===== inicio el metodo  Curl para consutlar la Api Rest ===
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->url_findMyIphone);
        curl_setopt($this->curl, CURLOPT_PORT, 443);
        curl_setopt($this->curl, CURLOPT_VERBOSE, 0);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'X-Apple-ShipTo: 0000643298',
            'X-Apple-SoldTo: 0000643298',
            'X-Apple-Auth-Token:'.$this->token,
            'X-Operator-User-ID: claro',
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        curl_setopt($this->curl, CURLOPT_SSLCERT, getcwd() . "/AppleCare-Partner-0000643298.Test.apple.com.chain.pem");
        curl_setopt($this->curl, CURLOPT_SSLKEY, getcwd() . "/privatekey.pem");
        //curl_setopt($this->curl, CURLOPT_CAINFO, "/AppleCare-Partner-0000643298.Test.apple.com.cert.pem");
        curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, 'Logytech2019');
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
        
         $result=curl_exec($this->curl);
         if (!$result) {die("Connection Failure");}
        curl_close($this->curl);
        return $result;


    }

}



/* $data = array('userAppleId'=>'claro_tech_aprvlnotreqrd@claro.com',
'authToken'=>'a850e04d-a2e3-49dd-a97d-7aadd5d62aba'
); */
/* $data = '{
    "device": {
        "id": "C06Y1AAAJCLJ"
    }
}';
$this->curl = curl_init();
curl_setopt($this->curl, CURLOPT_URL, "https://partner-connect-uat.apple.com/gsx/api/repair/eligibility");
curl_setopt($this->curl, CURLOPT_PORT, 443);
curl_setopt($this->curl, CURLOPT_VERBOSE, 0);
curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
    'X-Apple-ShipTo: 0000643298',
    'X-Apple-SoldTo: 0000643298',
    'X-Apple-Auth-Token: ba931c2e-d8b9-4c71-a21a-f5d9791c344a',
    'X-Operator-User-ID: claro',
    'Content-Type: application/json',
    'Accept: application/json',
));
curl_setopt($this->curl, CURLOPT_SSLCERT, getcwd() . "/AppleCare-Partner-0000643298.Test.apple.com.chain.pem");
curl_setopt($this->curl, CURLOPT_SSLKEY, getcwd() . "/privatekey.pem");
//curl_setopt($this->curl, CURLOPT_CAINFO, "/AppleCare-Partner-0000643298.Test.apple.com.cert.pem");
curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, 'Logytech2019');
curl_setopt($this->curl, CURLOPT_POST, 1);
curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 1);
//curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
//curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data)); */

 //curl_exec($this->curl);
//$info = curl_errno($this->curl) > 0 ? array("curl_error_" . curl_errno($this->curl) => curl_error($this->curl)) : curl_getinfo($this->curl);
//print_r($info);
//curl_close($this->curl);
//echo $response;
//$infocurl = curl_version();



//echo 'Version cURL: ', $infocurl['version'];


$consulta=new callAppleApi();




echo '<br>';
echo '<pre>';
//$responseObj= json_decode($response);
//var_dump($responseObj);
$reponse=$consulta->consulta();
echo '<br>';
$reponseY=json_decode($reponse, true);
print_r($reponseY['eligibilityDetails']['outcome']['0']['reasons']['0']['messages']['0']);
echo '<br>';
$reponse='{
    "eligibilityDetails": {
        "outcome": [
            {
                "action": "WARNING",
                "reasons": [
                    {
                        "type": "WARNING",
                        "messages": [
                            "Warning: Find My iPhone is active. Find My iPhone must be turned off for whole unit repairs."
                        ]
                    }
                ]
            },
            {
                "reasons": [
                    {
                        "type": "REPAIR_TYPE",
                        "repairOptions": [
                            {
                                "option": "CIN",
                                "priority": 1
                            },
                            {
                                "option": "CINR",
                                "priority": 2
                            },
                            {
                                "option": "CRBR",
                                "priority": 3
                            },
                            {
                                "option": "WUMS",
                                "priority": 4
                            },
                            {
                                "option": "WUMC",
                                "priority": 5
                            },
                            {
                                "option": "SVNR",
                                "priority": 6,
                                "subOption": "LUA"
                            },
                            {
                                "option": "SVNR",
                                "priority": 7,
                                "subOption": "NTF"
                            },
                            {
                                "option": "SVNR",
                                "priority": 8,
                                "subOption": "SRC"
                            },
                            {
                                "option": "INON",
                                "priority": 9
                            }
                        ]
                    }
                ]
            }
        ],
        "coverageCode": "OO",
        "coverageDescription": "Out Of Warranty (No Coverage)",
        "technicianMandatory": false
    }
}';
$resultx=json_decode($reponse,true);
echo '<br>';
print_r($resultx['eligibilityDetails']['outcome']['0']['reasons']['0']['messages']['0']);
echo '</pre>';
//echo $shell;
/* $reponses=shell_exec ( $shell);

print_r($reponses);

exec("ls", $o);
print_r ($o);
$cmd=$shell;

echo $cmd;
echo exec ($cmd);
?> */
