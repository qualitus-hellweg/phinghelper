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
        $fileSystemPath = $data[ 'path' ];
        
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
                    if( strlen( $min ) > 0 ) {
                        if( $iliasVersionToCheck < $min ) {
                            $out .= "min: " . $min;
                        }
                        if( $iliasVersionToCheck > $max ) {
                            if( strlen( $out ) > 0 ) {
                                $out .= '; ';
                            }
                            $out .= "max: " . $max;
                        }
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
                    if( strlen( $min ) > 0 ) {
                        if( $iliasVersionToCheck < $min ) {
                            $out .= "min: " . $min;
                        }
                        if( $iliasVersionToCheck > $max ) {
                            if( strlen( $out ) > 0 ) {
                                $out .= '; ';
                            }
                            $out .= "max: " . $max;
                        }
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
            if( 
                ( isset( $branches[ $prod ]) )
                && ( isset( $branches[ $dev ]) )
            ) {
                $outComposerErrors = '';
                $composer = $data[ 'composer' ];
            
            
                $prodBranch = $branches[ $prod ];
                $devBranch = $branches[ $dev ];

                if( strlen( $composer ) > 0 ) {
                    if( 
                        ( ! $prodBranch->isComposer() )
                        || ( $prodBranch->isComposer() && $prodBranch->isComposerVendor() ) 
                    ) {
                        $outComposerErrors .= 'prod-composer is used, but shouldnt <br/>' . PHP_EOL;
                    }
                    
                    if( 
                        ( ! $devBranch->isComposer() )
                        || ( $devBranch->isComposer() && $devBranch->isComposerVendor() ) 
                    ) {
                        $outComposerErrors .= 'dev-composer is used, but shouldnt <br/>' . PHP_EOL;
                    }
                    
                } else {
                    
                    if( 
                        ( ( $prodBranch->isComposer() ) && ( ! $prodBranch->isComposerVendor() ) )
                    ) {
                        $outComposerErrors .= 'prod-composer is not used, but should <br/>' . PHP_EOL;
                    }
                    
                    if( 
                        ( ( $devBranch->isComposer() ) && ( ! $devBranch->isComposerVendor() ) )
                    ) {
                        $outComposerErrors .= 'dev-composer is not used, but should <br/>' . PHP_EOL;
                    }
                    
                }

                if( strlen( $outComposerErrors ) > 0 ) {
                    $temp[ 'composer' ] = $outComposerErrors;
                }
            }
            
            // check fileSystemPath
            if( $project->getFilepath() != $fileSystemPath ) {
                $temp[ 'path' ] = 'path is different, git shows: "' . $project->getFilepath() . '"';
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