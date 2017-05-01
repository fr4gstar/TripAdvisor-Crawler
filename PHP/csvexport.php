<?php
 	header("Access-Control-Allow-Origin: *");
	$data = array(
		array('id' => '1','first_name' => 'Cynthia'),
		array('id' => '2','first_name' => 'Keith'),
		array('id' => '3','first_name' => 'Robert'),
		array('id' => '4','first_name' => 'Theresa'),
		array('id' => '5','first_name' => 'Margaret')
	);
    
    function array2csv(array &$array)
    {
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
    
    function download_send_headers($filename) {
        // disable caching
        // header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        // header("Last-Modified: {$now} GMT");
        // $now = gmdate("D, d M Y H:i:s");
        
        
        // force download
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    };
    
 
	// echo json_encode($data);
    
    download_send_headers("tripadvisor_" . date("m-d-Y") . ".csv");
    echo array2csv($data);
    die();
?>
