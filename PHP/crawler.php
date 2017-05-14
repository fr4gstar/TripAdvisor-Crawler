<html>
<?php
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
$debug = 1;
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

Status: 100%
************************************************************************************************/
function crawlHotel(){
	global $logdatei;
	
	$url = $_GET["url"];
	$type = $_GET["type"];
	$logdatei=fopen("logs/crawlerlog_" . time() . ".txt","a");
	$startzeit = time();
	fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Starttime: ". $startzeit . "\n");
	if(is_Null($url)){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " No URL in Request\n");
		echo("<br> Keine URL im Request");
	}else{
		$dom = new DOMDocument('1.0');
		if($dom->loadHTMLFile($url) == false){
			fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for URL: " . $url . "\n");
		}
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin with URL: " . $url . " and Type: " . $type . "\n");
		if($type == 'city'){
			$ankers = $dom->getElementsByTagName('a');
			foreach($ankers as $anker){
			if(strpos($anker->getAttribute('class'), 'property_title') !== false){
				$urlNew = 'https://www.tripadvisor.de' . $anker->getAttribute('href');			
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " HotelURL: ". $urlNew . "\n");
				$domNew = new DOMDocument('1.0');
				if($domNew->loadHTMLFile($urlNew) == false){
					fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for URL: " . $urlNew . "\n");
				}
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Hotel Information\n");
				$hotelID = extractHotelInformation($domNew, $urlNew);
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Hotel Information\n");
				$reviewUrl = getFirstReviewUrl($domNew);
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " First Review URL identified\n");
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Information\n");
				crawlReviews($reviewUrl, $hotelID);
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Information\n");
				$imageUrl = getImageUrl($urlNew);
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Images\n");
				crawlImages($imageUrl);
				fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Images\n");
				}
			}
		} elseif($type == 'hotel') {
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Hotel Information\n");
			$hotelID = extractHotelInformation($dom, $url);
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Hotel Information\n");
			$reviewUrl = getFirstReviewUrl($dom);
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " First Review URL identified\n");
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Information\n");
			crawlReviews($reviewUrl, $hotelID);
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Information\n");
			$imageUrl = getImageUrl($url);
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Begin Crawling Review Images\n");
			crawlImages($imageUrl);
			fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Finished Crawling Review Images\n");
		} else {
			fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Unknown Type in Request.\n");
			echo("<br> Falscher Type im Request");
		}
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
		if(strpos($div->getAttribute('class'), 'quote') !== false && $nextUrl == ""){
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
	global $logdatei;
	$limit = '9999999999';
	$imageUrl = '';
	$detail = substr($url, stripos($url, '-d')+2);
	$detail = substr($detail, 0, stripos($detail, '-'));
	$geo = substr($url, stripos($url, '-g')+2);
	$geo = substr($geo, 0, stripos($geo, '-'));
	$imageUrl = 'https://www.tripadvisor.de/MetaPlacementAjax?detail=' . $detail 
		. '&placementName=media_albums&servletClass=com.TripResearch.servlet.LocationPhotoAlbum&servletName=LocationPhotoAlbum&geo=' . $geo 
		. '&albumViewMode=images&cnt=' . $limit . '&offset=0';
	fputs($logdatei, "Info - ImageURL: " . $imageUrl);	
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
function crawlReviews($url, $hotelID){
	global $logdatei;
	$counter= 1;
	$reviewcounter = 1;
	while($url != "finished"){
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Next URL: " . $url . "\n");
		echo("<br>Nächste URL: " . $url . " Counter: " . $counter);
		$dom = new DOMDocument('1.0');
		if($dom->loadHTMLFile($url) == false){
			fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for URL: " . $url . "\n");
		}
		$url = getNextPageUrl($dom);
		set_time_limit(20);
		$reviewcounter = extractReviewInformation($dom, $reviewcounter, $hotelID);
		$counter++;
	}
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
	- hotelRating = Platz des Hotels in der Stadt
	- hotelReviews = Anzahl der Bewertungen, die das Hotel hat
	- hotelStreet = Straße und Hausnummer des Hotels
	- hotelPostalCode = PLZ des Hotels
	- hotelLocality = Stadt des Hotels
	- hotelRegion = Region in der das Hotel ist
	- hotelCountry = Land des Hotels
- Übergibt die Daten an die Funktion zum Schreiben in die DB

Übergabeparameter:
- dom = DOMDocument der Seite des Hotels

Status: 100%
************************************************************************************************/
function extractHotelInformation($dom, $url){
	$hotel = array();
	
	// Extrahiert den Hotelnamen
	$headings = $dom->getElementsByTagName('h1');
	
	// Extrahiert die Hotel ID aus der URL
	$detail = substr($url, stripos($url, '-d')+2);
	$detail = substr($detail, 0, stripos($detail, '-'));
	$hotel['id'] = $detail;
	
	// Extrahiert den Namen des Hotels
	foreach($headings as $head){
		if(strpos($head->getAttribute('id'), 'HEADING') !== false){
			$hotel['name'] = trim($head->nodeValue);
		}
	}
	// Extrahiert die Anzahl der Bewertungen
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if(strpos($anker->getAttribute('property'), 'reviewCount') !== false){
			$hotel['amountOfReviews'] = $anker->getAttribute('content');
		}
	}
	// Extrahiert das Hotel Ranking
	$bs = $dom->getElementsByTagName('b');
	foreach($bs as $b){
		if(strpos($b->getAttribute('class'), 'rank') !== false){
			$hotel['ranking'] = substr($b->nodeValue,3);
		}
	}
	// Extrahiert das Rating, und die Adresse
	$spans = $dom->getElementsByTagName('span');
	foreach($spans as $span){
		if(strpos($span->getAttribute('property'), 'ratingValue') !== false){
			$hotel['rating'] = str_replace(',','.',$span->getAttribute('content'));
		}
		if(strpos($span->getAttribute('property'), 'street-address') !== false){
			$hotel['street'] = $span->nodeValue;
		}
		if(strpos($span->getAttribute('property'), 'postal-code') !== false){
			$hotel['postalCode'] = $span->nodeValue;
		}
		if(strpos($span->getAttribute('property'), 'locality') !== false){
			$hotel['location'] = $span->nodeValue;
		}
		if(strpos($span->getAttribute('property'), 'region') !== false){
			$hotel['region'] = $span->nodeValue;
		}
		if(strpos($span->getAttribute('property'), 'country') !== false){
			$hotel['country'] = $span->nodeValue;
		}	
	}
	
	persistHotelInformation($hotel);
	
	return $hotel['id'];
}

/************************************************************************************************
Funktionsbeschreibung:
- Extrahiert die Informationen aller Reviews auf einer Seite
- Folgende Bestandteile werden gelesen:
	- User bezogene Attribute:
		- userID = Technischer Schlüssel des Users
		- userName = Username
		- userLocation = Herkunft des Users
		- userRating = Bewertung des Users
		- userGender = Geschlecht des Users
		- userAge = Alter des Users
	- Review bezogene Attribute:
		- reviewId = Technischer Schlüssel des Reviews
		- reviewTitel = Titel der Bewertung
		- reviewDate = Datum der Bewertung
		- reviewRating = "Sterne" der Bewertung
		- reviewText = Text der Bewertung
		- reviewFurtherInformation = Wer hatte den Aufenthalt wann
- Funktion crawlt den DOM Baum der Bewertungen. Anschließend extrahiert Sie die weiteren Informationen
  aus dem DOM Baum. Problem: Ältere Reviews werden über einen extra Aufruf von der Seite nachgeladen.
- Für den zweiten Fall werden alle Review IDs gesammelt, für die es keinen Titel und keinen Text gibt.
  Anschließend wird für diese der zweite Aufruf simuliert und aus diesem Aufruf die benötigten 
  Informationen extrahiert.

Übergabeparameter:
- dom = DOMDocument der Seite des Hotels
- reviewcounter = Zählt wieviele Reviews insgesamt gecrawlt wurden
- hotelid = HotelID des zu crawlenden Hotels

Status: 90%
************************************************************************************************/
function extractReviewInformation($dom, $reviewcounter, $hotelid){
	global $logdatei;

	$reviewIds = array();
	
	// Extrahiert die Review IDS aus dem DOM Baum der Tripadvisor Seite
	$divs = $dom->getElementsByTagName('div');
	foreach($divs as $div){
		if(strpos($div->getAttribute('class'), 'reviewSelector') !== false){
			$reviewId = substr($div->getAttribute('id'), -(strlen($div->getAttribute('id'))-7));	
			$reviewIds[] = $reviewId;
		}
	}
	
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
	
	// Lädt das DOMDocument aus dem Request String
	$requestDom = new DOMDocument('1.0');
	if($requestDom->loadHTMLFile($requestString) == false){
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for Request String : " . $requestString . "\n");
	}

	// Crawlt den Request DOM Baum
	$requestDivs = $requestDom->getElementsByTagName('div');
	
	foreach ($reviewIds as $key => $value){
		$reviewDiv = $requestDom->getElementById("review_" . $value);
		$user = array();
		$review = array();

		$reviewPictures = array();
		
		$review['id'] = $value;
		$revDivs = $reviewDiv->getElementsByTagName('div');
		foreach($revDivs as $revDiv){
			if(strpos($revDiv->getAttribute('class'), 'username') !== false){
				$user['name'] = utf8_decode($revDiv->nodeValue);
			}else if(strpos($revDiv->getAttribute('class'), 'inlineReviewUpdate') !== false){
				$user['id'] = utf8_decode(substr($revDiv->getAttribute('id'),2));
			}else if(strpos($revDiv->getAttribute('class'), 'levelBadge badge') !== false){
				$user['rating'] = utf8_decode(substr($revDiv->getAttribute('class'),strpos($revDiv->getAttribute('class'),'lvl_')+4,2));
			}else if(strpos($revDiv->getAttribute('class'), 'location') !== false){
				$user['location'] = utf8_decode($revDiv->nodeValue);
			}else if(strpos($revDiv->getAttribute('class'), 'quote') !== false){
				$review['title'] = substr(utf8_decode($revDiv->nodeValue),1,strlen(utf8_decode($revDiv->nodeValue))-2);
			}
		}

		$revParagraphs = $reviewDiv->getElementsByTagName('p');
		foreach($revParagraphs as $revParagraph){
			if(strpos($revParagraph->getAttribute('id'), 'review_' . $review['id']) !== false){
				$review['text'] = utf8_decode($revParagraph->nodeValue);
			}				
		}
		
		$revSpans = $reviewDiv->getElementsByTagName('span');
		foreach($revSpans as $revSpan){
			if(strpos($revSpan->getAttribute('class'), 'recommend-titleInline') !== false){
				$review['userStayInformation'] = utf8_decode($revSpan->nodeValue);
			}				
		}
		
		$review['userID'] = $user['id'];
		$review['hotelID'] = $hotelid;
		
		// Lädt die weiteren User Informationen
		// https://www.tripadvisor.de/MemberOverlay?src=236309855
		$userRequest = 'https://www.tripadvisor.de/MemberOverlay?src=' . $user['id'];
		$userDom = new DOMDocument('1.0');
		if($userDom->loadHTMLFile($userRequest) == false){
			fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error on Loading DOM for User Request String : " . $userRequest . "\n");
		}
		
		// Vollständiger Username
		$userHeadings = $userDom->getElementsByTagName('h3');
		foreach($userHeadings as $userHeading){
			if(strpos($userHeading->getAttribute('class'), 'username') !== false){
				$user['name'] = utf8_decode($userHeading->nodeValue);
			}			
		}
		
		$userListings = $userDom->getElementsByTagName('ul');
		foreach($userListings as $userListing){
			if(strpos($userListing->getAttribute('class'), 'memberdescription') !== false){
				$elements = $userListing->getElementsByTagName('li');
				if(!is_null($elements[1])){
					if(substr($elements[1]->nodeValue,0,3) !== 'Aus'){
						//TODO Korrekte 
						$user['gender'] = utf8_decode(substr($elements[1]->nodeValue,0, strpos($elements[1]->nodeValue, ' ')));
						$user['age'] = utf8_decode(substr($elements[1]->nodeValue, strpos($elements[1]->nodeValue, ' ')+1));
						$user['age'] = substr($user['age'],0, strpos($user['age'], ' '));
					}
				}
			}			
		}
		persistReviewInformation($user, $review);
	}
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
				$reviewImg['url'] = $img->getAttribute('src');
			}
		}
	}
	// Extrahiert die Review ID
	$ankers = $dom->getElementsByTagName('a');
	foreach($ankers as $anker){
		if(strpos($anker->getAttribute('href'), '/ShowUserReviews') !== false){
			$href = $anker->getAttribute('href');
			$userid = substr($href, stripos($href, '#UR')+3);
			$reviewImg['review'] = $userid;
		}
	}
	$reviewImg['id'] = $imageId;
	return $reviewImg;
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Hoteldaten in die Datenbank

Übergabeparameter:
- Array, in welchem die Daten gespeichert sind
- Persistiert ein Hotel

Status: 100%
************************************************************************************************/
function persistHotelInformation($hotel){
	global $logdatei;
	global $debug;
	if($debug == 1){
		echo("HotelID: " . $hotel['id'] . "<br>");
		echo("HotelName: " . $hotel['name'] . "<br>");
		echo("HotelStreet: " . $hotel['street'] . "<br>");
		echo("HotelPostalCode: " . $hotel['postalCode'] . "<br>");
		echo("HotelLocation: " . $hotel['location'] . "<br>");
		echo("HotelRegion: " . $hotel['region'] . "<br>");
		echo("HotelCountry: " . $hotel['country'] . "<br>");
		echo("HotelRating: " . $hotel['rating'] . "<br>");
		echo("HotelIAmountOfReviews: " . $hotel['amountOfReviews'] . "<br>");
		echo("HotelRank: " . $hotel['ranking'] . "<br>");
	}
	$conn = connectToDB();
	
	$query = "INSERT INTO TA_HOTEL (HotelID, HotelName, HotelStreet, HotelPostalCode, HotelLocation, HotelRegion, HotelCountry, HotelRating, AmountofReviews, HotelRank, HotelCreationDate, HotelUpdateDate)
	VALUES (" . $hotel["id"] . ", '" . $hotel["name"] . "', '".$hotel["street"]."', '".$hotel["postalCode"]."', '".$hotel["location"]."', '".$hotel["region"]."', '"
	.$hotel["country"]."', ".$hotel["rating"].", ".$hotel["amountOfReviews"].", ".$hotel["ranking"].", now(), now())
	ON DUPLICATE KEY 
		UPDATE HotelName='". $hotel["name"] ."',HotelStreet='".$hotel["street"]."', HotelPostalCode='".$hotel["postalCode"]."',HotelLocation='".$hotel["location"]."', HotelRegion='"
		.$hotel["region"]."', HotelCountry='".$hotel["country"]."', HotelRating=".$hotel["rating"].", AmountofReviews= ".$hotel["amountOfReviews"].", HotelRank=".$hotel["ranking"].
		", HotelCreationDate=HotelCreationDate, HotelUpdateDate=now();";
		
	if ($conn->query($query) === TRUE) {
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Hotel with ID " . $hotel['id'] . " safed into DB\n");
	} else {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Hotel could not be written into DB. Query: " . $query . "\n");
	}
	
	closeConnection($conn);
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Reviewdaten in die Datenbank

Übergabeparameter:
- Array, in welchem die Userdaten gespeichert sind ($user)
- Array, in welchem die Reviewdaten gespeichert sind ($review)
- Persistiert zuerst einen User und anschließend eine Review

Status: 100%
************************************************************************************************/
function persistReviewInformation($user, $review){
	global $logdatei;
	global $debug;
	if($debug == 1){
		echo("<br>");
		echo("UserID: " . $user['id'] . "<br>");
		echo("UserName: " . $user['name'] . "<br>");
		echo("UserRating: " . $user['rating'] . "<br>");
		echo("UserGender: " . $user['gender'] . "<br>");
		echo("UserCountry: " . $user['location'] . "<br>");
		echo("UserAge: " . $user['age'] . "<br>");
	}
	$conn = connectToDB();
	
	$user['name'] = str_replace("'", '', trim($user['name']));
	$user['rating'] = (!is_Null($user['rating'])) ? $user['rating'] : 'null';
	$user['gender'] = (!is_Null($user['gender'])) ? "'" . $user['gender'] . "'" : 'null';
	$user['location'] = (!is_Null($user['location'])) ?  trim($user['location']) : 'null';
	$user['location'] = (!empty($user['location'])) ? "'" . $user['location'] . "'" : 'null';
	
	$user['age'] = (!is_Null($user['age'])) ? "'" . $user['age'] . "'" : 'null';
	
	$query = "INSERT INTO TA_USER (UserID, UserName, UserRating, UserGender, UserCountry, UserAge, UserCreationDate, UserUpdateDate)
	VALUES (" . $user["id"] . ", '" . $user["name"] . "', " . $user['rating'].", ".$user["gender"].", ".$user["location"].", ".$user["age"]. ", now(), now())
	ON DUPLICATE KEY 
		UPDATE UserName='". $user["name"] ."',UserRating=". $user['rating'].", UserGender=".$user["gender"].",UserCountry=".$user["location"].", UserAge="
		.$user["age"].", UserCreationDate=UserCreationDate, UserUpdateDate=now();";
		
	if ($conn->query($query) === TRUE) {
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " User with ID " . $user['id'] . " safed into DB\n");
	} else {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " User could not be written into DB. Query: " . $query . "\n");
	}

	if($debug == 1){
		echo("<br>");
		echo("ReviewID: " . $review['id'] . "<br>");
		echo("ReviewTitle: " . $review['title'] . "<br>");
		echo("ReviewText: " . $review['text'] . "<br>");
		echo("UserStayInformation: " . $review['userStayInformation'] . "<br>");
		echo("HotelID: " . $review['hotelID'] . "<br>");	
		echo("UserID: " . $review['userID'] . "<br>");
	}
	
	$review['text'] = str_replace("'", '"', trim($review['text']));
	$review['title'] = str_replace("'", '', trim($review['title']));
	$review['userStayInformation'] = (!is_Null($review['userStayInformation'])) ? "'" . $review['userStayInformation'] . "'" : 'null';
	
	$query = "INSERT INTO TA_Review (ReviewID, ReviewTitel, ReviewText, UserStayDate, HotelID, UserID, ReviewCreationDate, ReviewUpdateDate)
	VALUES (" . $review["id"] . ", '" . $review["title"] . "', '".$review["text"]."', ".$review["userStayInformation"].", '".$review["hotelID"]."', '".$review["userID"]."', now(), now())
	ON DUPLICATE KEY 
		UPDATE ReviewTitel='". $review["title"] ."',ReviewText='".$review["text"]."', UserStayDate=".$review["userStayInformation"]."	,ReviewUpdateDate=now();";
		
	if ($conn->query($query) === TRUE) {
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Review with ID " . $review['id'] . " safed into DB\n");
	} else {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Review could not be written into DB. Query: " . $query . "\n");
	}	
	closeConnection($conn);
}

/************************************************************************************************
Funktionsbeschreibung:
- Schreibt die Bilder in die Datenbank

Status: 100%
************************************************************************************************/
function persistReviewImages($image){
	global $logdatei;
	global $debug;
	if($debug == 1){
		echo("<br>");
		echo("ReviewPictureID: " . $image['id'] . "<br>");
		echo("ReviewID: " . $image['review'] . "<br>");
		echo("ReviewPictureAdressID: " . $image['url'] . "<br>");
	}

	$conn = connectToDB();
	
	$query = "INSERT INTO TA_ReviewPicture (ReviewPictureID, ReviewID, ReviewPictureURL, ReviewPictureCreationDate, ReviewPictureUpdateDate)
	VALUES (" . $image["id"] . ", " . $image["review"] . ", '".$image["url"]."', now(), now())
	ON DUPLICATE KEY 
		UPDATE ReviewPictureURL='". $image["url"] ."' ,ReviewPictureUpdateDate=now();";
		
	if ($conn->query($query) === TRUE) {
		fputs($logdatei, "Info - " .date("Y-m-d H:i:s") . " Image with ID " . $image['id'] . " safed into DB\n");
	} else {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Image could not be written into DB. Query: " . $query . "\n");
	}
	closeConnection($conn);
}

/************************************************************************************************
Funktionsbeschreibung:
- Stellt die Verbindung zur Datenbank her
- Servername, Username, Password und Datenbankname müssen gegenfalls beim Umzug abgeändert werden

Status: 100%
************************************************************************************************/
function connectToDB(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "crawler";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error Setting Up DB Connection.\n");
		die("Connection failed: " . $conn->connect_error);
	}
	
	if (!$conn->set_charset("utf8")) {
		fputs($logdatei, "Error - " .date("Y-m-d H:i:s") . " Error Setting Charset for SQL Query.\n");
		exit();
	} 
	return $conn;
}

/************************************************************************************************
Funktionsbeschreibung:
- Schließt die Datenbankverbindung
- Muss immer aufgerufen werden

Status: 100%
************************************************************************************************/
function closeConnection($conn){
	$conn->close();
}

?>
</html>