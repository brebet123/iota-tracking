<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RsMemberActivity extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_member_activity';
    
    static function getDistance($param) {
        $result = RsMemberActivity::where('athlete_id', $param->athlete_id)
                  ->whereDate('start_date', '>=', $param->created_at)
                  ->whereDate('start_date', '<=', $param->to_date)
                  ->sum('distance');

        return $result;
    }
}
