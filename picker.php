<?php
require_once __DIR__ . '/lib/_all.php';

$config = getConfig();
$BASE_URL = $config[ 'baseurl' ];

// controller
$iliasVersionToCheck = "";
if( isset( $_REQUEST[ 'iliasVersion' ] ) ) {
    $iliasVersionToCheck = $_REQUEST[ 'iliasVersion' ];
}
$search = "";
if( isset( $_REQUEST[ 'search' ] ) ) {
    $search = $_REQUEST[ 'search' ];
}

if( isset( $_REQUEST[ 'add' ] ) ) {
    $plugins = parseCSV();
//    echo '<h4>add-before</h4><pre>', print_r( $plugins, 1 ), '</pre>';
    $url = $_REQUEST[ 'addURL' ];
    $git = GitData::getInstance();
    $project = $git->getProject( $url );
    if( $project != null ) {
        $temp = array();
        $temp[ 'name' ] = $project->getName();
        $temp[ 'path'] = $project->getFilepath();
        $temp[ 'url'] = $project->getRepourl();
        $temp[ 'branch'] = "";        
        $temp[ 'devbranch' ] = "";        
        $temp[ 'composer' ] = "";
        
        $branches = $project->getBranches();
        $firstBranch = null;        
        foreach( $branches as $name => $branch ) {
            if( $firstBranch != null ) {
                continue;
            }
            $firstBranch = $branch;            
        }
        $temp[ 'composer' ] = $firstBranch->getComposer();
        if( $firstBranch->isComposerVendor() ) {
            $temp[ 'composer' ] = '';
        }
        
        
        $plugins[ $project->getName() ] = $temp;
        serializePlugins( $plugins );
//        echo '<h4>after</h4><pre>', print_r( $plugins, 1 ), '</pre>';
    }
}
if( isset( $_REQUEST[ 'del' ] ) ) {
    $plugins = parseCSV();
//    echo '<h4>del-before</h4><pre>', print_r( $plugins, 1 ), '</pre>';
    $url = $_REQUEST[ 'delURL' ];
    
    $out = array();
    foreach( $plugins as $plugin ) {
        if( $plugin[ 'url' ] != $url ) {
            $out[ $plugin[ 'name' ] ] = $plugin;
        }
    }
    
    serializePlugins( $out );
//    echo '<h4>after</h4><pre>', print_r( $out, 1 ), '</pre>';
}

if( isset( $_REQUEST[ 'pluginform' ] ) ) {
    // create $plugins from $_REQUEST
    $out = array();
    
    $index = 1;
    $name = $_REQUEST[ 'name' ];
    $path = $_REQUEST[ 'path' ];
    $url = $_REQUEST[ 'url' ];
    $branch = $_REQUEST[ 'branch' ];
    $devbranch = $_REQUEST[ 'devbranch' ];
    $composer = $_REQUEST[ 'composer' ];
    
//    echo '<h1>name:' . $name[ 1 ] . ':</h1>';
    
    while( isset( $name[ $index ] ) ) {
        $temp = array();
        $temp[ 'name' ] = $name[ $index ];
        $temp[ 'path'] = $path[ $index ];
        $temp[ 'url'] = $url[ $index ];
        $tempBranch = "";
        if( isset( $branch[ $index ] ) ) {
            $tempBranch = $branch[ $index ];
        }
        $temp[ 'branch'] = $tempBranch;
        
        $tempBranch = "";
        if( isset( $devbranch[ $index ] ) ) {
            $tempBranch = $devbranch[ $index ];
        }
        $temp[ 'devbranch'] = $tempBranch;
        
        $temp[ 'composer' ] = $composer[ $index ];
        
        $out[ $temp[ 'name' ] ] = $temp;
        ++$index;
    }
//    echo 'OUT<pre>', print_r( $out, 1 ), '</pre>';
    serializePlugins( $out );
}


// header
include __DIR__ . '/header.php';
$plugins = parseCSV();
$errors = checkIntegrity( $plugins, $iliasVersionToCheck );
//echo '<pre>', print_r( $errors, 1 ), '</pre>';
$phingXML = createPhing( $plugins );
file_put_contents( __DIR__ . '/build.xml', $phingXML );            

/*
echo '<h2>DEL</h2>';
if( count( $plugins ) > 0 ) {
    
    echo '<table>';
    echo '<tr><th>&nbsp;</th><th>Name</th><th>Typ</th><th>git</th></tr>';
    foreach( $plugins as $plugin ) {
        /** @var  $result GitProjectDTO  /
        echo '<tr valign="top">';
        echo '<td>';

        echo '<form action="' . $BASE_URL . '" METHOD="POST">';
        echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '">';
//        echo '<input type="hidden" name="search" value="' . $search . '">';
        echo '<input type="hidden" name="delURL" value="' . $plugin[ 'url' ] . '">';
        echo '<input type="submit" name="del" value="del" />';
//        echo '[add]';
        echo '</form>';
        echo '</td>';
        echo '<td>' . $plugin[ 'name' ] . '</td>';
        echo '<td>' . basename( $plugin[ 'path' ] ) . '</td>';
        echo '<td>';
        echo '<a href="' . $plugin[ 'url' ] . '" target="_blank">gitlab</a><br/>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
//    echo '<pre>' . print_r( $searchResults, 1 ) . '</pre>';
} 
echo '<hr />';
// */

echo '<h2>ADD</h2>';
echo '<form action="' . $BASE_URL . '" METHOD="POST">';
echo 'search: <input type="text" name="search" value="';
if( isset( $_REQUEST[ 'search' ] ) ) {
    echo $_REQUEST[ 'search' ];
}
echo '" />';
echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '"><input type="submit" name="lookup" value="lookup">';
echo '</form>';
if( strlen( $search ) > 0 ) {
    echo '<form action="' . $BASE_URL . '" METHOD="POST">';
    echo '<input type="hidden" name="search" value="" />';
    echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '"><input type="submit" name="clear" value="clear">';
    echo '</form>';
}
    
if( strlen( $search ) > 0 ) {
    $git = GitData::getInstance();
    $searchResults = $git->search( $search );
    echo '<h3>Results</h3>';
    echo '<table>';
    echo '<tr><th>&nbsp;</th><th>Name</th><th>Typ</th><th>git</th></tr>';
    foreach( $searchResults as $result ) {
        /** @var  $result GitProjectDTO*/
        echo '<tr valign="top">';
        echo '<td>';

        echo '<form action="' . $BASE_URL . '" METHOD="POST">';
        echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '">';
        echo '<input type="hidden" name="search" value="' . $_REQUEST[ 'search' ] . '">';
        echo '<input type="hidden" name="addURL" value="' . $result->getRepourl() . '">';
        echo '<input type="submit" name="add" value="add" />';
//        echo '[add]';
        echo '</form>';
        echo '</td>';
        echo '<td>' . $result->getName() . '</td>';
        echo '<td>' . basename( $result->getFilepath() ) . '</td>';
        echo '<td>';
        echo '<a href="' . $result->getRepourl() . '" target="_blank">' . $result->getGitpath() . '</a><br/>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
//    echo '<pre>' . print_r( $searchResults, 1 ) . '</pre>';
} 
echo '<hr />';


echo '<h2>Branches</h2>';
echo '<form action="' . $BASE_URL . '" METHOD="POST">';
echo 'ilias version <input type="text" name="iliasVersion" value="' . $iliasVersionToCheck . '">';
echo '<input type="hidden" name="search" value="' . $search . '">';
echo '<input type="submit" />';
echo '</form>';


echo '<form action="' . $BASE_URL . '" METHOD="POST">';
echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '">';
echo '<input type="hidden" name="search" value="' . $search . '">';
echo '<input type="hidden" name="pluginform" value="pluginform">';

echo '<table border="0">';
//echo '<tr><th>Plugin</th><th>PROD</th><th>DEV</th><th>errors</th></tr>';
$index = 0;
$git = GitData::getInstance();

foreach( $plugins as $plugin ) {
    ++$index;
    echo '<tr align="center"><td colspan="3"><h1>' . $plugin[ 'name' ] 
            . '(<a href="' . $plugin[ 'url' ] . '" target="_blank">git</a>)' 
            . '</h1></td>'
            . '<td><a href="' . $BASE_URL . '?del=1&iliasVersion=' . $iliasVersionToCheck . '&delURL=' . urlencode( $plugin[ 'url' ] ) . '"><h1>DEL</h1></a></td>'
            . '</tr>';
/*
        echo '<form action="' . $BASE_URL . '" METHOD="POST">';
        echo '<input type="hidden" name="iliasVersion" value="' . $iliasVersionToCheck . '">';
        echo '<input type="hidden" name="delURL" value="' . $plugin[ 'url' ] . '">';
        echo '<input type="submit" name="del" value="del" />';
        echo '</form>';
// */

    echo '<tr align="left"><th>Plugin</th><th>PROD</th><th>DEV</th><th>errors</th></tr>';
    echo '<tr valign="top">';
    // basic
//      echo '<pre>' . print_r( $plugin, 1 ) . '</pre>';
    echo '<td>';
    echo '<table>';
    echo '<tr><td>Name</td>';
    echo '<td><input type="text" name="name[' . $index . ']" value="' . $plugin[ 'name' ] . '" /></td></tr>';    
    echo '<tr><td>Path</td>';
//    echo '<td><input type="hidden" name="name[' . $index . ']" value="' . $plugin[ 'name' ] . '" /><input type="hidden" name="path[' . $index . ']" value="' . $plugin[ 'path' ] . '" />' . $plugin[ 'path' ] . '</td></tr>';
    echo '<td><input type="text" name="path[' . $index . ']" value="' . $plugin[ 'path' ] . '" /></td></tr>';
    echo '<tr><td>URL</td>';
//    echo '<td><input type="hidden" name="url[' . $index . ']" value="' . $plugin[ 'url' ] . '" />' . $plugin[ 'url' ] . '</td></tr>';
    echo '<td><input type="text" name="url[' . $index . ']" value="' . $plugin[ 'url' ] . '" /></td></tr>';
    
    echo '<tr valign="top"><td>Composer</td>';
    echo '<td>';
    echo '<input type="radio" name="composer[' . $index . ']" value="1"';
    if( strlen( $plugin[ 'composer' ] ) > 0 ) {
        echo ' checked="checked"';
    }
    echo ' />';
    echo 'use composer<br />';
    echo '<input type="radio" name="composer[' . $index . ']" value=""';
    if( strlen( $plugin[ 'composer' ] ) == 0 ) {
        echo ' checked="checked"';
    }
    echo ' />';
    echo 'dont use composer<br />';
    
    echo '<td></tr>';
    echo '<tr><td colspan=2"><a href="' . $plugin[ 'url' ] . '" target="_blank">git</a></td></tr>';
    echo '</table>';
    echo '</td>';
    
    $gitProject = $git->getProject( $plugin[ 'url'] );
    if( $gitProject == null ) {    
        // PROD
        echo '<td>';
        echo '<input type="text" name="branch[' . $index . ']" value="' . $plugin[ 'branch' ] . '" />';
        echo '</td>';

        // DEV
        echo '<td>';
        echo '<input type="text" name="devbranch[' . $index . ']" value="' . $plugin[ 'devbranch' ] . '" />';
        echo '</td>';
    } else {
        // PROD
        echo '<td>';
        echo '<table>';
        $allBranches = $gitProject->getBranches();
        foreach( $allBranches as $branch ) {
            /** @var GitBranchDTO $branch */
            echo '<tr>';
            echo '<td>';
            echo '<input type="radio" name="branch[' . $index . ']" value="' . $branch->getName() . '"';
            if( $plugin[ 'branch' ] == $branch->getName() ) {
                echo ' checked="checked"';
            }
            echo ' />';
            echo $branch->getName();
            echo '</td>';
//            echo '<td>';
            if( strlen( $iliasVersionToCheck ) == 0 ) {
                echo '<td>';
            } else {
                if( strlen( $branch->getIliasMin() ) == 0 ) {
                    echo '<td style="background-color:#FFFF00">no min/max</td>';
                } else {
                    if( 
                        ( $branch->getIliasMin() <= $iliasVersionToCheck ) 
                        && ( $iliasVersionToCheck <= $branch->getIliasMax() ) 
                    ) {
                        echo '<td style="background-color:#00FF00">';
                    } else {
                        echo '<td style="background-color:#FF0000">';
                    }
        
                    echo $branch->getIliasMin() . ' - ' . $branch->getIliasMax();
                    echo '</td>';
                }
            }
            echo '<tr>';
        }
        echo '</table>';        
        echo '</td>';

        // DEV
        echo '<td>';
        echo '<table>';
        $allBranches = $gitProject->getBranches();
        foreach( $allBranches as $branch ) {
            /** @var GitBranchDTO $branch */
            echo '<tr>';
            echo '<td>';
            echo '<input type="radio" name="devbranch[' . $index . ']" value="' . $branch->getName() . '"';
            if( $plugin[ 'devbranch' ] == $branch->getName() ) {
                echo ' checked="checked"';
            }
            echo ' />';
            echo $branch->getName();
            echo '</td>';
//            echo '<td>';
            if( strlen( $iliasVersionToCheck ) == 0 ) {
                echo '<td>';
            } else {
                if( strlen( $branch->getIliasMin() ) == 0 ) {
                    echo '<td style="background-color:#FFFF00">no min/max</td>';
                } else {
                    if( 
                        ( $branch->getIliasMin() <= $iliasVersionToCheck ) 
                        && ( $iliasVersionToCheck <= $branch->getIliasMax() ) 
                    ) {
                        echo '<td style="background-color:#00FF00">';
                    } else {
                        echo '<td style="background-color:#FF0000">';
                    }
                    echo $branch->getIliasMin() . ' - ' . $branch->getIliasMax();
                    echo '</td>';
                }            
            }
            echo '<tr>';
        }
        echo '</table>';        
        echo '</td>';
    }
    
    
    echo '<td>';
    if( isset( $errors[ $plugin[ 'name' ] ] ) ) {
        $allErrors = $errors[ $plugin[ 'name' ] ];
        foreach( $allErrors as $source => $comment ) {
            echo $source . ' : ' . $comment . '<br />';
        }
//        echo '<pre>', print_r( $errors[ $plugin[ 'name' ] ], 1 ), '</pre>';
    }
    echo '</td>';
    echo '</tr>';
    echo '<tr><td colspan="7">&nbsp;</td></tr>';
}

echo '</table>';
echo '<input type="submit" />';
echo '</form>';