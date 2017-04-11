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
	//echo('<h1>' . $url . '</h1>');
	foreach($links as $element){
	$id = "";
			$ueberschrift = "";
			$bewertung = "";
		if($element->getAttribute('class') == '  reviewSelector '){
			//echo('<b>' . $element->getAttribute('id') . '</b><br>');
			$id = $element->getAttribute('id');
			$divs = $element->getElementsByTagName('div');

			foreach($divs as $div){
				if($div->getAttribute('class') == 'quote'){
					//echo('<b>Überschrift der Bewertung: </b>' . $div->nodeValue . '<br>');
					$ueberschrift = $div->nodeValue;
				}
				if($div->getAttribute('class') == 'entry'){
					//echo('<b>Bewertungstext: </b>' . $div->nodeValue . '<br>');
				}
				if($div->getAttribute('class') == 'ratingList'){
					$beschreibung =$div->childNodes[0]->childNodes[0]->childNodes[0];
					//echo($beschreibung->nodeValue);
				}
			}
			$spans = $element->getElementsByTagName('span');
			foreach($spans as $span){
				if($span->getAttribute('class') == 'ratingDate'){
					//echo('<b>Bewertungsdatum: </b>' . $span->nodeValue . '<br>');
				}
			}

			$imgs = $element->getElementsByTagName('img');
			foreach($imgs as $img){
				if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s00'){
					//echo('<b>Bewertung: </b> 0 von 5');
					$bewertung = "0";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s10'){
					//echo('<b>Bewertung: </b> 1 von 5');
					$bewertung = "1";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s20'){
					//echo('<b>Bewertung: </b> 2 von 5');
					$bewertung = "2";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s30'){
					//echo('<b>Bewertung: </b> 3 von 5');
					$bewertung = "3";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s40'){
					//echo('<b>Bewertung: </b> 4 von 5');
					$bewertung = "4";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s50'){
					//echo('<b>Bewertung: </b> 5 von 5');
					$bewertung = "5";
				}

				if($img->getAttribute('class') == 'reviewInlineImage'){
					//echo('<b>Bildlink: </b> ' . $img->getAttribute('src'));
				}
			}
			  array_push($data, array('r_id' => $id, 'title' => $ueberschrift));
		}
	}
	echo json_encode($data);
	//return $data;
}
function getNaechsteSeite($url){
	$dom = new DOMDocument('1.0');
	$dom->loadHTMLFile($url);
	$weiter = $dom->getElementsByTagName('a');
	foreach($weiter as $next){
		if($next->getAttribute('class') == 'nav next rndBtn ui_button primary taLnk'){
			$url='https://www.tripadvisor.de' . $next->getAttribute('href');
		}
	}
	return $url;
}
error_reporting(E_ERROR | E_PARSE);
function crawlBewertungen(){
	$url = 'https://www.tripadvisor.de/ShowUserReviews-g187309-d201817-r471437591-Carat_Hotel_Apartments_Munchen-Munich_Upper_Bavaria_Bavaria.html#REVIEWS';

	while($url != null){
		extractInfos($url);
		$url = getNaechsteSeite($url);
	}
}
//crawlBewertungen();
extractInfos($url);
}
?>