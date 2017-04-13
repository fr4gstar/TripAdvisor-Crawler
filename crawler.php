<html>
<?php

function extractInfos($url){
	$dom = new DOMDocument('1.0');
	$dom->loadHTMLFile($url);
	//echo("<code>" . str_replace('>','&gt;',str_replace('<', '&lt;', $dom->saveHTML())) . "</code>" );
	
	$links = $dom->getElementsByTagName('div');
	echo('<h1>' . $url . '</h1>');
	foreach($links as $element){
	$id = "";
			$ueberschrift = "";
			$bewertung = "";
		if($element->getAttribute('class') == '  reviewSelector '){
			echo('<b>' . $element->getAttribute('id') . '</b><br>');
			$id = $element->getAttribute('id');
			/*$children = $element->childNodes;
			$i = 1;
			foreach($children as $child){
				echo($child->ownerDocument->saveHTML($child));
				
			}
			echo('<br>');*/
			$divs = $element->getElementsByTagName('div');
			
			foreach($divs as $div){
				if($div->getAttribute('class') == 'quote'){
					echo('<b>Überschrift der Bewertung: </b>' . $div->nodeValue . '<br>');
					$ueberschrift = $div->nodeValue;
				}
				if($div->getAttribute('class') == 'entry'){
					echo('<b>Bewertungstext: </b>' . $div->nodeValue . '<br>');
				}
				if($div->getAttribute('class') == 'ratingList'){
					$beschreibung =$div->childNodes[0]->childNodes[0]->childNodes[0];
					echo($beschreibung->nodeValue);
				}
			}
			$spans = $element->getElementsByTagName('span');
			foreach($spans as $span){
				if($span->getAttribute('class') == 'ratingDate'){
					echo('<b>Bewertungsdatum: </b>' . $span->nodeValue . '<br>');
				}
			}
			
			$imgs = $element->getElementsByTagName('img');
			foreach($imgs as $img){
				if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s00'){
					echo('<b>Bewertung: </b> 0 von 5');
					$bewertung = "0";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s10'){
					echo('<b>Bewertung: </b> 1 von 5');
					$bewertung = "1";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s20'){
					echo('<b>Bewertung: </b> 2 von 5');
					$bewertung = "2";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s30'){
					echo('<b>Bewertung: </b> 3 von 5');
					$bewertung = "3";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s40'){
					echo('<b>Bewertung: </b> 4 von 5');
					$bewertung = "4";
				}
				else if($img->getAttribute('class') == 'sprite-rating_s_fill rating_s_fill s50'){
					echo('<b>Bewertung: </b> 5 von 5');
					$bewertung = "5";
				}
			}
			
			echo('<br><br><br>');
			
			//insertDatabase($id, $ueberschrift, $bewertung);
		}
	}
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

	$url = 'https://www.tripadvisor.de/Hotel_Review-g187309-d279562-Reviews-Hotel_Laimer_Hof-Munich_Upper_Bavaria_Bavaria.html';
function crawlBewertungen(){
	while($url != null){
		extractInfos($url);
		$url = getNaechsteSeite($url);
	}
}
//crawlBewertungen();

//insertDatabase("testid", "testueberschrift", "testbewertung");


function insertDatabase($id, $ueberschrift, $bewertung){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "crawler";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$sql = "INSERT INTO bewertung (bewertungsid, ueberschrift, bewertung)
	VALUES ('". $id . "', '" . $ueberschrift . "', '" . $bewertung . "')";

	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();
}
//extractInfos($url);

// Test URL: https://www.tripadvisor.de/Hotel_Review-g187309-d279562-Reviews-Hotel_Laimer_Hof-Munich_Upper_Bavaria_Bavaria.html
$logdatei;
crawlHotel();




function crawlHotel(){
	global $logdatei;
	
	$url = $_GET["url"];
	$maxReviews = $_GET["maxReviews"];
	$maxCrawlTime = $_GET["maxCrawlTime"];
	$logdatei=fopen("logs/crawlerlog_" . time() . ".txt","a");
	$startzeit = time();
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Starttime: ". $startzeit . "\n");
	if(is_Null($url)){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " No URL in Request\n");
		// TODO echo Rückgabe an Angular
	}else if(!is_Null($maxReviews)){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Crawl Request:\n");
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . "\t-URL: " . $url . "\n");
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . "\t-maxReviews: " . $maxReviews . "\n");
		// TODO echo Rückgabe an Angular
		crawlHotelMaxReviews($url, $maxReviews);
	}else if (!is_Null($maxReviews)){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Crawl Request:\n");
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " URL: " . $url . "\n");
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " maxCrawlTime: " . $maxCrawlTime . "\n");
		// TODO echo Rückgabe an Angular
		crawlHotelMaxCrawlTime($url, $maxCrawlTime);
	}else{
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " No MaxReviews and non MaxCrawlTime in Request\n");
		// TODO echo Rückgabe an Angular
	}
	$endzeit = time();
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Endtime: " . $endzeit . "\n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Seconds running: " . ($endzeit - $startzeit));
	fclose($logdatei);
}

function crawlHotelMaxReviews($url, $maxReviews){
	global $logdatei;
	$dom = new DOMDocument('1.0');
	$dom->loadHTMLFile($url);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Hotel Information\n");
	crawlHotelInformation($dom);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Hotel Information\n");
	$url = getFirstReviewUrl($dom);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " First Review URL identified\n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Information\n");
	crawlReviews($url);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Information\n");
}

function getFirstReviewUrl($dom){
	$nextUrl = "";
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if($div->getAttribute('class') == 'quote' && $nextUrl == ""){
			echo($div->nodeValue);
			$nextUrl='https://www.tripadvisor.de' . $div->childNodes->item(0)->getAttribute('href');
		}
	}
	return $nextUrl;
}

function crawlReviews($url){
	global $logdatei;
	$counter= 1;
	while($url != "finished"){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " " . $counter . ". Next URL: " . $url . "\n");
		echo("<br>Nächste URL: " . $url . " Counter: " . $counter);
		$dom = new DOMDocument('1.0');
		$dom->loadHTMLFile($url);
		$url = getNextPageUrl($dom);
		set_time_limit(20);
		extractReviewInformation($dom);
		$counter++;
	}
	
	echo("<br>Fertig!<br>");
}

function extractReviewInformation($dom){
	echo("<br>Extrakt Review");
	flush();
	ob_flush();
}
function getNextPageUrl($dom){
	$nextUrl="";
	//echo($dom->saveHtml());
	$weiter = $dom->getElementsByTagName('a');
	foreach($weiter as $next){
		if($next->getAttribute('class') == 'nav next rndBtn ui_button primary taLnk'){
			$nextUrl='https://www.tripadvisor.de' . $next->getAttribute('href');
		}
	}
	if($nextUrl == ""){
		$nextUrl = "finished";
	}
	return $nextUrl;
}

function crawlHotelMaxCrawlTime($url, $maxCrawlTime){
}

function crawlHotelInformation($dom){
	$hotelName;
	$hotelRating;
	$hotelReviews;
	$hotelStreet;
	$hotelPostalCode;
	$hotelLocality;
	$hotelRegion;
	$hotelCountry;
	
	// Extrahiert den Hotelnamen
	$headings = $dom->getElementsByTagName('h1');
	foreach($headings as $head){
		if($head->getAttribute('id') == 'HEADING'){
			$hotelName = $head->nodeValue;
		}
	}
	// Extrahiert das Rating, und die Adresse
	$spans = $dom->getElementsByTagName('span');
	foreach($spans as $span){
		if($span->getAttribute('property') == 'ratingValue'){
			$hotelRating = $span->getAttribute('content');
		}
		if($span->getAttribute('property') == 'streetAddress'){
			$hotelStreet = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'postalCode'){
			$hotelPostalCode = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressLocality'){
			$hotelLocality = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressRegion'){
			$hotelRegion = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressCountry'){
			$hotelCountry = $span->nodeValue;
		}
	}
	// Extrahiert die Anzahl an Bewertungen
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if($anker->getAttribute('property') == 'reviewCount'){
			$hotelReviews = $anker->getAttribute('content');
		}
	}
	persistHotelInformation($hotelName, $hotelRating, $hotelReviews, $hotelStreet, $hotelPostalCode, $hotelLocality, $hotelRegion, $hotelCountry);
}

function persistHotelInformation($hotelName, $hotelRating, $hotelReviews, $hotelStreet, $hotelPostalCode, $hotelLocality, $hotelRegion, $hotelCountry){
	echo($hotelName);
	echo($hotelRating);
	echo($hotelReviews);
	echo($hotelStreet . $hotelPostalCode . $hotelLocalit . $hotelRegion . $hotelCountry);
	
	//TODO
}


?>
</html>