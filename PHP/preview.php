<?php
    /***********************************************************************************************/
    /************************************************************************************************
     Autor: Sergej Bardin, bardin@hm.edu
     Datum: 30.04.2017
     
     Programmbeschreibung:
     Lädt eine Vorschau zu einer Tripadvisor URL.
     
     ************************************************************************************************/
    /***********************************************************************************************/
header("Access-Control-Allow-Origin: *");
    $url = $_GET["url"];
    
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
        die('Not a valid URL');
    } else {
        /************************************************************************************************
         Funktionsbeschreibung:
         - Extrahiert die Daten aus der Tripadvisor URL
         ************************************************************************************************/
    function extractInfos($url){
        $type = $_GET["type"];
        $data = array();
        $dom = new DOMDocument('1.0');
        $dom->loadHTMLFile($url);
        $links = $dom->getElementsByTagName('div');
        $name = '';
        $amountOfReviews = '';
        $street = '';
        $locality = '';
        $country = '';
        $city = '';
        $amountOfHotels='';

        $title = '';
        if ($type == 'hotel'){
            
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
            
        } elseif($type == 'city') {
            foreach($links as $element){
                $as = $element->getElementsByTagName('a');
                $spans = $element->getElementsByTagName('span');
                $divs = $element->getElementsByTagName('h1');
                
                
                foreach($spans as $span){
                    if($span->getAttribute('class') == 'tab_count'){
                        $amountOfHotels = $span->nodeValue;
                    };
                    /*
                    if($span->getAttribute('class') == 'locality'){
                        $city = $span->nodeValue;
                    };
                 */
                };
            /*
                foreach($as as $a){
                    if($a->getAttribute('class') == 'more taLnk'){
                        $amountOfReviews = $a->nodeValue;
                    };
                };
                 */
                
                foreach($divs as $div){
                    if($div->getAttribute('id') == 'HEADING'){
                        $city = $div->nodeValue;
                    };
                };
            };
            
            array_push($data, array('city' => $city, '$amountOfHotels' => $amountOfHotels));
            
        }
            
            
        echo json_encode($data);
    }
    error_reporting(E_ERROR | E_PARSE);
    extractInfos($url);
}
?>
