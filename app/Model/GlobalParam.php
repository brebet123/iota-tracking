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

    static function getById($id, $param_services) {
        try {
            $result = GlobalParam::find($id);

            if($param_services == 'services_restep') {
                if($result->param_code == 'BIKE') $result->param_name = 'Ride';
                if($result->param_code == 'RUN') $result->param_name = 'Run';
                if($result->param_code == 'WALK') $result->param_name = 'Walk';
            }

            return $result;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function version() {
        return GlobalParam::select('id', 'param_type', 'param_name', 'param_code')->where('param_type', 'VERSION')->get();
    }
}
