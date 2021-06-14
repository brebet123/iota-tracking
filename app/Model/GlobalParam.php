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
}
