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
    
    public function __construct( stdClass $item ) {
        $this->name = "" . $item->name;
        $this->ilias_min = "";
        if( isset( $item->ilias_min ) ) {
            $this->ilias_min = "" . $item->ilias_min;
        }
        if( isset( $item->ilias_max ) ) {
            $this->ilias_max = "" . $item->ilias_max;
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
    
    /**
     * 
     * @var string
     */
    protected $composer;
    
    public function __construct( stdClass $item ) {
        $this->id       = "" . $item->id;
        $this->name     = "" . $item->name;
        $this->filepath = "" . $item->filepath;
        $this->gitpath  = "" . $item->gitpath;
        $this->repourl  = "" . $item->repourl;
        $this->composer = "" . $item->composer;
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

    public function getBranches(): array {
        return $this->branches;
    }
    
    public function getComposer(): string {
        return $this->composer;
    }

}