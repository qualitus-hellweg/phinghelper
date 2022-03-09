<?php

function fetchDataFromGitlab( $url ) : array {
    $temp = getConfig();
    $token = $temp[ 'token' ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => "https://gitlab.qualitus.de/api/v4" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array(
              'PRIVATE-TOKEN: ' . $token
            , 'Content-Type: application/json'
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    ));
    $json = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $curl = null;
    if( $httpCode != 200 ) {
        // something went wrong
        // create an empty instead of the usual result
        $json = json_encode( array() );
    }
    $data = json_decode( $json );
//    print_r( $data  );
    return $data;
}

function fetchFileFromGitlab( $projectId, $branchName, $filename = 'plugin.php' ) {
    $temp = getConfig();
    $token = $temp[ 'token' ];
    
    $curl = curl_init();
    $url = '/projects/' . $projectId . '/repository/files/' . urlencode( $filename ) . '?ref=' . urlencode( $branchName );
//    echo PHP_EOL . $url . PHP_EOL;
    curl_setopt_array($curl, array(
        CURLOPT_URL            => "https://gitlab.qualitus.de/api/v4" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array(
              'PRIVATE-TOKEN: ' . $token
            , 'Content-Type: application/json'
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    ));
    $json = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $curl = null;
    $data = json_decode( $json );
//    print_r( $data );
//    echo PHP_EOL . "" . $data->content;
    
    
    
    $out = "";
//    echo PHP_EOL ."HTTP-CODE: " . $httpCode . PHP_EOL; 
    if( $httpCode == 200 ) {
//        echo PHP_EOL ."decode";
        $out = base64_decode( "" . $data->content );
    }
    
//    print_r( $data  );
    return $out;
}


function checkComposerFromGitlab( $projectId, $branchName ) {
    $temp = getConfig();
    $token = $temp[ 'token' ];
    
    $curl = curl_init();
    $url = '/projects/' . $projectId . '/repository/files/' . urlencode( 'composer.json' ) . '?ref=' . urlencode( $branchName );
//    echo PHP_EOL . $url . PHP_EOL;
    curl_setopt_array($curl, array(
        CURLOPT_URL            => "https://gitlab.qualitus.de/api/v4" . $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array(
              'PRIVATE-TOKEN: ' . $token
            , 'Content-Type: application/json'
        ),
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    ));
    $json = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $curl = null;
    if( $httpCode == 200 ) {
        return "1";
    }
    return "";
}