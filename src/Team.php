<?php namespace Espn;

/**
 * ESPN Team
 * 
 * @author Carlos Cruz <jccs24@gmail.com>
 * 
 */
class Team
{

    public $name;
    public $logo;
    
    /**
     * [__construct description]
     * 
     * @param String $name Name of the team
     */
    function __construct(String $name)
    {
        $this->name = $name;
    }
}
