<?php

// --------------------- CONFIG ----------------------------
// exchange the values here!
function getConfig() {
    return array(
        'token' => "my-token-here"
        , "baseurl" => 'http://localhost:8080'  
                        . $_SERVER[ 'PHP_SELF' ]
        , 'gitlabUrl' => "https://my-url-here/api/v4"
    );
}
// --------------------- /CONFIG ----------------------------


require_once __DIR__ . '/gitcurl.php';
require_once __DIR__ . '/gitdata.php';
require_once __DIR__ . '/csvparser.php';
require_once __DIR__ . '/createCSVBasedPhing.php';
require_once __DIR__ . '/integrityCheck.php';
