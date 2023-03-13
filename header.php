<?php

echo '<h1>Phinghelper</h1>';

// determine current branch
$currentBranch = shell_exec( 'git rev-parse --abbrev-ref HEAD' );
echo '<h2>Current Branch :' . $currentBranch . ':</h2>';
echo '<hr />';
echo '<a href="' . dirname( $BASE_URL ) . '/plugins.csv">plugins.csv/<a><br />';
echo '<a href="' . dirname( $BASE_URL ) . '/build.xml" target="_BLANK">build.xml/<a><br />';
echo '<a href="' . dirname( $BASE_URL ) . '/index.php';
if( isset( $_REQUEST[ 'iliasVersion' ] ) ) {
    echo '?iliasVersion=' . $_REQUEST[ 'iliasVersion' ];
}
echo '">csv-checker/<a><br />';
echo '<a href="' . dirname( $BASE_URL ) . '/picker.php';
if( isset( $_REQUEST[ 'iliasVersion' ] ) ) {
    echo '?iliasVersion=' . $_REQUEST[ 'iliasVersion' ];
}
echo '">picker/<a><br />';
echo '<hr />';

/*
echo '<h1>$REQUEST</h1>';
echo '<pre>', print_r( $_REQUEST, 1 ), '</pre>';
echo '<hr />';
// */