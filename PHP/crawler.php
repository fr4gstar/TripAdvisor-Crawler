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
/***********************************************************************************************/
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
/***********************************************************************************************/

$logdatei;
error_reporting(E_ERROR | E_PARSE);

crawlHotel();

//echo(getImageUrl('https://www.tripadvisor.de/Hotel_Review-g187309-d279562-Reviews-Hotel_Laimer_Hof-Munich_Upper_Bavaria_Bavaria.html'));



/************************************************************************************************
Funktionsbeschreibung:
- Steuert den allgemeinen Ablauf des Programms
- Liest die Daten aus dem Get Request aus:
	- url: URL des Hotels, welche gecrawlt werden soll
	- maxReviews: Anzahl an Reviews welche gecrawlt werden sollen
	- maxCrawlTime: Zeit die maximal gecrawlt werden soll
- Es wird lediglich maxReviews oder maxCrawlTime übergeben
- Erzeugt die Logdatei und stellt Sie zum Schrieben für die weiteren Funktionen bereit

Status: 100%
************************************************************************************************/
function crawlHotel(){
	global $logdatei;
	
	$url = $_GET["url"];
	$logdatei=fopen("logs/crawlerlog_" . time() . ".txt","a");
	$startzeit = time();
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Starttime: ". $startzeit . "\n");
	if(is_Null($url)){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " No URL in Request\n");
		echo("<br> Keine URL im Request");
	}else{
		$dom = loadDom($url);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Hotel Information\n");
		extractHotelInformation($dom);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Hotel Information\n");
		$reviewUrl = getFirstReviewUrl($dom);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " First Review URL identified\n");
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Information\n");
		crawlReviews($reviewUrl);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Information\n");
		$imageUrl = getImageUrl($url);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Images\n");
		crawlImages($imageUrl);
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Images\n");
	}
	$endzeit = time();
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Endtime: " . $endzeit . "\n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Seconds running: " . ($endzeit - $startzeit));
	fclose($logdatei);
}

/************************************************************************************************
Funktionsbeschreibung:
- Lädt die URL der ersten Review (Vergleibar mit Klick auf die erste Bewertung

Übergabeparameter:
- DOM Baum der Initialen Seite

Rückgabeparameter:
- Erste Review URL

Status: 100%
************************************************************************************************/
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

/************************************************************************************************
Funktionsbeschreibung:
- Ermittelt die URL zum Laden der Bilder aus der Hotel URL
- Extrahiert hierfür die Detail und Geo Variable aus der URL
- Beispielhaftes Ergebnis:
	https://www.tripadvisor.de/MetaPlacementAjax?detail=279562&placementName=media_albums&
	servletClass=com.TripResearch.servlet.LocationPhotoAlbum&servletName=LocationPhotoAlbum&
	geo=187309&albumViewMode=images&cnt=1000000&offset=0
- limit Variable gibt an wie viele Bilder maximal geholt werden sollen

Übergabeparameter:
- DOM Baum der Initialen Seite

Rückgabeparameter:
- URL für den Image Request

Status: 100%
************************************************************************************************/
function getImageUrl($url){
	$limit = '9999999999';
	$imageUrl = '';
	$detail = substr($url, stripos($url, '-d')+2);
	$detail = substr($detail, 0, stripos($detail, '-'));
	$geo = substr($url, stripos($url, '-g')+2);
	$geo = substr($geo, 0, stripos($geo, '-'));
	$imageUrl = 'https://www.tripadvisor.de/MetaPlacementAjax?detail=' . $detail 
		. '&placementName=media_albums&servletClass=com.TripResearch.servlet.LocationPhotoAlbum&servletName=LocationPhotoAlbum&geo=' . $geo 
		. '&albumViewMode=images&cnt=' . $limit . '&offset=0';
	
	return $imageUrl;
}

/************************************************************************************************
Funktionsbeschreibung:
- Steuert die Funktionalität des Crawlers
- Ruft die Funktion zum Extrahieren der Daten ab
- Lädt die nächste URL der nächsten Reviewseite
	- url: URL der ersten Reviewseite

Status: 100%
************************************************************************************************/
function crawlReviews($url){
	global $logdatei;
	$counter= 1;
	$reviewcounter = 1;
	while($url != "finished"){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " " . $counter . ". Next URL: " . $url . "\n");
		echo("<br>Nächste URL: " . $url . " Counter: " . $counter);
		$dom = new DOMDocument('1.0');
		if($dom->loadHTMLFile($url) == false){
			fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for URL: " . $url . "\n");
		}
		$url = getNextPageUrl($dom);
		set_time_limit(20);
		$reviewcounter = extractReviewInformation($dom, $reviewcounter);
		$counter++;
	}
	echo("<br>Fertig!<br>");
}

/************************************************************************************************
Funktionsbeschreibung:
- Lädt die nächste Bewertungsseiten URL aus dem DOM Baum

Status: 100%
************************************************************************************************/
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
	$hotel = array();
	
	// Extrahiert den Hotelnamen
	$headings = $dom->getElementsByTagName('h1');
	
	// TODO ID AUS WEBSITE ZIEHEN
	foreach($headings as $head){
		if($head->getAttribute('id') == 'HEADING'){
			$hotel['name'] = $head->nodeValue;
		}
	}
	// Extrahiert die Anzahl der Bewertungen
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if($anker->getAttribute('property') == 'reviewCount'){
			$hotel['amountOfReviews'] = $head->getAttribute('content');;
		}
	}
	// Extrahiert das Rating, und die Adresse
	$spans = $dom->getElementsByTagName('span');
	foreach($spans as $span){
		if($span->getAttribute('property') == 'ratingValue'){
			$hotel['rating'] = $span->getAttribute('content');
		}
		if($span->getAttribute('property') == 'streetAddress'){
			$hotel['street'] = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'postalCode'){
			$hotel['postalCode'] = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressLocality'){
			$hotel['locality'] = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressRegion'){
			$hotel['region'] = $span->nodeValue;
		}
		if($span->getAttribute('property') == 'addressCountry'){
			$hotel['country'] = $span->nodeValue;
		}
	}
	// Extrahiert die Anzahl an Bewertungen
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if($anker->getAttribute('property') == 'reviewCount'){
			$hotel['reviews'] = $anker->getAttribute('content');
		}
	}
	persistHotelInformation($hotel);
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
- Funktion crawlt den DOM Baum der Bewertungen. Anschließend extrahiert Sie die weiteren Informationen
  aus dem DOM Baum. Problem: Ältere Reviews werden über einen extra Aufruf von der Seite nachgeladen.
- Für den zweiten Fall werden alle Review IDs gesammelt, für die es keinen Titel und keinen Text gibt.
  Anschließend wird für diese der zweite Aufruf simuliert und aus diesem Aufruf die benötigten 
  Informationen extrahiert.

Übergabeparameter:
- dom = DOMDocument der Seite des Hotels
- reviewcounter = Zählt wieviele Reviews insgesamt gecrawlt wurden

Status: 90%
************************************************************************************************/
function extractReviewInformation($dom, $reviewcounter){
	global $logdatei;

	$reviewIds = array();
	
	// Crawlt den DOM Baum der Tripadvisor Seite
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'reviewSelector') !== false){
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
			
			$reviewId = substr($div->getAttribute('id'), -(strlen($div->getAttribute('id'))-7));	
			$reviewIds[] = $reviewId;
			echo("<br><h2>Review ID = " . $reviewId . "</h2>");
			$revDivs = $div->getElementsByTagName('div');
			foreach($revDivs as $revDiv){
				if(strpos($revDiv->getAttribute('class'), 'username') !== false){
					$userName = $revDiv->nodeValue;
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
			}
			
			$revParagraphs = $div->getElementsByTagName('p');
			foreach($revParagraphs as $revParagraph){
				if(strpos($revParagraph->getAttribute('id'), 'review_' . $reviewId) !== false){
					$reviewText = $revParagraph->nodeValue;
					echo("<br>Review Text: ". $reviewText);
				}				
			}
			
			if((!is_null($reviewText) || $reviewText != "") && (!is_null($reviewTitle) || $reviewTitle != "")){
				unset($reviewIds[array_search($reviewId, $reviewIds)]);
			}else{
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
	}
	
	echo("<br><b>Review IDs Array:</b>");
	var_dump($reviewIds);
	
	// Erzeugt die URL für den Request auf die Detailinformationen
	$requestString = "https://www.tripadvisor.de/UserReviewController?a=rblock&r=";
	$first = true;
	foreach ($reviewIds as $key => $value)
	{
		if($first){
			$requestString = $requestString . $value;
			$first = false;
		}else{
			$requestString = $requestString . ":" . $value;;
		}
	}
	$requestString = $requestString . "&type=5&tr=false&n=16&d=3338551";
	
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " " . count($reviewIds) . " Reviews found. \n");
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " RequestString for Information created: " . $requestString . "\n");
	
	// Lädt das DOMDocument aus dem Request String PRÜFEN BRAUCHT MAN DASS?????
	$requestDom = new DOMDocument('1.0');
	if($requestDom->loadHTMLFile($requestString) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for Request String : " . $requestString . "\n");
	}
	
	echo("<br><h1>" . $requestString . "</h1>");
	
	// Crawlt den Request DOM Baum
	$requestDivs = $requestDom->getElementsByTagName('div');
	
	foreach ($reviewIds as $key => $value){
		$reviewDiv = $requestDom->getElementById("review_" . $value);
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
		
		$reviewId = $value;	
		echo("<br><h2>Review ID = " . $reviewId . "</h2>");
		$revDivs = $reviewDiv->getElementsByTagName('div');
		foreach($revDivs as $revDiv){
			if(strpos($revDiv->getAttribute('class'), 'username') !== false){
				$userName = $revDiv->nodeValue;
				echo("<br>User Name: ". $userName);
			}else if(strpos($revDiv->getAttribute('class'), 'location') !== false){
				$userLocation = $revDiv->nodeValue;
				echo(" - Location: " . $userLocation);
			}else if(strpos($revDiv->getAttribute('class'), 'quote') !== false){
				$reviewTitle = $revDiv->nodeValue;
				echo(" - Titel: " . $reviewTitle);
			}
		}
		
		$revParagraphs = $reviewDiv->getElementsByTagName('p');
		foreach($revParagraphs as $revParagraph){
			if(strpos($revParagraph->getAttribute('id'), 'review_' . $reviewId) !== false){
				$reviewText = $revParagraph->nodeValue;
				echo("<br>Review Text: ". $reviewText);
			}				
		}
		
	}

	flush();
	ob_flush();
	return $reviewcounter;
}


/************************************************************************************************
Funktionsbeschreibung:
- Crawlt die Seite der Bild URL um die Bilder zu erkennen
- Ruft die Funktion zum Extrahieren der Bilder ab

- Übergabeparameter
	- url: URL für den Requerst der Bilder
Status: 100%
************************************************************************************************/
function crawlImages($url){
	$dom = new DOMDocument('1.0');
	if($dom->loadHTMLFile($url) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for URL: " . $url . "\n");
	}
	// Anker in dem sich die Bilder befinden
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		$data = $anker->getAttribute('data-href');
		$detail = substr($data, stripos($data, 'detail=')+7);
		$detail = substr($detail, 0, stripos($detail, '&'));
		$geo = substr($data, stripos($data, 'geo=')+4);
		$geo = substr($geo, 0, stripos($geo, '&'));
		$filter = substr($data, stripos($data, 'filter=')+7);
		$imageId = substr($data, stripos($data, 'ff=')+3);
		$imageId = substr($imageId, 0, stripos($imageId, '&'));
		
		$image = extractReviewPicture($detail, $geo, $filter, $imageId);
		persistReviewImages($image);
		// Ausgabe im Brwoser
		echo($image['image'] . " von User " . $image['user'] . "<br>");
		flush();
		ob_flush();
	}
}

/************************************************************************************************
Funktionsbeschreibung:
- Baut einen Request auf um die Bilder zu exportieren, da diese per JavaScript im Browser nach
  geladen werden
- Extrahiert die URL der jeweiligen Bilder und gibt diese zurück mit der jeweiligen UserID

Übergabeparameter:
- Parameter, mit denen man das Bild eindeutig identifzieren kann (vgl. Aufruf in der Funktion)

Rückgabewert:
- Array ( $reviewImg ) das den User und die Bild URL beinhaltet:
	- $reviewImg['user'] beinhaltet die UserID
	- $reviewImg['image'] beinhaltet die Bild URL

Status: 100%
************************************************************************************************/
function extractReviewPicture($detail, $geo, $filter, $imageId){
	global $logdatei;
	set_time_limit(20);
	//Ganzer String:	https://www.tripadvisor.de/LocationPhotoAlbum?detail=279562&geo=187309&filter=2&imageId=249186460&heroMinWidth=1141&heroMinHeight=719&ff=249186460&albumid=101&albumViewMode=hero&albumPartialsToUpdate=full&thumbnailMinWidth=50&cnt=30&offset=-5&baseMediaId=249186460&extraAlbumCoverCount=2&area=QC_Meta_Mini%7CPhoto_Lightbox&metaReferer=ShowUserReviews&metaRequestTiming=1492187292194
	//Notwendige Teile: https://www.tripadvisor.de/LocationPhotoAlbum?detail=279562&geo=187309&filter=2&imageId=249186460&ff=249186460&albumViewMode=hero
	$requestString = 'https://www.tripadvisor.de/LocationPhotoAlbum';
	$requestString = $requestString . '?detail=' . $detail;
	$requestString = $requestString . '&geo=' . $geo;
	$requestString = $requestString . '&filter=' . $filter;
	$requestString = $requestString . '&imageId=' . $imageId;
	$requestString = $requestString . '&ff=' . $imageId;
	$requestString = $requestString . '&albumViewMode=hero';

	$reviewImg = array();
	
	$dom = new DOMDocument('1.0');
	if($dom->loadHTMLFile($requestString) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for Image Request String : " . $requestString . "\n");	
	}
	// Extrahiert die Bild URL
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'mainImg') !== false){
			//$reviewImg = $div->childNodes[0]->getAttribute('src');	
			$imgs = $div->getElementsByTagName('img');
			foreach($imgs as $img){
				$reviewImg['image'] = $img->getAttribute('src');
			}
		}
	}
	// Extrahiert die User ID
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if(strpos($anker->getAttribute('href'), '/ShowUserReviews') !== false){
			$href = $anker->getAttribute('href');
			$userid = substr($href, stripos($href, '#UR')+3);
			$reviewImg['user'] = $userid;
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
function persistHotelInformation($hotel){
/*	echo("<br><h1>HotelInformationen</h1>");
	echo("<br>".$hotelName);
	echo("<br>".$hotelRating);
	echo("<br>".$hotelReviews);
	echo("<br>".$hotelStreet .", ". $hotelPostalCode .", ".  $hotelLocality .", ". $hotelRegion .", ". $hotelCountry);
	echo("<br><h1>Bewertungsinformationen</h1>");*/
	//TODO
	$query = 
	'INSERT INTO TA_HOTEL (HotelID, HotelName, HotelLocation, HotelRating, AmountOfReviews, HotelRank, HotelCreationDate, HotelUpdateDate)
	VALUES (' . $hotel['id'] . ', ' . $hotel['name'] . ', ' . hotel['location'] .', 2 , 3, 4, now(), now())
	ON DUPLICATE KEY 
		UPDATE HotelName='test',HotelLocation='Deutschland', HotelRating=6, AmountOfReviews=19, HotelRank=10, HotelCreationDate=HotelCreationDate, HotelUpdateDate=now()';
	/*INSERT INTO table (a,b,c) VALUES (1,2,3)
  ON DUPLICATE KEY UPDATE c=c+1;*/
/*	INSERT INTO TA_HOTEL (HotelID, HotelName, HotelLocation, HotelRating, AmountOfReviews, HotelRank, HotelCreationDate, HotelUpdateDate)
	VALUES (1, 'test', 'Test', 2 , 3, 4, now(), now()
	ON DUPLICATE KEY 
		UPDATE HotelName='test',HotelLocation='Deutschland', HotelRating=6, AmountOfReviews=19, HotelRank=1, HotelUpdateDate=now()*/
  /*HotelID numeric PRIMARY key,
HotelName varchar (255),
HotelLocation varchar (255),
HotelRating numeric,
AmountofReviews numeric,
HotelRank numeric,
HotelCreationDate timestamp,
HotelUpdateDate timestamp*/
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Daten in die Datenbank

Status: 0%
************************************************************************************************/
function persistReviewInformation(){
	
	//TODO
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Daten in die Datenbank

Status: 0%
************************************************************************************************/
function persistReviewImages($image){
	
	//TODO
}


function connectToDB(){
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
	return $conn;
/*
	$sql = "INSERT INTO bewertung (bewertungsid, ueberschrift, bewertung)
	VALUES ('". $id . "', '" . $ueberschrift . "', '" . $bewertung . "')";

	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();*/
}

function closeConnection($conn){
	$conn->close();
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