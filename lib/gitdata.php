<?php

final class GitData {
    
    /**
     * 
     * @var self
     */
    private static $instance = null;
    
    /**
     * 
     * @var stdClass[]
     */
    private $data;
    
    private function __construct() {
        $json = file_get_contents( __DIR__ . '/gitdata.json' );
        $this->data = json_decode( $json );
    }
    
    public static function getInstance(): GitData {
        if( self::$instance == null ) {
            self::$instance = new GitData();
        }
        return self::$instance;
    }    
    
    public function getData() {
        return $this->data;
    }
    
    /**
     * 
     * @param string $url
     * @return GitProjectDto | null
     */
    public function getProject( string $url ) {
        if( ! isset ( $this->data->$url ) ) {
            return null;
        }
        return new GitProjectDTO( $this->data->$url );
    }
    
    public function isProject( string $url ): bool {
        return ( $this->getProject( $url ) != null );
    }
    
    public function search( string $search ) {
        $out = array();
        foreach( $this->data as $url => $data ) {
            if( strpos( strtolower( $url ), strtolower( $search ) ) != false ) {
                $out[] = $this->getProject( $url );
            }
        }
        return $out;
    }
}

class GitBranchDTO {
    /**
     * 
     * @var string
     */
    protected $name;
    
    /**
     * 
     * @var string
     */
    protected $ilias_min;
    
    /**
     * 
     * @var string
     */
    protected $ilias_max;
    
    /**
     * 
     * @var string
     */
    protected $composer;
    
    /**
     * 
     * @var string
     */
    protected $composer_vendor;
    
    public function __construct( stdClass $item ) {
        $this->name = "" . $item->name;
        $this->ilias_min = "";
        $this->ilias_max = "";
        $this->composer = "";
        $this->composer_vendor = "";
                
        if( isset( $item->ilias_min ) ) {
            $this->ilias_min = "" . $item->ilias_min;
        }
        if( isset( $item->ilias_max ) ) {
            $this->ilias_max = "" . $item->ilias_max;
        }        
        if( isset( $item->composer ) ) {
            $this->composer = "" . $item->composer;
        }        
        if( isset( $item->composer_vendor ) ) {
            $this->composer_vendor = "" . $item->composer_vendor;
        }                
    }
    
    public function getName(): string {
        return $this->name;
    }

    public function getIliasMin(): string {
        return $this->ilias_min;
    }

    public function getIliasMax(): string {
        return $this->ilias_max;
    }
    
    public function getComposer(): string {
        return $this->composer;
    }

    public function getComposer_vendor(): string {
        return $this->composer_vendor;
    }

    public function getComposerVendor(): string {
        return $this->getComposer_vendor();
    }
    
    
    public function isComposer(): bool {
        return ( strlen( $this->composer ) > 0 );
    }

    public function isComposer_vendor(): bool {
        return ( strlen( $this->composer_vendor ) > 0 );
    }

    public function isComposerVendor(): bool {
        return $this->getComposer_vendor();
    }
}

class GitProjectDTO {
    
    /**
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var string
     */
    protected $name;
    
    /**
     * 
     * @var string
     */
    protected $gitpath;
    
    /**
     * 
     * @var string
     */
    protected $filepath;
    
    /**
     * 
     * @var string
     */
    protected $repourl;
        
    /**
     * 
     * @var GitBranchDTO[]
     */
    protected $branches;
        
    public function __construct( stdClass $item ) {
        $this->id       = "" . $item->id;
        $this->name     = "" . $item->name;
        $this->filepath = "" . $item->filepath;
        $this->gitpath  = "" . $item->gitpath;
        $this->repourl  = "" . $item->repourl;
        $temp = array();
        foreach( $item->branches as $name => $data ) {
            $temp[ $name ] = new GitBranchDTO( $data );
        }
        $this->branches = $temp;
    }
    
    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getGitpath(): string {
        return $this->gitpath;
    }

    public function getFilepath(): string {
        return $this->filepath;
    }

    public function getRepourl(): string {
        return $this->repourl;
    }
    
    /**
     * 
     * @return GitBranchDTO[]
     */
    public function getBranches(): array {
        return $this->branches;
    }
}