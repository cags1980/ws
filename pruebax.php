<?php

echo substr("2019-08-12T17:28:35.775118Z",0,23)."<br>";
http://10.51.128.73/wstrack/images/p1_id_48851.png

echo substr("http://10.51.128.73/wstrack/images/p1_id_48851.png",35)."<br>";

$hora='-1';

$horas_negativas= strpos($hora, '-');

var_dump($horas_negativas);
        
if($hora==0 or $horas_negativas !== false ){
    $horae=''; 
}else{

    $horae=$hora.' Hrs';

}

echo $horae
?>