<?php
/**
 * 
 * @param array $plugins
 * @return array errormessages
 */
function checkIntegrity( $plugins, $iliasVersionToCheck='' ) {
    $git = GitData::getInstance();

    $checkIliasVersions = false;
    if( strlen( $iliasVersionToCheck > 0 ) ) {
        $checkIliasVersions = true;
    }
    
    $errors = array();
    foreach( $plugins as $pluginName => $data ) {
        $url  = $data[ 'url' ];
        $prod = $data[ 'branch' ];
        $dev  = $data[ 'devbranch' ];

        $temp = array();
        // check, if url is correct
        if( $git->isProject( $url ) ) {
            $project = $git->getProject( $url );
            $branches = $project->getBranches();

            // check prodbranch
            if( isset( $branches[ $prod ] ) ) {           
                if( $checkIliasVersions ) {
                    $branch = $branches[ $prod ];
                    /** @var GitBranchDTO branch */

                    $min = $branch->getIliasMin();
                    $max = $branch->getIliasMax();

                    $out = "";
                    if( $iliasVersionToCheck < $min ) {
                        $out .= "min: " . $min;
                    }
                    if( $iliasVersionToCheck > $max ) {
                        if( strlen( $out ) > 0 ) {
                            $out .= '; ';
                        }
                        $out .= "max: " . $max;
                    }
                    if( strlen( $out ) > 0 ) {
                        if( isset( $temp[ 'branch' ] ) ) {
                            $out = $temp[ 'branch' ] . $out;
                        }
                        $temp[ 'branch' ] = $out;
                    }
                }                

            } else {
                $temp[ 'branch' ] = 'prodbranch doesnt exist.';
            }

            // check devbranch
            if( isset( $branches[ $dev ] ) ) {
                if( $checkIliasVersions ) {
                    $branch = $branches[ $dev ];
                    /** @var GitBranchDTO branch */

                    $min = $branch->getIliasMin();
                    $max = $branch->getIliasMax();

                    $out = "";
                    if( $iliasVersionToCheck < $min ) {
                        $out .= "min: " . $min;
                    }
                    if( $iliasVersionToCheck > $max ) {
                        if( strlen( $out ) > 0 ) {
                            $out .= '; ';
                        }
                        $out .= "max: " . $max;
                    }
                    if( strlen( $out ) > 0 ) {
                        if( isset( $temp[ 'devbranch' ] ) ) {
                            $out = $temp[ 'devbranch' ] . $out;
                        }
                        $temp[ 'devbranch' ] = $out;
                    }
                }
            } else {
                $temp[ 'devbranch' ] = 'devbranch doesnt exist.';
            }
            
            // check composer
            $composer = $data[ 'composer' ];
            if( strlen( $composer ) > 0 ) {
                if( strlen( $project->getComposer() ) == 0 ) {
                    $temp[ 'composer' ] = 'composer is used, but shouldnt';
                }
            }
            if( strlen( $composer ) == 0 ) {
                if( strlen( $project->getComposer() ) > 0 ) {
                    $temp[ 'composer' ] = 'composer is not used, but should';
                }
            }

        } else {
            $temp[ 'url' ] = 'url doesnt exist';
        }

        if( ! empty( $temp ) ) {
            $errors[ $pluginName ] = $temp;
        }
    }
    return $errors;
}