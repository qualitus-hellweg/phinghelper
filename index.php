<?php
require_once __DIR__ . '/lib/_all.php';


$config = getConfig();
$BASE_URL = $config[ 'baseurl' ];


$iliasVersionToCheck = '';
$webmode = true;
if( isset( $argv[ 0 ] ) ) {
    $webmode = false;
}
if( isset( $argv[ 1 ] ) ) {
    $iliasVersionToCheck = $argv[ 1 ];
}

if( $webmode ) {
    if( isset( $_REQUEST[ 'iliasVersion' ] ) ) {
        $iliasVersionToCheck = $_REQUEST[ 'iliasVersion' ];
    }
    if( isset( $_REQUEST[ 'csv' ] ) ) {
        if( strlen( $_REQUEST[ 'csv' ] ) > 0 ) {
            file_put_contents( __DIR__ . '/plugins.csv', $_REQUEST[ 'csv' ] );
        }
    }
}

$plugins = parseCSV();
$errors = checkIntegrity( $plugins, $iliasVersionToCheck );
$phingXML = createPhing( $plugins );
file_put_contents( __DIR__ . '/build.xml', $phingXML );            



if( $webmode ) { 
    echo '<html><body>';

    // header
    include __DIR__ . '/header.php';

    echo '<h1>edit/check</h1>';
//    echo '<h1>' . $_SERVER[ 'PHP_SELF' ] . '</h1>';
    echo '<form action="' . $BASE_URL . '" METHOD="POST">';
    echo 'ilias version <input type="text" name="iliasVersion" value="' . $iliasVersionToCheck . '" /><br />';
    echo 'csv<br />';
    echo '<textarea name="csv" rows="5" cols="100">';
    echo file_get_contents( __DIR__ . '/plugins.csv' );
    echo '</textarea><br />';
    echo '<input type="submit" value="speichern"><br />';

    if( strlen( $iliasVersionToCheck == 0 ) ) {
        echo "<h1>NO VERSION CHECK</h1>";
    }
    echo '<h1>check</h1>';
    echo '<table>';
    echo '<tr><th>name</th><th>path</th><th>url</th><th>branch</th><th>devbranch</th><th>composer</th><th>error</th></tr>';

    foreach( $plugins as $name => $data ) {
        echo '<tr>';
        echo '<td><a href="'. $data[ 'url' ] .'">' . $name . '</a></td>';
        if( isset( $errors[ $name ][ 'path' ] ) ) {
            echo '<td style="background-color:#FF0000">';
        } else {
            echo '<td style="background-color:#00FF00">';
        }
        echo $data[ 'path' ];
        echo '</td>';
        if( isset( $errors[ $name ][ 'url' ] ) ) {
            echo '<td style="background-color:#FF0000">';
        } else {
            echo '<td style="background-color:#00FF00">';
        }
        echo $data[ 'url' ];
        echo '</td>';
        
        if( isset( $errors[ $name ][ 'branch' ] ) ) {
            echo '<td style="background-color:#FF0000">';
        } else {
            echo '<td style="background-color:#00FF00">';
        }
        echo $data[ 'branch' ];
        echo '</td>';
        
        if( isset( $errors[ $name ][ 'devbranch' ] ) ) {
            echo '<td style="background-color:#FF0000">';
        } else {
            echo '<td style="background-color:#00FF00">';
        }
        echo $data[ 'devbranch' ];
        echo '</td>';
 
        if( isset( $errors[ $name ][ 'composer' ] ) ) {
            echo '<td style="background-color:#FF0000">';
        } else {
            echo '<td style="background-color:#00FF00">';
        }
        echo $data[ 'composer' ];
        echo '</td>';
        
        
        echo '<td>';
        if( isset( $errors[ $name ] ) ) {
            echo '<pre>' . print_r( $errors[ $name ], 1 ) . '</pre>';
        }
        echo '</td>';        
        echo '</tr>';
    }
    echo '</table>';
    echo '<h1>build.xml</h1>';
    echo '<textarea cols="100", rows="20">' . file_get_contents( __DIR__ . '/build.xml' ) . '</textarea>';

} else {
    echo PHP_EOL . PHP_EOL . '------------------- plugins' . PHP_EOL;
    print_r( $plugins );
    echo PHP_EOL . PHP_EOL . '------------------- errors' . PHP_EOL;
    print_r( $errors );
    if( strlen( $iliasVersionToCheck == 0 ) ) {
        echo "NO VERSION CHECK" . PHP_EOL;
        echo "enable version checks with:" . PHP_EOL;
        echo '$> php index.php <VERSION-STRING>' . PHP_EOL;
        echo 'eg' . PHP_EOL;
        echo '$> php index.php 1.2.3' . PHP_EOL;
    }
}
