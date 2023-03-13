<?php
function parseCSV() {
    $csvFilename  = 'plugins.csv';
// Name;Path;URL;prod-Branch;dev-Branch;composer;
    $modules = array();

    $config  = file( $csvFilename );
    $firstLine = true;
    
    
    $name = '';
    // fetch path and branch from .gitmodules
    foreach( $config as $line ) {
        if( $firstLine ) {
            $firstLine = false;
            continue;
        }
        $explodedLine = explode( ';', $line );
        if( count( $explodedLine ) > 5 ) {
            $temp = array();
            $temp[ 'name' ] = $explodedLine[ 0 ];
            $temp[ 'path'] = $explodedLine[ 1 ];
            $temp[ 'url'] = $explodedLine[ 2 ];
            $temp[ 'branch'] = $explodedLine[ 3 ];        
            $temp[ 'devbranch' ] = $explodedLine[ 4 ];        
            $temp[ 'composer' ] = $explodedLine[ 5 ];
            $modules[ $temp[ 'name' ] ] = $temp;
        }
    }
    return $modules;
}
//echo '<pre>', print_r( $gitmodules, 1 ), '</pre>';

function serializePlugins( $plugins ) {
    $out = 'Name;Path;URL;prod-Branch;dev-Branch;composer' . PHP_EOL;
    foreach( $plugins as $plugin ) {
        $out .= $plugin[ 'name' ] 
            . ';' . $plugin[ 'path'] 
            . ';' . $plugin[ 'url']
            . ';' . $plugin[ 'branch'] 
            . ';' . $plugin[ 'devbranch' ] 
            . ';' . $plugin[ 'composer' ]
            . ';' . PHP_EOL;
    }
    file_put_contents( __DIR__ . '/../plugins.csv', $out );
}