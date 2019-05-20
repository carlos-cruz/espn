<?php namespace Espn;

use Espn\Team;
use Espn\Interfaces\Jsonable;

/**
 * 
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

	
	function __construct(Team $home,Team $away,$time=null,String $espnid,String $score = null,String $stadium = null,$state = null)
	{
		$this->hometeam = $home;
		$this->awayteam = $away;
		$this->time = $time;
		$this->espnId = strrchr($espnid,'=') ? substr(strrchr($espnid,'=') ,1,10):$espnid;
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

	public function toJson()
	{
		return json_encode($this);
	}

	public function toArray()
	{
		return (array) $this;
	}
}


?>