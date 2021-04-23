<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ActivityTracking extends CompModel
{
    protected static function boot() {
        static::creating(function($model)
        {
            $lastCodeId = ActivityTracking::max('id');
            $code = str_repeat("0", 4 - strlen($lastCodeId)).($lastCodeId + 1);
            $model->activity_trackings_code = 'ACT-'.date('Ymd').$code;
        });

        parent::boot();
    }
}
