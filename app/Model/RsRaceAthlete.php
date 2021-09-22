<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RsRaceAthlete extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_race_athlete';

    static function getGeligaRace($param) {
        $result = RsRaceAthlete::whereIn('race_id', $param)
                  ->join('rs_member_authenticated_athlete as ms', 'rs_race_athlete.athlete_id', 'ms.id')
                  ->join('rs_race as rr', 'rs_race_athlete.race_id', 'rr.id')
                  ->select('rs_race_athlete.id', 'rs_race_athlete.athlete_id', 'rs_race_athlete.race_id', 'rs_race_athlete.created_at', 'rr.to_date')
                  ->get();

        return $result;
    }

    static function getGeligaRacePerRace($param) {
        try {
            $result = RsRaceAthlete::where('race_id', $param)
                      ->join('rs_member_authenticated_athlete as ms', 'rs_race_athlete.athlete_id', 'ms.id')
                      ->join('rs_race as rr', 'rs_race_athlete.race_id', 'rr.id')
                      ->select('rs_race_athlete.id', 'rs_race_athlete.athlete_id', 'rs_race_athlete.race_id', 'rs_race_athlete.created_at', 'rr.to_date')
                      ->get();

        return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
