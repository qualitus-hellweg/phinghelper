<?php

/**
 * 
 * @param array $modules
 * @return string
 */
function createPhing( $modules ) {

    $outClean   = '';
    $outClone   = '';
    $outPull    = '';
    $outDev     = '';
    $outProd    = '';
    $outFb      = '';
    $outComposer= '';
    
    
    $out = '';
    $temp = array();
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;
        
        $out .= PHP_EOL . '        <delete dir="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '" quiet="true" />';
    }
    $outClean = $out;
    
    $out = '';
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;
        
        $out .= PHP_EOL . '        <gitclone
            repository="' . $module[ 'url' ] . '"
            targetPath="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '"
        />';
    }
    $outClone = $out;
    
    $out = '';
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;
        
        $out .= PHP_EOL . '        <echo msg="' . $module[ 'name' ] . '" />';
        $out .= PHP_EOL . '        <gitpull repository="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '" />';
    }
    $outPull = $out;

    $out = '';
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;
        if( 
            ( $module[ 'composer' ] == '1' ) 
            || ( strtolower( $module[ 'composer' ] ) == 'j' ) 
            || ( strtolower( $module[ 'composer' ] ) == 'y' ) 
            || ( strtolower( $module[ 'composer' ] ) == 'ja' ) 
            || ( strtolower( $module[ 'composer' ] ) == 'yes' ) 
        ) {
                $out .= PHP_EOL . '        <exec 
            command="composer install --no-interaction" passthru="true"
            dir="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '"
        />';
        }
    }
    $outComposer = $out;
    if( strlen( $outComposer ) == 0 ) {
        $outComposer = PHP_EOL . '        <echo msg="skipping, no composer parts" />';
    }
    
    $out = '';
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;

        if( isset( $module[ 'branch' ] ) ) {
            $out .= PHP_EOL . '        <gitcheckout
            repository="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '"
            branchname="' . $module[ 'branch' ] . '"  
        />';
        }
            
    }
    $outProd = $out;
    
    // shortcut, always use outProd
    $out = '';
    foreach( $modules as $module ) {
//        echo '<h1><pre>', print_r( $module, 1 ), '</pre></h1>';
        if( ! isset( $module[ 'name' ] ) ) 
            continue;
        
        $devbranch = $module[ 'devbranch' ]; 
        if( strlen( $module[ 'devbranch' ] ) == 0 ) {
            $devbranch = $module[ 'branch' ]; 
        }
        if( isset( $module[ 'branch' ] ) ) {
            $out .= PHP_EOL . '        <gitcheckout
            repository="' . $module[ 'path' ] . '/' . $module[ 'name' ] . '"
            branchname="' . $devbranch . '"  
        />'; 
        }
            
    }
    $outDev = $out;
    $outFb  = $outDev;
    
    
    // complete output
    $out = '<?xml version="1.0" encoding="UTF-8"?>
<!-- this xml was generated and not manually modified. pls delete this line, if you change something -->
<project name="MyPhingTest" default="list">
    <target name="list">
        <echo msg="Lists available targets" />
        <echo msg="general:" />
        <echo msg="clean        cleans up old installations" />
        <echo msg="clone        clones the repositories" />
        <echo msg="pull         pulls the repositories" />
        <echo msg="composer     calls all composers" />
        <echo msg="" />
        <echo msg="branches:" />
        <echo msg="dev          switches to dev-branch" />
        <echo msg="prod         switches to prod-branch" />
        <echo msg="fb           switches to feature-branch" />
        <echo msg="" />
        <echo msg="makros:" />
        <echo msg="install      synonym for \'install-prod\'" />
        <echo msg="install-prod synonym for \'clean clone prod composer\'" />
        <echo msg="install-dev  synonym for \'clean clone dev  composer\'" />
        <echo msg="install-fb   synonym for \'clean clone fb   composer\'" />
        <echo msg="update       synonym for \'pull composer\'" />		
    </target>

    <!-- FB first. please delete this line if you actually changed the branch here -->
    <target name="fb">' . $outFb . '
    </target>
    
    <!-- ====================================== clean ====================================== -->
    <target name="clean">' . $outClean . '        
    </target>

    <!-- ====================================== clone ====================================== -->
    <target name="clone">' . $outClone . '
    </target>
    
    <!-- ====================================== pull ====================================== -->
    <target name="pull">' . $outPull . '        
    </target>

    <!-- ====================================== composer ====================================== -->
    <target name="composer">' . $outComposer . '        
    </target>

    <!-- ====================================== prod ====================================== -->
    <target name="prod">' . $outProd . '        
    </target>

    <!-- ====================================== dev ====================================== -->
    <target name="dev">' . $outDev . '        
    </target>



    <!-- ====================================== makros ====================================== -->
    <target name="install">
        <phingcall target="install-prod" />
    </target>
    <target name="install-prod">
        <phingcall target="clean" />
        <phingcall target="clone" />
        <phingcall target="prod" />
        <phingcall target="composer" />    
    </target>
    <target name="install-dev">
        <phingcall target="clean" />
        <phingcall target="clone" />
        <phingcall target="dev" />
        <phingcall target="composer" />
    </target>
    <target name="install-fb">
        <phingcall target="clean" />
        <phingcall target="clone" />
        <phingcall target="fb" />
        <phingcall target="composer" />
    </target>
    
    <target name="update">
        <phingcall target="pull" />
        <phingcall target="composer" />
    </target>
</project>        
';    
    
    return $out;
}