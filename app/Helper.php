<?php

namespace App;

use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;

class Helper {
    static function responseData($data = false, $paginate = null){
        if (!$data && [] !== $data) $data = json_decode("{}");
        if($paginate == null){
            $data = ['error' => EC::NOTHING,'message' => EM::NONE ,
            "data" => $data ];
        }else{
            $data = ['error' => EC::NOTHING,'message' => EM::NONE,'page' => $paginate,
                    "data" => $data ];
        }

        return response()->json($data, 200);
    }

    static function createResponse($EC, $EM, $data = false) {
        $ECM = $EC;

        if (!$data && [] !== $data) $data = json_decode("{}");

        if($EC == 200) $ECM = 0;

        $data = [
            'error' => $ECM, 'message' => $EM,
            "data" => $data
        ];

        if ($EC > 0 || is_string($EC)) unset($data['data']);

        return response()->json($data, $EC);
    }
}