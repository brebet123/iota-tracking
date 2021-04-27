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
        $activity_tracking = ActivityTracking::where('activity_trackings_code', $request->activity_trackings_code)->first();
        $activity_tracking->athlete_name = $userClientName;
        $activity_tracking_decode = Polyline::decode($activity_tracking->polyline);
        $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
        $activity_tracking->tracking = $activity_tracking_pair;
        $activity_tracking->pace_km = json_decode($activity_tracking->pace_km, true);
        $activity_tracking->pace_50m = json_decode($activity_tracking->pace_50m, true);
        $activity_tracking->date = date('Y-m-d H:i:s', strtotime($activity_tracking->created_at));

        return Helper::responseData($activity_tracking);
    }

    public function getListMember(Request $request) {
        $users = User::getUser( $request->bearerToken());
        $userClientName = UserClient::getName($users->email_client);
        $activity_tracking = ActivityTracking::where('athlete_id', $request->athlete_id)->orderBy('id', 'DESC')->get();
        
        foreach($activity_tracking as $key => $val) {
            $val->athlete_name = $userClientName;;
            $activity_tracking_decode = Polyline::decode($val->polyline);
            $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
            $val->tracking = $activity_tracking_pair;
            $val->pace_km = json_decode($val->pace_km, true);
            $val->pace_50m = json_decode($val->pace_50m, true);
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
        $data['pace_km'] = json_encode($data['pace_km']);
        $data['pace_50m'] = json_encode($data['pace_50m']);
        
        unset($data['api_token']);
        unset($data['tracking']);

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
            $activity_tracking = ActivityTracking::where('athlete_id', $request->athlete_id)->orderBy('id', 'DESC')->get();
            
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
