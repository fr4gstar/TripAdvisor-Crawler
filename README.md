# TripAdvisor-Crawler

## Crawling attributes 
- HotelName
- HotelRating
- HotelLocation
- HotelRank
- AmountofReviews
- Username
- Userrating
- Usergender
- UserCountry
- UserAge
- UserStayDate
- UserReviewTitle
- UserReviewText
- UserPictureNumber (number of photos posted)
- UserPictureAddress
- GoodOrBad

## Conditions to match until 02/05/17
Ziel: Lauffähiger und benutzerfreundlicher Prototyp mit dem erfolgreichen Crawlen und Speichern eines/mehrerer Hotels in eine MySQL-Datenbank. 

DB – MySQL - Mathias:
-	Datenmodell
-	DB anlegen
-	Einheitlicher Verbindungsaufbau über PHP -> Connection.php
-	SQL-Skripte für die Erstellung der Datenbank und Tabellen + Testdaten

UI – AngularJS2– Sergej:
-	Kategorien + URL + Filter
-	DB-View
-	Tooltips
-	Usability -> Sinnvolle und übersichtliche Anordnung der Elemente
-	UTF-8
-	URL-Check
o	TripAdvisor in URL vorhanden
o	HTTPS only

Crawler- PHP - Andreas:
-	Ergebnisse als JSON
-	Check-Duplikate in DB
-	Mehrere PHP-Dateien (Single Hotel, All Hotel from City ...)
-	Parameterübergabe (URL bzw. Filter) an PHP-Script


## Architecture
- UI -> AngularJS2
- DB -> MySQL(XAMPP)
- Crawler -> ApacheWebServer(XAMPP) + PHP
