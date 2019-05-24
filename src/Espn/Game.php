<?php namespace CarlosCruz\Espn;

use CarlosCruz\Espn\Team;
use CarlosCruz\Espn\Interfaces\Jsonable;

/**
 * ESPN Game
 * 
 * @author Carlos Cruz <jccs24@gmail.com>
 */
class Game implements Jsonable
{

    public $hometeam;
    public $awayteam;
    public $time;
    public $score_home;
    public $score_away;
    public $stadium;
    public $espnId;
    public $state;

    /**
     * [__construct description]
     * 
     * @param Team        $home    [description]
     * @param Team        $away    [description]
     * @param String|null $time    [description]
     * @param String      $espnid  [description]
     * @param String|null $score   [description]
     * @param String|null $stadium [description]
     * @param [type]      $state   [description]
     */
    function __construct(
        Team $home, 
        Team $away,
        String $time = null, 
        String $espnid, 
        String $score = null, 
        String $stadium = null, 
        String $state = null,
        String $league = null
    ) {
        $this->hometeam = $home;
        $this->awayteam = $away;
        $this->time = $time;
        $this->espnId = $espnid;

        if ($score!=null && $score != "v") {
            $sc = explode('-', $score);
            $this->score_home = (int) trim($sc[0]);
            $this->score_away = (int) trim($sc[1]);
        }

        if ($stadium!=null) {
            $this->stadium = $stadium;
        }

        if ($state != null) {
            $this->state = $state;
        }
        if ($league != null) {
            $this->league = $league;
        }
    }

    /**
     * Return game in json format
     * 
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Return game in array format
     * 
     * @return array
     */
    public function toArray()
    {
        return (array) $this;
    }
}
