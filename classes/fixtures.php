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

        $seasonStart = "2017-08-11";
        $seasonEnd = "2018-05-13";
        $amountOfGames = 38;

        $fixt = [];

        for($i = 0; $i < 19; $i++) {

            $sql = "SELECT id FROM premier_league ORDER BY RAND()";
            $result = DB::getCon()->query($sql);

            if($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {

                    $id = $row['id'];                   

                    $fixtTeam = ['home' => $id];


                    $fixture = new Fixtures($fixtTeam);

                    array_push($fixt, $fixture);

                }

            }

        }

        $fixtCount = count($fixt);

        for($i = 0; $i < 19; $i++) {

            $sql = "SELECT id FROM premier_league ORDER BY RAND()";
            $result = DB::getCon()->query($sql);
            

            if($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {

                    $id = $row['id'];  
                                  
                    for($j = 0; $j < $fixtCount; $j++) {

                        $HT = $fixt[$j]->home;
                        $AT = $fixt[$j]->away;

                        $away = Fixtures::findGameNum($fixt, $HT, $id);

                        if($HT != $id && empty($AT)) {

                            if($away < 1) {

                                $fixt[$j]->away = $id;
                            }

                        }

                    }
                   

                }

            }

        }


        return $fixt;

    }

    public static function findGameNum($array, $HT, $AT) {

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