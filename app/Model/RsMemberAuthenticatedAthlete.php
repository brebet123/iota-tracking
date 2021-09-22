<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RsMemberAuthenticatedAthlete extends Model
{
    protected $connection = 'mysql';
    protected $table = 'rs_member_authenticated_athlete';

    static function getData($param) {
        $result = RsRaceAthlete::where('id', $param)->get();

        return $result;
    }
}
