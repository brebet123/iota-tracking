<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\ActivityTracking;
use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;
use App\User;
use App\Helper;
use App\Polyline;

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
        $activity_tracking = ActivityTracking::where('activity_trackings_code', $request->activity_trackings_code)->first();
        $activity_tracking_decode = Polyline::decode($activity_tracking->polyline);
        $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
        $activity_tracking->tracking = $activity_tracking_pair;

        return Helper::responseData($activity_tracking);
    }

    public function getListMember(Request $request) {
        $users = User::getUser( $request->bearerToken());
        $activity_tracking = ActivityTracking::where('athlete_id', $request->athlete_id)->orderBy('id', 'DESC')->get();
        
        foreach($activity_tracking as $key => $val) {
            $activity_tracking_decode = Polyline::decode($val->polyline);
            $activity_tracking_pair = Polyline::pair($activity_tracking_decode);
            $val->tracking = $activity_tracking_pair;
        }

        return Helper::responseData($activity_tracking);
    }

    public function add(Request $request) {
        $data = $request->all();
        $users = User::getUser( $request->bearerToken());

        $activity_tracking = new ActivityTracking;
        $arrTracking = $request->tracking;
        $polyline = Polyline::encode($arrTracking);

        unset($data['api_token']);
        unset($data['tracking']);

        foreach($data as $key => $val) {
            $activity_tracking->{$key} = $val;
        }

        $activity_tracking['user_id'] = $users->id;
        $activity_tracking['polyline'] = $polyline;

        if($activity_tracking->save()) {
            return Helper::createResponse(0, 'Success', $activity_tracking);

        } else {
            return Helper::createResponse(EC::INTERNAL_ERROR_SERVER, EM::INTERNAL_SERVER_ERROR);
        }
    }
}
