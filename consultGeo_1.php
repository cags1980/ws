<?php
include_once('OAuth.php');
$base_feed='https://apiv3.geovictoria.com/api/AttendanceBook/GetAttendance';
$base_feed='https://apiv3.geovictoria.com/api/Group/ListGroup';
//$base_feed='http://apiv3.geovictoria.com/api/User/List';	https://apiv3.geovictoria.com/api/Group/Paths
$base_feed='https://apiv3.geovictoria.com/api/Punch/List';
//$consumer=(object) array("key"=>"s3efec","secret"=>"d4da93e$");
$CONSUMER_KEY = '55cd12'; 
$CONSUMER_SECRET = 'ade2f88e'; 
$consumer = new OAuthConsumer($CONSUMER_KEY, $CONSUMER_SECRET, NULL);

//print_r($consumer);
//$params= array('Range' => '0BzQVmpoq81SaUT6hBxMeQ', 'from' => '20190701000000', 'to' => '20190916235959', 'includeAll' => 0 );
//$params= array('Range' => 'currentWeek', 'from' => '', 'to' => '' );
$params= array('Range' => 'currentDay','from' => null, 'to' =>null  );
//$params=null;
$request = OAuthRequest::from_consumer_and_token($consumer, NULL, 'POST', $base_feed, $params);
// Sign the constructed OAuth request using HMAC-SHA1
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
// Make signed OAuth request to the Contacts API server
$url = $base_feed . '?' . implode_assoc('=', '&', $params);
echo send_request($request->get_normalized_http_method(), $url, $request->to_header());
/**
* Makes an HTTP request to the specified URL
* @param string $http_method The HTTP method (GET, POST, PUT, DELETE)
* @param string $url Full URL of the resource to access
* @param string $auth_header (optional) Authorization header
* @param string $postData (optional) POST/PUT request body
* @return string Response body from the server
*/
function send_request($http_method, $url, $auth_header=null, $postData=null) {
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FAILONERROR, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
switch($http_method) {
case 'GET':
if ($auth_header) {
curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
}
break;
case 'POST':
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml',
$auth_header));
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
break;
case 'PUT':
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml',
$auth_header));
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
break;
case 'DELETE':
curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
break;
}
$response = curl_exec($curl);
//print_r($auth_header);
//print_r($response);
if (!$response) {
$response = curl_error($curl);
}
curl_close($curl);
return $response;
}
/**
* Joins key:value pairs by inner_glue and each pair together by outer_glue
* @param string $inner_glue The HTTP method (GET, POST, PUT, DELETE)
* @param string $outer_glue Full URL of the resource to access
* @param array $array Associative array of query parameters
* @return string Urlencoded string of query parameters
*/
function implode_assoc($inner_glue, $outer_glue, $array) {
$output = array();
foreach($array as $key => $item) {
$output[] = $key . $inner_glue . urlencode($item);
}
return implode($outer_glue, $output);
}
?>