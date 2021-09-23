<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RsMemberActivity extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_member_activity';
    
    static function getDistance($param) {
        $joindate = new Carbon($param->created_at);
        $result = RsMemberActivity::where('athlete_id', $param->athlete_id)
                  ->whereDate('start_date', '>=', $joindate->addHours(12))
                  ->whereDate('start_date', '<=', $param->to_date)
                  ->sum('distance');

        return $result;
    }
}
