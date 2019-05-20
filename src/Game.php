<?php namespace Espn;

use Espn\Team;
use Espn\Interfaces\Jsonable;

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
        String $state = null
    ) {
        $this->hometeam = $home;
        $this->awayteam = $away;
        $this->time = $time;
        $this->espnId = strrchr($espnid, '=') ? 
        substr(strrchr($espnid, '='), 1, 10) : $espnid;

        if ($score!=null && $score != "v") {
            $sc = explode('-', $score);
            $this->score_home = trim($sc[0]);
            $this->score_away = trim($sc[1]);
        }

        if ($stadium!=null) {
            $this->stadium = $stadium;
        }

        if ($state != null) {
            $this->state = $state;
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
