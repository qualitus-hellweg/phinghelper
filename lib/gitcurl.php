<?php

function fetchDataFromGitlab( $url ) : array {
    $temp = getConfig();
    $token = $temp[ 'token' ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL            => $temp[ 'gitlabUrl' ] . $url,
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
        CURLOPT_URL            => $temp[ 'gitlabUrl' ] . $url,
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

function fileExists( $projectId, $branchName, $filename ) {
    $fileContents = fetchFileFromGitlab( $projectId, $branchName, $filename );
    if( strlen( $fileContents ) > 0 ) {
        return true;
    }
    return false;
}

function getFilesystemPathForRepository( $projectId, $branchName, $pluginName ) {
    
    // check for skins
    $skinContent = fetchFileFromGitlab( $projectId, $branchName, "template.xml" );
    if( strlen( $skinContent ) > 0 ) {
        return "Customizing/global/skin";
    }
    
    // check for php project
    $indexPhpContent = fetchFileFromGitlab( $projectId, $branchName, "index.php" );
    if( strlen( $indexPhpContent ) > 0 ) {
        return "??? DONT at this moment ???";
    }
            
    
    // check for plugin
    $filename = "classes/class.il" . $pluginName . "Plugin.php";
    $pluginContents = fetchFileFromGitlab( $projectId, $branchName, $filename );
    if( strlen( $pluginContents ) > 0 ) {
        // this must be a plugin
        if( 
            ( isInString( "extends ilRepositoryObjectPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilRepositoryObjectPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/Repository/RepositoryObject";
        }        
        if( 
            ( isInString( "extends ilUserInterfaceHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilUserInterfaceHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook";
        }        
        if( 
            ( isInString( "extends ilCloudHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilCloudHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/Cloud/CloudHook";
        }  
        if( 
            ( isInString( "extends ilPageComponentPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilPageComponentPlugin", $pluginContents ) ) 
        ) {        
            return "Customizing/global/plugins/Services/COPage/PageComponent";
        }        
        if( 
            ( isInString( "extends ilQuestionsPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilQuestionsPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/TestQuestionPool/Questions";
        }  
        if( 
            ( isInString( "extends ilTestExportPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilTestExportPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/Test/Export";
        }  
        if( 
            ( isInString( "extends ilTestSignaturePlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilTestSignaturePlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/Test/Signature";
        }          
        if( 
            ( isInString( "extends ilCronHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilCronHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/Cron/CronHook";
        }        
        if( 
            ( isInString( "extends ilDclFieldTypePlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilDclFieldTypePlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/DataCollection/FieldTypeHook";
        }        
        if( 
            ( isInString( "extends ilOrgUnitExtensionPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilOrgUnitExtensionPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Modules/OrgUnit/OrgUnitExtension";
        }        
        if( 
            ( isInString( "extends ilPDFRendererPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilPDFRendererPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/PDFGeneration/Renderer";
        }        
        if( 
            ( isInString( "extends ilEventHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilEventHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/EventHandling/EventHook";
        }          
        if( 
            ( isInString( "extends ilUserInterfaceHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilUserInterfaceHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook";
        }
        if( 
            ( isInString( "extends ilSoapHookPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilSoapHookPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/WebServices/SoapHook";
        }
        if( 
            ( isInString( "extends ilUDFClaimingPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilUDFClaimingPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/User/UDFClaiming";
        }
        if( 
            ( isInString( "extends ilAdvancedMDClaimingPlugin", $pluginContents ) ) 
            || ( isInString( "extends \ilAdvancedMDClaimingPlugin", $pluginContents ) ) 
        ) {
            return "Customizing/global/plugins/Services/AdvancedMetaData/AdvancedMDClaiming";
        }
        
    }
    
    
    return "";
}

function isInString( $needle, $haystack ) {
    if( strpos( $haystack, $needle ) != false ) {
        return true;
    }
    return false;
}