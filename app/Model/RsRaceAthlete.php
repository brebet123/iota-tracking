<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;

class RsRaceAthlete extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_race_athlete';

    static function getGeligaRace($param) {
        $result = RsRaceAthlete::whereIn('race_id', $param)
                  ->join('rs_member_authenticated_athlete as ms', 'rs_race_athlete.athlete_id', 'ms.id')
                  ->join('rs_race as rr', 'rs_race_athlete.race_id', 'rr.id')
                  ->select('rs_race_athlete.id', 'rs_race_athlete.athlete_id', 'rs_race_athlete.race_id', 'rs_race_athlete.created_at', 'rr.to_date', 'rr.race_activity_type')
                  ->get();

        return $result;
    }

    static function getGeligaRacePerRace($param) {
        try {
            $result = RsRaceAthlete::where('race_id', $param)
                      ->join('rs_member_authenticated_athlete as ms', 'rs_race_athlete.athlete_id', 'ms.id')
                      ->join('rs_race as rr', 'rs_race_athlete.race_id', 'rr.id')
                      ->select('rs_race_athlete.id', 'rs_race_athlete.athlete_id', 'rs_race_athlete.race_id', 'rs_race_athlete.created_at', 'rr.to_date', 'rr.race_activity_type')
                      ->get();

        return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function getGeligaRacePerRaceNonStrava($param) {
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

    static function getGeligaLeaderboard($param) {
        try {
            return RsRaceAthlete::join('rs_member_authenticated_athlete as ms', 'rs_race_athlete.athlete_id', 'ms.id')
                   ->join('rs_race as rr', 'rs_race_athlete.race_id', 'rr.id')
                   ->join('rs_member_activity as rma', 'rs_race_athlete.athlete_id', 'rma.athlete_id')
                   ->where('rr.id', $param)
                   ->whereRaw('date(`start_date`) >= date(ms.created_at)')
                   ->whereRaw('date(`start_date`) <= date(rr.to_date)')
                   ->select('race_id', 'rma.athlete_id', 'ms.firstname AS name', 'ms.profile', DB::raw('sum(rma.distance) AS total_distance'))
                   ->groupBy('race_id', 'ms.firstname', 'rma.athlete_id')
                   ->orderBy('total_distance', 'DESC')
                   ->limit(10)
                   ->get();

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
