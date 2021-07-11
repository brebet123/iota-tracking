<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\GlobalParam AS GP;

class ActivityTracking extends CompModel
{
    static function mapData($users, $datas) {
        try {
            $data = [];
            foreach($datas as $keey => $val) {
                $activity_trackings = ActivityTracking::where('external_id_restep', $val->id)->first();

                if(!$activity_trackings) {
                    $data = self::saveMappingData($users, $val);
                }
            }

            return $data;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function saveMappingData($user, $data) {
        try {
            $insert = new ActivityTracking;
            $insert->user_id = $user->id;
            $insert->athlete_email = $user->email_client;
            $insert->distance = $data->distance;
            $insert->max_speed = $data->max_speed;
            $insert->avg_speed = $data->average_speed;
            $insert->polyline = $data->map_summary_polyline;
            $insert->elevation = $data->total_elevation_gain;
            $insert->athlete_id = $user->id_client;
            $insert->type_id = GP::getIdByName($data->type);
            // $insert->duration = str_replace('.','',$data->moving_time);
            $insert->title = $data->name;
            // $insert->moving_time = str_replace('.','',$data->moving_time);
            $insert->external_id_restep = $data->id;
            $insert->save();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

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
