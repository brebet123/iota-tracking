<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\ActivityTracking;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\User;
use App\Helper;
use App\Polyline;
use App\Model\UserClient;
use App\Model\GlobalParam;
use App\Model\LogDataExternal;
use App\Model\LeaderBoardMirror;
use App\Services\RestepService;
use App\Model\GlobalParam AS GP;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ActivityController extends Controller
{   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function getList(Request $request) {
        $users = User::getUser( $request->bearerToken());
        $userClientName = UserClient::getName($users->email_client);
        $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id', 'image')->where('activity_trackings_code', $request->activity_trackings_code)->select('activity_trackings.*', 'global_param.param_name AS type_name')->first();
        $activity_tracking->athlete_name = $userClientName;
        $activity_tracking_decode = Polyline::decode($activity_tracking->polyline);
        $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
        $activity_tracking->tracking = $activity_tracking_pair;
        $activity_tracking->pace_km = isset($activity_tracking->pace_km) ? json_decode($activity_tracking->pace_km, true) : null;
        $activity_tracking->pace_50m = isset($activity_tracking->pace_50m) ? json_decode($activity_tracking->pace_50m, true) : null;
        $activity_tracking->date = date('Y-m-d H:i:s', strtotime($activity_tracking->created_at));
        $activity_tracking->image = $activity_tracking->image ? url('uploads/img/'.$users->id.'/IMG-ACT-'.$activity_tracking->id.'.jpg') : '';
        $images = [];
        if($activity_tracking->images) {
            foreach(json_decode($activity_tracking->images) as $vals) {
                $images[] = url('uploads/img/'.$users->id.'/'.$vals);
            }
        }
        $activity_tracking->images = $images;

        return Helper::responseData($activity_tracking);
    }

    public function getListMember(Request $request) {
        $users = User::getUser($request->bearerToken());
        $getDataExternalRestep = RestepService::dispallacts($users->email_client, $request);
        // dd($getDataExternalRestep);
        if($getDataExternalRestep) {
            $mapData = ActivityTracking::mapData($users, $getDataExternalRestep);
        }
        $userClientName = UserClient::getName($users->email_client);
        $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id')->where('athlete_email', $users->email_client)->orderBy('id', 'DESC')->select('activity_trackings.*', 'global_param.param_name AS type_name')->paginate(100);
        
        foreach($activity_tracking as $key => $val) {
            $val->athlete_name = $userClientName;;
            $activity_tracking_decode = Polyline::decode($val->polyline);
            $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
            $val->tracking = $activity_tracking_pair;
            $val->pace_km = isset($val->pace_km) ? json_decode($val->pace_km, true) : null;
            $val->pace_50m = isset($val->pace_50m) ? json_decode($val->pace_50m, true) : null;
            $val->date = date('Y-m-d H:i:s', strtotime($val->created_at));
            $val->image = $val->image ? url('uploads/img/'.$users->id.'/IMG-ACT-'.$val->id.'.jpg') : '';
        }

        $pages['page'] = $activity_tracking->currentPage();
        $pages['perPage'] = $activity_tracking->perPage();
        $pages['total'] = $activity_tracking->total();
        $pages['lastPage'] = $activity_tracking->lastPage();

        return Helper::responseData($activity_tracking->items(), $pages);
    }

    public function getListMembers(Request $request) {
        $users = User::getUser($request->bearerToken());
        $getDataExternalRestep = RestepService::dispallacts($users->email_client, $request);
        // dd($getDataExternalRestep);
        if($getDataExternalRestep) {
            $mapData = ActivityTracking::mapData($users, $getDataExternalRestep);
        }
        $userClientName = UserClient::getName($users->email_client);
        $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id')->where('athlete_email', $users->email_client)->orderBy('id', 'DESC')->select('activity_trackings.*', 'global_param.param_name AS type_name')->paginate();
        
        foreach($activity_tracking as $key => $val) {
            $val->athlete_name = $userClientName;;
            $activity_tracking_decode = Polyline::decode($val->polyline);
            $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
            $val->tracking = $activity_tracking_pair;
            $val->pace_km = isset($val->pace_km) ? json_decode($val->pace_km, true) : null;
            $val->pace_50m = isset($val->pace_50m) ? json_decode($val->pace_50m, true) : null;
            $val->date = date('Y-m-d H:i:s', strtotime($val->created_at));
            $val->image = $val->image ? url('uploads/img/'.$users->id.'/IMG-ACT-'.$val->id.'.jpg') : '';
            $images = [];
            if($val->images) {
                foreach(json_decode($val->images) as $vals) {
                    $images[] = url('uploads/img/'.$users->id.'/'.$vals);
                }
            }
            $val->images = $images;
        }

        $pages['page'] = $activity_tracking->currentPage();
        $pages['perPage'] = $activity_tracking->perPage();
        $pages['total'] = $activity_tracking->total();
        $pages['lastPage'] = $activity_tracking->lastPage();

        return Helper::responseDatas($activity_tracking->items(), $pages);
    }

    public function add(Request $request) {
        $data = $request->all();
        $users = User::getUser( $request->bearerToken());
        $genId = RestepService::generateId();
        // dd($users);
        $activity_tracking = new ActivityTracking;
        $arrTracking = $request->tracking;
        $polyline = Polyline::encode($arrTracking);
        $data['pace_km'] = isset($data['pace_km']) ? json_encode($data['pace_km']) : null;
        $data['pace_50m'] = isset($data['pace_50m']) ? json_encode($data['pace_50m']) : null;
        $data['type_id'] = isset($data['type_act']) ? GlobalParam::getId($data['type_act']) : null;
        unset($data['api_token']);
        unset($data['tracking']);
        unset($data['type_act']);
        unset($data['image']);
        unset($data['images']);

        foreach($data as $key => $val) {
            $activity_tracking->{$key} = $val;
        }
        $activity_tracking->external_id_restep = $genId;

        $activity_tracking['user_id'] = $users->id;
        $activity_tracking['polyline'] = $polyline;

        if($activity_tracking->save()) {
            if($request->image) {
                $path = 'uploads/img/'.$users->id;
                if (!File::exists($path)) {File::makeDirectory('uploads/img/'.$users->id, 0775, true);}
                $imageName = 'IMG-ACT-'.$activity_tracking->id.'.'.'jpg';
                File::put('uploads/img/'.$users->id.'/' . $imageName, base64_decode($request->image));
                $activity_tracking->image = $imageName;
                $activity_tracking->save();
            }

            if($request->images) {
                foreach($request->images as $key => $val) {
                    $path = 'uploads/img/'.$users->id;
                    if (!File::exists($path)) {File::makeDirectory('uploads/img/'.$users->id, 0775, true);}
                    $imageName = 'IMG-ACT-'.$activity_tracking->id.'-'.($key+1).'.'.'jpg';
                    File::put('uploads/img/'.$users->id.'/' . $imageName, base64_decode($val));
                    $imageNames[] = $imageName;
                }

                $activity_tracking->images = json_encode($imageNames);
                $activity_tracking->save();
            }

            $datasArr = [
                'id' => $genId,
                'resource_state' => 333,
                'athlete_id' => $users->id_client,
                'name' => $activity_tracking->title,
                'slug' => $activity_tracking->title.'-'.$genId,
                'distance' => $activity_tracking->distance,
                'moving_time' => $activity_tracking->moving_time,
                'elapsed_time' => $activity_tracking->duration,
                'total_elevation_gain' => $activity_tracking->elevation,
                'type' => GP::getById($activity_tracking->type_id)->param_name,
                'start_date' => date('Y-m-d H:i:s'),
                'start_date_local' => date('Y-m-d H:i:s'),
                'timezone' => '(GMT+07:00) Asia/Jakarta',
                'map_summary_polylin' => $activity_tracking->polyline,
                'average_speed' => $activity_tracking->avg_speed,
                'max_speed' => $activity_tracking->max_speed,
                'action' => 'C'
            ];
            // dd($datasArr);
            $pushDataToApi = RestepService::setactivities_nonstrava($datasArr, $request);
            return Helper::createResponse(200, 'Success', $activity_tracking);

        } else {
            return Helper::createResponse(EC::INTERNAL_ERROR_SERVER, EM::INTERNAL_SERVER_ERROR);
        }
    }

    public function getListDataUpdated(Request $request) {
        try {
            $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id')->where('athlete_id', $request->athlete_id)->orderBy('id', 'DESC')->select('activity_trackings.*', 'global_param.param_name AS type_name')->get();
            
            foreach($activity_tracking as $key => $val) {
                $activity_tracking_decode = Polyline::decode($val->polyline);
                $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
                $val->tracking = $activity_tracking_pair;
                $val->pace_km = json_decode($val->pace_km, true);
                $val->pace_50m = json_decode($val->pace_50m, true);
                $val->created_at = date('Y-m-d H:i:s', strtotime($val->created_at));
            }

            return Helper::responseData($activity_tracking);

        } catch(\Throwable $th) {
            throw $th;
        }
    }

    public function tes(Request $request) {
        $arr = [];
        $activity_tracking_decode = Polyline::decode($arr);
        $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
    }

    public function getLeaderBoard(Request $request) {
        try {
            $checkLog = LogDataExternal::cekData();

            if($checkLog) {
                LeaderBoardMirror::truncate();
                $getDataExternalRestep = RestepService::getLeaderBoard();
                $dataChunk = array_chunk($getDataExternalRestep, 200);

                foreach($dataChunk as $val) {
                    $insertBulk = LeaderBoardMirror::insert(json_decode(json_encode($val),true));
                }
            }

            $result = LeaderBoardMirror::getData($request);

            $pages['page'] = $result->currentPage();
            $pages['perPage'] = $result->perPage();
            $pages['total'] = $result->total();
            $pages['lastPage'] = $result->lastPage();

            return Helper::responseDatas($result->items(), $pages);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
