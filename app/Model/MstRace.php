<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MstRace extends CompModel
{
    protected $table = 'mst_race';
    
    static function getGeligaRace($param) {
        $result = MstRace::select(DB::raw('SUM(at.distance) AS total_distance'), 'at.race_id', 'mst_race.race_name')
                  ->join('activity_trackings as at', DB::raw('at.race_id::INTEGER'), 'mst_race.id')
                  ->where('slug', 'iLike', '%geliga%')
                  ->groupBy('at.race_id', 'mst_race.race_name')
                  ->get();

        return $result;
    }
    
    static function getListGeligaRace() {
        $result = MstRace::select('athlete_id', 'at.race_id', 'mst_race.race_name', DB::raw('SUM(at.distance) AS total_distance'))
                  ->join('activity_trackings as at', DB::raw('at.race_id::INTEGER'), 'mst_race.id')
                  ->where('slug', 'iLike', '%geliga%')
                  ->groupBy('athlete_id', 'at.race_id', 'mst_race.race_name')
                  ->get()->toArray();

        return $result;
    }
    
    static function getGeligaRaceMst($param) {
        $result = MstRace::where('slug', 'iLike', '%geliga%')
                  ->get();

        return $result;
    }
}
