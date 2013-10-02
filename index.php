<?php

$departureDates=array("2013-11-18","2013-11-19","2013-11-20","2013-11-21","2013-11-22",
                "2013-11-23","2013-11-24","2013-11-25","2013-11-26","2013-11-27",
                "2013-11-28","2013-11-29","2013-11-30"); //Fechas de salida

$minReturnDate= "2013-12-02"; //minima fecha de retorno
$stay= 8; //dias de estadia (8 minimo)
$destinationCode="mad"; //codigo del destino

echo 'Fecha de retorno minima: '.$minReturnDate;
echo "<br>";
echo 'Dias de estadia: '.$stay;
echo "<br>";
echo "<br>";

$minGlobal = 99999;
$departureDateGlobal = '';
$returnDateGlobal = '';
$maxDays = $stay-1;

foreach ($departureDates as $departureDate) {

    $returnDate = date('Y-m-d', strtotime($departureDate. ' + '.$maxDays.' days'));
    
    if(strtotime($minReturnDate) <= strtotime($returnDate))
    {
        $json_url = "http://www.despegar.com.ve/shop/flights/data/search/roundtrip/ccs/".$destinationCode."/".$departureDate."/".$returnDate."/1/0/0/TOTALFARE/ASCENDING/NA/NA/NA/NA/NA";
        $ch = curl_init($json_url);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        );

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $data = json_decode($result, true);

        $min = 99999;
        foreach ($data["result"]["data"]["items"] as $item) {
            $aux = intval($item["itinerariesBox"]["itinerariesBoxPriceInfoList"][0]["total"]["fare"]["raw"]);
            if ($aux<$min)
                $min=$aux;
        }

        echo "Pasaje minimo del ".date("D", strtotime($departureDate))." ".$departureDate." al ".date("D", strtotime($returnDate))." ".$returnDate.": ".$min." Bs.F\n";
        $linkUrl = "http://www.despegar.com.ve/shop/flights/results/roundtrip/CCS/".$destinationCode."/".$departureDate."/".$returnDate."/1/0/0";
        echo '   ---><a href="'.$linkUrl.'" target="_blank">Link</a>';
        echo "<br>";

        if ($min<$minGlobal){
            $minGlobal=$min;
            $departureDateGlobal=$departureDate;
            $returnDateGlobal=$returnDate;
        }
    }
}

echo "<br>";
echo "Pasaje minimo definitivo= Del ".date("l", strtotime($departureDateGlobal))." ".$departureDateGlobal." al ".date("l", strtotime($returnDateGlobal))." ".$returnDateGlobal.": ".$minGlobal." Bs.F";
$linkUrl = "http://www.despegar.com.ve/shop/flights/results/roundtrip/CCS/".$destinationCode."/".$departureDateGlobal."/".$returnDateGlobal."/1/0/0";
echo '   ---><a href="'.$linkUrl.'" target="_blank">Link</a>';
echo "<br>";
echo "<br>";
date_default_timezone_set('America/Caracas');
echo "Actualizado: ".date('H:i:s');

?>
