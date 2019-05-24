<?php namespace CarlosCruz\Espn;


use CarlosCruz\Espn\Game;
use CarlosCruz\Espn\Team;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Espn Class
 * 
 * @author  Carlos Cruz <jccs24@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
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
        } else {
            $this->league = 'todo';
        }

        if (isset($props['date'])) {
            $this->date = $props['date'];
        }
    }

    /**
     * Loads the Espn page
     * 
     * @return none
     */
    private function _loadInfo()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_REFERER, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $str = curl_exec($curl);
        curl_close($curl);

        // Load HTML from a string
        $this->html = new Crawler($str); 
    }

    /**
     * Gets fixtures for the provided league
     * 
     * @return array
     */
    public function getFixtures(): array
    {
        //Sets the url to load
        $this->url = self::URLFIXTURES;
        $this->url .= strtolower($this->league);

        if (isset($this->date)) {
            $this->url .= '/fecha/'.$this->date;
        }

        //Loads the html
        $this->_loadInfo();


        $games = $this->html->filter('.table-caption')->each(function(Crawler $fixture, $i){
            
            $resp['group'] = $fixture->text();

            $games = $fixture->parents()->filter('table.schedule')->eq($i);
            
            $resp['games'] = $games->filter('tbody tr')->each(function(Crawler $game, $j) use ($league) {
                //$league = $game->parents()->parents()->parents()->parents()->filter('h2.table-caption')->eq($i)->text();
                $home = new Team($game->filter('td a.team-name span')->first()->text());
                $away = new Team($game->filter('td a.team-name span')->last()->text());
                $time = $game->filter('td')->eq(2)->attr('data-date');
                $espnid = (int) filter_var($game->filter('td span.record a')->eq(0)->attr('href'), FILTER_SANITIZE_NUMBER_INT);
                $score = $game->filter('td span.record a')->first()->text();
                $stadium = ($game->filter('td.schedule-location')->count()) ? $game->filter('td.schedule-location')->first()->text():null;

                $game = new Game($home, $away, $time, $espnid, $score, $stadium);
                return $game->toArray();
            });
            return $resp;
        });

        return $games;
    }

    /**
     * Gets the information of a single match
     * 
     * @param int $matchid An Espn match id
     * 
     * @return Game
     */
    public function getMatch(int $matchid): Game
    {
        $this->url = self::URLMATCH.$matchid;
        $this->_loadInfo();
        
        $home = new Team(trim($this->html->filter('div.sm-score div.away div.team__content div.team-container div.team-info span.short-name')->text()));
        $away = new Team(trim($this->html->filter('div.sm-score div.home div.team__content div.team-container div.team-info span.short-name')->text()));
        $home->logo = trim($this->html->filter('div.sm-score div.away div.team__content div.team-container div.team-info-logo a.logo picture img')->attr('src'));
        $away->logo = trim($this->html->filter('div.sm-score div.home div.team__content div.team-container div.team-info-logo a.logo picture img')->attr('src'));

        $time = $this->html->filter('div.game-status span')->attr('data-date');
        $score_home = trim($this->html->filter('div.sm-score div.away div.score-container span')->text());
        $score_away = trim($this->html->filter('div.sm-score div.home div.score-container span')->text());
        $score = $score_home.' - '.$score_away;

        $state = utf8_encode(trim($this->html->filter('span.game-time')->text()));
        $st = $this->html->filter('div#gamepackage-game-information li.venue div')->eq(0)->text();
        $stadium = trim(substr($st, strpos($st, ':')+1));

        return new Game($home, $away, $time, $matchid, $score, $stadium, $state);
    }
}
