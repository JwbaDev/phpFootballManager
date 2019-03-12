<?php

class Fixtures extends System{

    public $id;
    public $home;
    public $away;
    public $score;
    public $datePlayed;
    public $uid;

    public static $tableName = "fixtures";
    public static $columns = ['id', 'home', 'away', 'score', 'datePlayed', 'uid'];

    public function __construct($args=[]) {

        $this->id = isset($args['id']) ? $args['id'] : "";
        $this->home = isset($args['home']) ? $args['home'] : "";
        $this->away = isset($args['away']) ? $args['away'] : "";
        $this->score = isset($args['score']) ? $args['score'] : "";
        $this->datePlayed = isset($args['datePlayed']) ? $args['datePlayed'] : "";
        $this->uid = isset($args['uid']) ? $args['uid'] : "";

    }


    public static function matchDates() {

        /*$seasonStart = "2017-08-14";
        $seasonEnd = "2018-05-13";
        $amountOfGames = 38;*/

        $fixt = array();

        $sql = "SELECT id FROM premier_league ORDER BY RAND()";
        $result = DB::getCon()->query($sql);

        $teamArray = array();

        if($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {

                $id = $row['id'];

                $teamArray[] = $id;

            }

        }

        for($i = 0; $i < 19; $i++) {

            foreach($teamArray as $team) {                   

                $fixtTeam = ['home' => $team];

                $fixture = new Fixtures($fixtTeam);

                array_push($fixt, $fixture);

            }

        }

        $fixtCount = count($fixt);

        for($i = 0; $i < 19; $i++) {

            foreach($teamArray as $team) {

                for($j = 0; $j < $fixtCount; $j++) {

                    $HT = $fixt[$j]->home;
                    $AT = $fixt[$j]->away;

                    $away = Fixtures::findGameNum($fixt, $HT, $team);

                    if($HT != $team && empty($AT)) {

                        if($away < 1) {

                            $fixt[$j]->away = $team;
                        }

                    }

                }

            }

        }
        
        $tracker  = array();
        $newArray = array();
        
        for($i = 0; $i < 38; $i++) {
         
            $newArray[] = array(["", "", "", "", ""], ["", "", "", "", ""]);
            
        }
        
        //Each newArray element is equal to a week worth of game time
        
        foreach($newArray as $week) {
            
            foreach($week as $day) {

                foreach($day as $gameSlot) {

                    for($i = 0; $i < $fixtCount; $i++) {
                    
                        $fixture = $fixt[$i];
                        $HT = $fixture->HT;
                        $AT = $fixture->AT;
                     
                        //Check if game is eligible
                        $weekCheck = Fixtures::weekCheck($week, $HT, $AT);
                        $gameExistCheck = Fixtures::gameExistCheck($tracker, $HT, $AT);
                        $alternateGames = Fixtures::alternateGames($tracker, $HT, $AT);
                        
                        if($weekCheck == true && $gameExistCheck == true && $alternateGames == true) {
                         
                            //Add fixture to game slot and tracker
                            $gameSlot = $fixture;
                            $tracker  = $fixture;
                            
                        }
                        
                    }

                }
             
                
            }
            
        }

        return($newArray);

    }
    
    private static function weekCheck($week, $HT, $AT) {
        //Check to make sure the teams haven't already played that week
        //return true if everything is okay
        foreach($week as $day) {

            foreach($day as $game) {

                if($game->HT == $HT || $game->AT == $HT) {

                    return false; 

                } else if($game->HT == $AT || $game->AT == $AT) {

                    return false;

                }

            }

        }

        return true;
            
    }
    
    private static function gameExistCheck($tracker, $HT, $AT) {
        //Check to make sure the game doesn't already exist
        //return true is everything is okay
        foreach($tracker as $game) {

            if($game->HT == $HT && $game->AT == $AT) {

                return false;

            }

        }

        return true;
        
    }
    
    private static function alternateGames($tracker, $HT, $AT) {
        //Check to make sure that the same game but not at home or away doesn't already exist
        //return true is everything is okay

        foreach($tracker as $game) {

            if($game->HT == $AT && $game->AT == $HT) {

                return false;

            }

        }

        return true;
        
    }
    

    private static function findGameNum($array, $HT, $AT) {

        $c = 0;

        $count = count($array);

        for($i = 0; $i < $count; $i++) {

            if($array[$i]->home == $HT && $array[$i]->away == $AT) {

                $c++;

            }

        }

        return $c;

    }
    
}


?>
