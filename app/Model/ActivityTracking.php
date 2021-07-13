<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\GlobalParam AS GP;

class ActivityTracking extends CompModel
{
    static function mapData($users, $datas) {
        try {
            $data = [];
            // dd($datas);
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
            $arrPace = $data->moving_time != 0 ? [['pace' => floor($data->distance/$data->moving_time), 'time' => floor($data->moving_time)]] : [];
            $dataPace = json_encode($arrPace);
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
            $insert->duration = $data->elapsed_time;
            $insert->title = $data->name;
            $insert->start_time = date('H:i:s', strToTime($data->start_date_local));
            $insert->end_time = date_format(date_add(date_create($data->start_date_local), date_interval_create_from_date_string(floor($data->elapsed_time).' second')), 'H:i:s');
            $insert->pace_km = $dataPace;
            $insert->moving_time = $data->moving_time;
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
