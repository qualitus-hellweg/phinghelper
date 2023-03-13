<?php
if( ! isset( $argv[ 0 ] ) ) {
    echo '<html><head><title>oooops</title></head>'
    . '<body><h1>this takes ~10-30min, so it will run into a timeout webwise</h1>'
    . 'please use<br />$> php spider.php<br />to run.'
    . '</body></html>';
    die();
}
require_once __DIR__ . '/lib/_all.php';
echo "this script creates a new index of the gitlab." .PHP_EOL;
echo "this takes ~10-30min" . PHP_EOL . PHP_EOL;


$allProjects = array();
$page = 0;
$count = 1;
while( $count > 0 ) {
    ++$page;
    $projects = fetchDataFromGitlab( '/projects?per_page=50&page=' . $page );

    // print_r( $projects );
    
    foreach( $projects as $item ) {
        $gitid = "" . $item->id;
        $name = "" . $item->name;
        $gitPath = "" . $item->path_with_namespace;
        $filePath = "";
        $repoUrl  = "" . $item->http_url_to_repo;
        
        $tempProject = array();
        $tempProject[ 'id' ] = "" . $item->id;
        $tempProject[ 'name' ] = "" . $item->name;
        $tempProject[ 'gitpath' ] = "" . $item->path_with_namespace;
//        $tempProject[ 'filepath' ] = "";
        $tempProject[ 'repourl' ]  = "" . $item->http_url_to_repo;
        
        
//        echo $id . ':' . $name . ':' . PHP_EOL;
        $branchUrl = '/projects/' . $gitid . '/repository/branches';
        $branches = fetchDataFromGitlab( $branchUrl );
        $allBranches = array();
        foreach( $branches as $branch ) {
            $branchName = "" . $branch->name;
            echo 'Branch for:' . $gitid . ':' . $name . ':=>:' . $branchName . ':' . PHP_EOL;
            
            $ilias_min_version = "";
            $ilias_max_version = "";            
            $fileContent = fetchFileFromGitlab( $gitid, $branchName );            
            if( strlen( $fileContent ) > 0 ) {
                if( isInString( 'require', $fileContent ) ) {
                    $fileContent = str_replace( 'require_once', 'echo', $fileContent );
                    $fileContent = str_replace( 'require', 'echo', $fileContent );
                }
                if( isInString( 'include', $fileContent ) ) {
                    $fileContent = str_replace( 'include_once', 'echo', $fileContent );
                    $fileContent = str_replace( 'include', 'echo', $fileContent );
                }
                
                
                file_put_contents( __DIR__ . '/plugin.php', $fileContent );
                include __DIR__ . '/plugin.php';
                unlink( __DIR__ . '/plugin.php' );

            }
            
            $min = $ilias_min_version;
            $max = $ilias_max_version;
            
            $temp = array();
            $temp[ 'id' ] = "" . $gitid;
            $temp[ 'name' ] = "" . $branchName;
            $temp[ 'ilias_min' ] = $min;
            $temp[ 'ilias_max' ] = $max;
            
            // add composer to branch
            $temp[ 'composer' ] = "";
            if( fileExists( $gitid, $branchName, 'composer.json' ) ) {
                $temp[ 'composer' ] = "1";
            }
            $temp[ 'composer_vendor' ] = "";
            if( fileExists( $gitid, $branchName, 'vendor/autoload.php' ) ) {
                $temp[ 'composer_vendor' ] = "1";
            }
            
            
            $allBranches[ $branchName ] = $temp;
            
        }                
        $tempProject[ 'filepath' ] = getFilesystemPathForRepository( $gitid, $branchName, $tempProject[ 'name' ] );
        
        
        // */
        $tempProject[ 'branches' ] = $allBranches;
        $allProjects[ $repoUrl ] = $tempProject;
    }
    $count = count( $projects );
}
$out = json_encode( $allProjects );
file_put_contents( __DIR__ . '/lib/gitdata.json', $out );