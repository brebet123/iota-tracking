<?php

namespace App;

use App\Constants\ErrorCode as EC;
use App\Constants\ErrorMessage as EM;

class Helper {
    static function responseData($data = false, $paginate = null){
        if (!$data && [] !== $data) $data = json_decode("{}");
        if($paginate == null){
            $data = ["meta" => ['error' => EC::NOTHING,'message' => EM::NONE ],
            "data" => $data ];
        }else{
            $data = ["meta" => ['error' => EC::NOTHING,'message' => EM::NONE,'page' => $paginate ],
                    "data" => $data ];
        }

        return response()->json($data, 200);
    }

    static function createResponse($EC, $EM, $data = false) {
        if (!$data && [] !== $data) $data = json_decode("{}");

        $data = [
            "meta" => ['error' => $EC, 'message' => $EM ],
            "data" => $data
        ];

        if ($EC > 0 || is_string($EC)) unset($data['data']);
        return response()->json($data, 200);
    }
}