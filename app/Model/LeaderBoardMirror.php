<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LeaderBoardMirror extends CompModel
{
    protected $table = 'leader_board_mirror';

    static function getData($param) {
        try {
            $limit = $param->limit ? $param->limit : 10;
 
            return LeaderBoardMirror::where(function($query) use($param) {
                if($param->where_value) {
                    $query->where('athlete_email', 'iLike', '%'.$param->where_value.'%');
                    $query->orWhere('socmed_name', 'iLike', '%'.$param->where_value.'%');
                }
            })
            ->paginate($limit);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
