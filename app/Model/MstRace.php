<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MstRace extends CompModel
{
    protected $table = 'mst_race';
    
    static function getGeligaRace($param) {
        $result = MstRace::select(DB::raw('SUM(at.distance) AS total_distance'), 'at.race_id', 'mst_race.race_name')
                  ->join('activity_trackings as at', 'at.race_id', 'mst_race.id')
                  ->where('slug', 'iLike', '%geliga%')
                  ->groupBy('at.race_id', 'mst_race.race_name')
                  ->get();

        return $result;
    }
}
