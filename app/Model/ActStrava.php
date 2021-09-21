<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActStrava extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_member_activity_non_strava';
    
    static function cekData($param) {
        $result = ActStrava::where('id', $param)->first();

        return $result;
    }
    
    static function updateDatas($param) {
        $result = ActStrava::where('id', $param->external_id_restep)->update(['athlete_id' => $param->athlete_id]);

        return $result;
    }
}
