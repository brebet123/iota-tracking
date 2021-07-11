<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GlobalParam extends CompModel
{
    protected $table = 'global_param';
    
    static function getId($param) {
        $result = GlobalParam::where('param_type', 'ACTIVITY_TYPE')->where('param_code', $param)->select('id')->first();

        return $result->id;
    }

    static function getIdByName($param) {
        try {
            if($param == 'Ride') $param = 'Bike';
            $result = GlobalParam::where('param_type', 'ACTIVITY_TYPE')->where('param_name', $param)->first();
            
            if($result) {
                return $result->id;
            
            } else {
                return GlobalParam::where('param_type', 'ACTIVITY_TYPE')->where('param_code', 'WALK')->first()->id;
            }

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function getById($id) {
        try {
            $result = GlobalParam::find($id);
            
            return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
