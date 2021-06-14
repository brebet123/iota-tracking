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
        $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id')->where('activity_trackings_code', $request->activity_trackings_code)->select('activity_trackings.*', 'global_param.param_name AS type_name')->first();
        $activity_tracking->athlete_name = $userClientName;
        $activity_tracking_decode = Polyline::decode($activity_tracking->polyline);
        $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
        $activity_tracking->tracking = $activity_tracking_pair;
        $activity_tracking->pace_km = isset($activity_tracking->pace_km) ? json_decode($activity_tracking->pace_km, true) : null;
        $activity_tracking->pace_50m = isset($activity_tracking->pace_50m) ? json_decode($activity_tracking->pace_50m, true) : null;
        $activity_tracking->date = date('Y-m-d H:i:s', strtotime($activity_tracking->created_at));

        return Helper::responseData($activity_tracking);
    }

    public function getListMember(Request $request) {
        $users = User::getUser( $request->bearerToken());
        $userClientName = UserClient::getName($users->email_client);
        $activity_tracking = ActivityTracking::leftJoin('global_param', 'activity_trackings.type_id', 'global_param.id')->where('athlete_id', $request->athlete_id)->orderBy('id', 'DESC')->select('activity_trackings.*', 'global_param.param_name AS type_name')->get();
        
        foreach($activity_tracking as $key => $val) {
            $val->athlete_name = $userClientName;;
            $activity_tracking_decode = Polyline::decode($val->polyline);
            $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
            $val->tracking = $activity_tracking_pair;
            $val->pace_km = isset($val->pace_km) ? json_decode($val->pace_km, true) : null;
            $val->pace_50m = isset($val->pace_50m) ? json_decode($val->pace_50m, true) : null;
            $val->date = date('Y-m-d H:i:s', strtotime($val->created_at));
        }

        return Helper::responseData($activity_tracking);
    }

    public function add(Request $request) {
        $data = $request->all();
        $users = User::getUser( $request->bearerToken());
        
        $activity_tracking = new ActivityTracking;
        $arrTracking = $request->tracking;
        $polyline = Polyline::encode($arrTracking);
        $data['pace_km'] = isset($data['pace_km']) ? json_encode($data['pace_km']) : null;
        $data['pace_50m'] = isset($data['pace_50m']) ? json_encode($data['pace_50m']) : null;
        $data['type_id'] = isset($data['type_act']) ? GlobalParam::getId($data['type_act']) : null;
        unset($data['api_token']);
        unset($data['tracking']);
        unset($data['type_act']);

        foreach($data as $key => $val) {
            $activity_tracking->{$key} = $val;
        }

        $activity_tracking['user_id'] = $users->id;
        $activity_tracking['polyline'] = $polyline;

        if($activity_tracking->save()) {
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
}
