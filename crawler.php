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

/*----------------------------------------------------- Neuer Teil -----------------------------------------------*/
// Test URL: https://www.tripadvisor.de/Hotel_Review-g187309-d279562-Reviews-Hotel_Laimer_Hof-Munich_Upper_Bavaria_Bavaria.html

/************************************************************************************************
Autor: Andreas Geyer, andreas.geyer@geyer-net.de / ageyer@hm.edu
Datum: 14.04.2017

Programmbeschreibung:
Extrahiert Daten von einer Hotel URL aus Tripadvisor
Erzeugt ein Log File zum Debugen, in welchem der Fortschritt des Prozesses protokolliert wird.

Gobale Variablen:
- logdatei = Beinhaltet die Logdatei

Sonstiges:
- Nur Errors werden gemeldet
- Datei ruft die Funktion crawlHotel immer auf
************************************************************************************************/

$logdatei;
error_reporting(E_ERROR | E_PARSE);



crawlHotel();



/************************************************************************************************
Funktionsbeschreibung:
- Steuert den allgemeinen Ablauf des Programms
- Liest die Daten aus dem Get Request aus:
	- url: URL des Hotels, welche gecrawlt werden soll
	- maxReviews: Anzahl an Reviews welche gecrawlt werden sollen
	- maxCrawlTime: Zeit die maximal gecrawlt werden soll
- Es wird lediglich maxReviews oder maxCrawlTime übergeben
- Erzeugt die Logdatei und stellt Sie zum Schrieben für die weiteren Funktionen bereit
************************************************************************************************/
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
	$dom = loadDom($url);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Hotel Information\n");
	extractHotelInformation($dom);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Hotel Information\n");
	$url = getFirstReviewUrl($dom);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " First Review URL identified\n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Information\n");
	crawlReviews($url);
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Information\n");
}


//Wird noch benötigt weil sonst die Schleife nicht richtig durchläuft -> zu untersuchen
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
	$reviewcounter = 1;
	while($url != "finished"){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " " . $counter . ". Next URL: " . $url . "\n");
		echo("<br>Nächste URL: " . $url . " Counter: " . $counter);
		//$dom = loadDom($url);
		$dom = new DOMDocument('1.0');
		$dom->loadHTMLFile($url);
		$url = getNextPageUrl($dom);
		set_time_limit(20);
		$reviewcounter = extractReviewInformation($dom, $reviewcounter);
		$counter++;
	}
	
	echo("<br>Fertig!<br>");
}


function getNextPageUrl($dom){
	$nextUrl="";
	//echo($dom->saveHtml());
	//echo("<code>" . str_replace('>','&gt;',str_replace('<', '&lt;', $dom->saveHTML())) . "</code>" );
	$weiter = $dom->getElementsByTagName('a');
	foreach($weiter as $next){
		//echo("<br>" . $next->getAttribute("class"));
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
 //TODO
}


/************************************************************************************************
Funktionsbeschreibung:
- Extrahiert die Informationen des Hotels
- Folgende Bestandteile werden gelesen:
	- hotelName = Name des Hotels
	- hotelRating = TODO
	- hotelReviews = Anzahl der Bewertungen, die das Hotel hat
	- hotelStreet = Straße und Hausnummer des Hotels
	- hotelPostalCode = PLZ des Hotels
	- hotelLocality = Stadt des Hotels
	- hotelRegion = Region in der das Hotel ist
	- hotelCountry = Land des Hotels
- Übergibt die Daten an die Funktion zum Schreiben in die DB

Übergabeparameter:
- dom = DOMDocument der Seite des Hotels

Status: 90%
************************************************************************************************/
function extractHotelInformation($dom){
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

/************************************************************************************************
Funktionsbeschreibung:
- Extrahiert die Informationen aller Reviews auf einer Seite
- Folgende Bestandteile werden gelesen:
	- User bezogene Attribute:
		- userName = Username ohne ID zur Identifikation
		- userLocation = TODO
		- userRating = TODO
		- userGender = TODO
		- userAge = TODO
	- Review bezogene Attribute:
		- reviewId = Technischer Schlüssel des Reviews
		- reviewTitel = Titel der Bewertung
		- reviewDate = Datum der Bewertung
		- reviewRating = "Sterne" der Bewertung
		- reviewText = Text der Bewertung
		- reviewFurtherInformation = Wer hatte den Aufenthalt wann -> TODO Format
		- Bilder TODO
- Die Funktion holt sich über eine Schleife alle Review ID's der Seite. Anschließend wird für
  diese IDS ein weiterer Request an Tripadvisor gesendet. Dies muss gemacht werden, da teilweise
  die Informationen am Browser per JavaScript Aufruf nachgeladen werden und nicht mit dem ersten
  Request ankommen.
- Aus dem zweiten Request werden die weiteren Informationen extrahiert.

Übergabeparameter:
- dom = DOMDocument der Seite des Hotels
- reviewcounter = Zählt wieviele Reviews insgesamt gecrawlt wurden

Status: 90%
************************************************************************************************/
function extractReviewInformation($dom, $reviewcounter){
	global $logdatei;
	
	$review = array();
	$userName;
	$userLocation;
	$userRating;
	$userGender;
	$userAge;
	$reviewId;
	$reviewTitle;
	$reviewDate;
	$reviewRating;
	$reviewText;
	$reviewFurtherInformation;
	$reviewPictures = array();
	
	
	
	// Sucht alle Review IDs
	$reviewIds = array();
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'reviewSelector') !== false){
			$reviewIds[] = substr($div ->getAttribute('id'), -(strlen($div ->getAttribute('id'))-7));	
		}
	}
	
	// Erzeugt die URL für den Request auf die Detailinformationen
	$requestString = "https://www.tripadvisor.de/UserReviewController?a=rblock&r=";
	/*foreach ($reviewIds as $key => $value)
	{
		if($key == 0){
			$requestString = $requestString . $value;
		}else{
			$requestString = $requestString . ":" . $value;;
		}
	}
	$requestString = $requestString . "&type=5&tr=false&n=16&d=3338551";
	
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " " . count($reviewIds) . " Reviews found. \n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " RequestString for Information created: " . $requestString . "\n");
	
	// Lädt das DOMDocument aus dem Request String PRÜFEN BRAUCHT MAN DASS?????
	$informationDom = new DOMDocument('1.0');
	if($informationDom->loadHTMLFile($requestString) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for Request String : " . $requestString . "\n");
	}
	*/
	
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'reviewSelector') !== false){
			$reviewId = substr($div ->getAttribute('id'), -(strlen($div ->getAttribute('id'))-7));	
			$revDivs = $div->getElementsByTagName('div');
			foreach($revDivs as $revDiv){
				if(strpos($revDiv->getAttribute('class'), 'username') !== false){
					$userName = $revDiv->childNodes[0]->nodeValue;
					echo("<br>User Name: ". $userName);
				}else if(strpos($revDiv->getAttribute('class'), 'location') !== false){
					$userLocation = $revDiv->nodeValue;
					echo(" - Location: " . $userLocation);
				}else if(strpos($revDiv->getAttribute('class'), 'quote') !== false){
					$reviewTitle = $revDiv->nodeValue;
					echo(" - Titel: " . $reviewTitle);
				}
			}
			
			$revImgs = $div->getElementsByTagName('img');
			foreach($revImgs as $revImg){
				if(strpos($revImg->getAttribute('class'), 'rating_s_fill') !== false){
					$reviewRating = substr($revImg->getAttribute('alt'), 0,1);
					echo("<br>Rating: ". $reviewRating);
				}
				
			}
			
			$revSpans = $div->getElementsByTagName('span');
			foreach($revSpans as $revSpan){
				if(strpos($revSpan->getAttribute('class'), 'ratingDate') !== false){
					$reviewDate = $revSpan->nodeValue;
					echo("<br>Review Date: ". $reviewDate);
				} else if(strpos($revSpan->getAttribute('class'), 'recommend-titleInline noRatings') !== false){
					$reviewFurtherInformation = $revSpan->nodeValue;
					echo("<br>Weitere Infos: ". $reviewFurtherInformation);
				} else if(strpos($revSpan->getAttribute('class'), 'LocationPhotoDirectLink') !== false){
					echo("<br>Bei dem Review wurde ein Foto gefunden");
					// Extrahiert die Informationen aus dem Onclick Call
					$onclick= $revSpan->getAttribute('onclick');
					$detail = substr($onclick, strpos($onclick, 'detail'));
					$detail = substr($detail, 0, strpos($detail, ','));
					$detail = substr($detail, strpos($detail, ':')+1);
					$geo = substr($onclick, strpos($onclick, 'geo'));
					$geo = substr($geo, 0, strpos($geo, ','));
					$geo = substr($geo, strpos($geo, ':')+1);
					$filter = substr($onclick, strpos($onclick, 'filter'));
					$filter = substr($filter, 0, strpos($filter, ','));
					$filter = substr($filter, strpos($filter, ':')+1);
					$imageId = substr($onclick, strpos($onclick, 'imageId'));
					$imageId = substr($imageId, 0, strpos($imageId, '}'));
					$imageId = substr($imageId, strpos($imageId, ':')+1);
					$imageUrl = extractReviewPicture($detail, $geo, $filter, $imageId);
					$reviewPictures[] = $imageUrl;
					echo("<br>Bild Url: " . $imageUrl);
				}
			}
			
			$revParagraphs = $div->getElementsByTagName('p');
			foreach($revParagraphs as $revParagraph){
				if(strpos($revParagraph->getAttribute('id'), 'review_' . $reviewId) !== false){
					$reviewText = $revParagraph->nodeValue;
					echo("<br>Review Text: ". $reviewText);
				}				
			}
			
			$review['userName'] = $userName;
			$review['userLocation'] = $userLocation;
			$review['reviewTitle'] = $reviewTitle;
			$review['reviewDate'] = $reviewDate;
			$review['reviewRating'] = $reviewRating;
			$review['reviewText'] = $reviewText;
			$review['reviewFurtherInformation'] = $reviewFurtherInformation;
			$review['reviewPictures'] = $reviewPictures;
			
			persistReviewInformation($review);
			
		}
	}
	
	//echo("<h1>");
	//var_dump($reviews);
	//echo("</h1>");
	
	
	
	
	
	
	
	
	//echo("<br>" . $requestString);
	// echo("<br>Reviewcounter: " . $reviewcounter . " - ID: " . $reviewId);
	// Vorlage: https://www.tripadvisor.de/UserReviewController?a=rblock&r=184959210&type=5&tr=false&n=16&d=3338551

	//echo("<code>" . str_replace('>','&gt;',str_replace('<', '&lt;', $informationDom->saveHTML())) . "</code>" );
	//echo("https://www.tripadvisor.de/UserReviewController?a=rblock&r=" . $revString . "&type=5&tr=false&n=16&d=3338551");
	flush();
	ob_flush();
	return $reviewcounter;
}

/************************************************************************************************
Funktionsbeschreibung:
- Baut einen Request auf um die Bilder zu exportieren, da diese per JavaScript im Browser nach
  geladen werden
- Extrahiert die URL der jeweiligen Bilder und gibt diese zurück

Übergabeparameter:
- Parameter, mit denen man das Bild eindeutig identifzieren kann (vgl. Aufruf in der Funktion)

Status: 100%
************************************************************************************************/
function extractReviewPicture($detail, $geo, $filter, $imageId){
	global $logdatei;
	//Ganzer String:	https://www.tripadvisor.de/LocationPhotoAlbum?detail=279562&geo=187309&filter=2&imageId=249186460&heroMinWidth=1141&heroMinHeight=719&ff=249186460&albumid=101&albumViewMode=hero&albumPartialsToUpdate=full&thumbnailMinWidth=50&cnt=30&offset=-5&baseMediaId=249186460&extraAlbumCoverCount=2&area=QC_Meta_Mini%7CPhoto_Lightbox&metaReferer=ShowUserReviews&metaRequestTiming=1492187292194
	//Notwendige Teile: https://www.tripadvisor.de/LocationPhotoAlbum?detail=279562&geo=187309&filter=2&imageId=249186460&ff=249186460&albumViewMode=hero
	$requestString = 'https://www.tripadvisor.de/LocationPhotoAlbum';
	$requestString = $requestString . '?detail=' . $detail;
	$requestString = $requestString . '&geo=' . $geo;
	$requestString = $requestString . '&filter=' . $filter;
	$requestString = $requestString . '&imageId=' . $imageId;
	$requestString = $requestString . '&ff=' . $imageId;
	$requestString = $requestString . '&albumViewMode=hero';
	
	$reviewImg;
	
	$dom = new DOMDocument('1.0');
	if($dom->loadHTMLFile($requestString) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for Image Request String : " . $requestString . "\n");	
	}
	
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'mainImg') !== false){
			//$reviewImg = $div->childNodes[0]->getAttribute('src');	
			$imgs = $div->getElementsByTagName('img');
			foreach($imgs as $img){
				$reviewImg = $img->getAttribute('src');
			}
		}
	}
	return $reviewImg;
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Daten in die Datenbank

Übergabeparameter:
- Array, in welchem die Daten gespeichert sind
- Persistiert eine Review

Status: 0%
************************************************************************************************/
function persistHotelInformation($hotelName, $hotelRating, $hotelReviews, $hotelStreet, $hotelPostalCode, $hotelLocality, $hotelRegion, $hotelCountry){
	echo($hotelName);
	echo($hotelRating);
	echo($hotelReviews);
	echo($hotelStreet . $hotelPostalCode . $hotelLocalit . $hotelRegion . $hotelCountry);
	
	//TODO
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Daten in die Datenbank

Status: 0%
************************************************************************************************/
function persistReviewInformation($hotelName, $hotelRating, $hotelReviews, $hotelStreet, $hotelPostalCode, $hotelLocality, $hotelRegion, $hotelCountry){
	echo($hotelName);
	echo($hotelRating);
	echo($hotelReviews);
	echo($hotelStreet . $hotelPostalCode . $hotelLocalit . $hotelRegion . $hotelCountry);
	
	//TODO
}


function loadDom($url){
	$data = array('filterLang' => 'ALL');
	$options = array(
		'https' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { /* Handle error */ }

	$dom = new DOMDocument('1.0');
	$dom->loadHTML($result);
	return $dom;
}


?>
</html>