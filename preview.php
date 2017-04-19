<?php
header("Access-Control-Allow-Origin: *");
    $url = $_GET["url"];
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
        die('Not a valid URL');
    } else {
    function extractInfos($url){
        $data = array();
        $dom = new DOMDocument('1.0');
        $dom->loadHTMLFile($url);
        $links = $dom->getElementsByTagName('div');
        $name = '';
        $amountOfReviews = '';
        $street = '';
        $locality = '';
        $country = '';

        $title = '';
        foreach($links as $element){
            $as = $element->getElementsByTagName('a');
            $spans = $element->getElementsByTagName('span');
            $divs = $element->getElementsByTagName('h1');

            foreach($spans as $span){
                if($span->getAttribute('class') == 'street-address'){
                    $street = $span->nodeValue;
                };
                if($span->getAttribute('class') == 'locality'){
                    $locality = $span->nodeValue;
                };
                if($span->getAttribute('class') == 'country-name'){
                    $country = $span->nodeValue;
                };
            };

            foreach($as as $a){
                if($a->getAttribute('class') == 'more taLnk'){
                    $amountOfReviews = $a->nodeValue;
                };
            };

            foreach($divs as $div){
                if($div->getAttribute('id') == 'HEADING'){
                    $name = $div->nodeValue;
                };
            };
        };

        array_push($data, array('name' => $name, 'street' => $street, 'locality' => $locality, 'country' => $country,'amountOfReviews' => $amountOfReviews));

        echo json_encode($data);
}
    error_reporting(E_ERROR | E_PARSE);
    extractInfos($url);
}
?>