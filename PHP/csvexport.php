<?php
    /***********************************************************************************************/
    /************************************************************************************************
     Autor: Sergej Bardin, bardin@hm.edu
     Datum: 30.04.2017
     
     Programmbeschreibung:
     Extrahiert alle Daten aus der Crawler Datenbank als CSV.
     
     ************************************************************************************************/
    /***********************************************************************************************/
    
 	header("Access-Control-Allow-Origin: *");
    
    /************************************************************************************************
     Funktionsbeschreibung:
     - Konvertiert ein Array zu CSV
     ************************************************************************************************/
    function array2csv(array &$array){
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    };
    
    /************************************************************************************************
     Funktionsbeschreibung:
     - Sendet die CSV File an den Client
     ************************************************************************************************/
    function download_send_headers($filename) {
        // force download
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    };
     
    /************************************************************************************************
     Funktionsbeschreibung:
     - Stellt die Verbindung zur Datenbank her
     - Gibt die Daten als CSV
     
     ************************************************************************************************/
    function loadData() {
        $conn = connectToDB();
        $data = array();
        $sql_Hotel = "SELECT * FROM TA_HOTEL, TA_Review, TA_User where TA_Hotel.HotelID = TA_Review.HotelID and TA_Review.UserID = TA_User.UserID;";
        $result = $conn->query($sql_Hotel);
        
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                array_push($data,array('ReviewID' => $row["ReviewID"],
                                       'ReviewTitel' => utf8_decode($row["ReviewTitel"]),
                                       'ReviewText' => utf8_decode($row["ReviewText"]),
                                       //'ReviewPictureID' => $row["ReviewPictureID"],
                                       //'ReviewPictureURL' => utf8_decode($row["ReviewPictureURL"]),
                                       
                                       'UserID' => $row["UserID"],
                                       'UserName' => utf8_decode($row["UserName"]),
                                       'Userrating' => $row["Userrating"],
                                       'Usergender' => utf8_decode($row["Usergender"]),
                                       'UserCountry' => utf8_decode($row["UserCountry"]),
                                       'UserAGE' => $row["UserAGE"],
                                       'UserStayDate' => $row["UserStayDate"],
                                    
                                       'HotelID' => $row["HotelID"],
                                       'HotelName' => utf8_decode($row["HotelName"]),
                                       'HotelStreet' => utf8_decode($row["HotelStreet"]),
                                       'HotelPostalCode' => $row["HotelPostalCode"],
                                       'HotelLocation' => utf8_decode($row["HotelLocation"]),
                                       'HotelRegion' => utf8_decode($row["HotelRegion"]),
                                       'HotelCountry' => utf8_decode($row["HotelCountry"]),
                                       'HotelRating' => $row["HotelRating"],
                                       'AmountOfReviews' => $row["AmountofReviews"],
                                       'HotelRank' => $row["HotelRank"]
                                       ));
            }
            echo array2csv($data);
        } else {
            echo "0 results";
        }
        closeConnection($conn);
    };
    
    /************************************************************************************************
     Autor: Andreas Geyer
     Funktionsbeschreibung:
     - Stellt die Verbindung zur Datenbank her
     - Servername, Username, Password und Datenbankname müssen gegenfalls beim Umzug abgeändert werden
     
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
     Autor: Andreas Geyer
     Funktionsbeschreibung:
     - Schließt die Datenbankverbindung
     - Muss immer aufgerufen werden
     
     ************************************************************************************************/
    function closeConnection($conn){
        $conn->close();
    }

    download_send_headers("crawler" . date("H:i m-d-Y ") . ".csv");
    loadData();
    die();
?>
