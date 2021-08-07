<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LogDataExternal extends CompModel
{
    protected $table = 'log_data_external';

    static function cekData() {
        try {
            $data = LogDataExternal::where('date', date('Y-m-d'))->where('leader_board', 1)->first();
            $update = false;

            if(!$data) {
                $update = true;
                $insert = new LogDataExternal;
                $insert->date = date('Y-m-d');
                $insert->leader_board = 1;
                $insert->save();
            }

            return $update;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
