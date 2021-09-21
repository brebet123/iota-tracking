<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActNonStrava extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_member_activity_non_strava';

    static function cekData($param) {
        $result = ActNonStrava::where('id', $param)->first();
        
        return $result;
    }
    
    static function updateDatas($param) {
        $result = ActNonStrava::where('id', $param->external_id_restep)->update(['athlete_id' => $param->athlete_id]);
        
        return $result;
    }
}
