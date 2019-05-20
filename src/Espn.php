<?php namespace Espn;

include_once('vendor/simple-html-dom/simple-html-dom/simple_html_dom.php');

use Espn\Game;
use Espn\Team;

/**
 * 
 */
class Espn
{
	const URLFIXTURES = "http://espndeportes.espn.go.com/futbol/calendario/_/liga/";
	const URLMATCH = "http://espndeportes.espn.go.com/futbol/ficha?id=";

	private $url;
	private $league;
	private $date;
	private $html;

	function __construct(Array $props)
	{
		if (isset($props['league'])) {
			$this->league = $props['league'];
		}else{
			$this->league = 'todo';
		}

		if (isset($props['date'])) {
			$this->date = $props['date'];
		}
	}


	private function loadInfo()
	{
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_REFERER, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($curl);
        curl_close($curl);

        $html = new \simple_html_dom();

        // Load HTML from a string
        $this->html = $html->load($str);
	}

	public function getFixtures(): array
	{
		//Sets the url to load
		$this->url = self::URLFIXTURES;
		$this->url .= strtolower($this->league);

		if (isset($this->date)) {
			$this->url .= '/fecha/'.$this->date;
		}

		//Loads the html
		$this->loadInfo();

		$games = [];
		foreach ($this->html->find('table.schedule') as $fixture)
		{
			foreach ($fixture->find('tbody tr') as $game) {
				if ($game->parent->tag == "tbody" && count($game->find('td a.team-name')) > 0) {
					$home = new Team($game->find('td a.team-name span',0)->innertext);
					$away = new Team($game->find('td a.team-name span',1)->innertext);
					$time = @$game->find('td',2)->{'data-date'};
					$espnid = $game->find('td span.record a',0)->href;
					$score = $game->find('td span.record a',0)->innertext;
					$stadium = @$game->find('td.schedule-location',0)->innertext;

					$game = new Game($home,$away,$time,$espnid,$score,$stadium);
					$games[] = $game->toArray();
				}
			}
		}

		return $games;
	}

	public function getMatch(int $matchid): Game
	{
		$this->url = self::URLMATCH.$matchid;
		$this->loadInfo();
		
		$home = new Team(trim($this->html->find('div.sm-score div.away div.team__content div.team-container div.team-info > span.short-name',0)->innertext));
		$away = new Team(trim($this->html->find('div.sm-score div.home div.team__content div.team-container div.team-info > span.short-name',0)->innertext));
		$home->logo = trim($this->html->find('div.sm-score div.away div.team__content div.team-container div.team-info-logo a.logo picture img',0)->attr['src']);
		$away->logo = trim($this->html->find('div.sm-score div.home div.team__content div.team-container div.team-info-logo a.logo picture img',0)->attr['src']);

		$time = $this->html->find('div.game-status span',1)->{'data-date'};
		$score_home = trim($this->html->find('div.sm-score div.away div.team__content div.score-container span',0)->innertext);
		$score_away = trim($this->html->find('div.sm-score div.home div.team__content div.score-container span',0)->innertext);
		$score = $score_home.' - '.$score_away;

		$state = utf8_encode(trim($this->html->find('span.game-time', 0)->innertext));
		$matchgoals = $this->html->find('div[id=custom-nav] div.game-details',1);
		$status = @$matchgoals->find('div.game-status',0)->innertext;
		$st = $this->html->find('div#gamepackage-game-information li.venue div',0)->innertext;
		$stadium = trim(substr($st, strpos($st, ':')+1));

		return new Game($home,$away,$time,$matchid,$score,$stadium,$state);
	}
}

?>