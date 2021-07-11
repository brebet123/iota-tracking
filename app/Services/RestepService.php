<?php 

namespace App\Services;
use Ixudra\Curl\Facades\Curl;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
class RestepService {

    /*
        service get token
    */
    static function generateToken()
    {
        try {
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => config('services.hris.url').'/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'name='.config('services.hris.user').'&password=apijamkrindo#2019',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            if(!$response) throw new CustomException("Ups: silahkan ulangi beberapa saat lagi.");
            if(!json_decode($response)->success) throw new CustomException(json_decode($response)->message);
            return json_decode($response)->token;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function getactivities_nonstrava()
    {
        try {
            $responses = Curl::to(config('services.restep.url').'/getactivities_nonstrava')
                         ->withContentType('application/json')
                         ->returnResponseObject()
                         ->withOption('USERPWD', config('services.restep.user').':'.config('services.restep.password'))
                         ->withData(['ID_App' => "RESTEP.ID", 'Data' => ['id' => null, 'limit' => 1]])
                         ->asJson()
                         ->returnResponseObject()
                         ->post();

            $response = $responses->content;
            if($response->status != "success") throw new CustomException("Ups: silahkan ulangi beberapa saat lagi.");

            return $response->Data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function dispallacts($email, $request)
    {
        try {
            $responses = Curl::to(config('services.restep.url').'/dispallacts')
                         ->withContentType('application/json')
                         ->returnResponseObject()
                         ->withOption('USERPWD', config('services.restep.user').':'.config('services.restep.password'))
                         ->withData(["ID_App" => "RESTEP.ID", 
                                     "Data" => [["refresh_token" => self::validate_uid()->refresh_token]],
                                     "DataActivities" => [["emailUser" => $email, "typeAct" => "All"]]
                                    ])
                         ->asJson()
                         ->returnResponseObject()
                         ->post();

            // dd($responses);
            $response = $responses->content;
            if($response->message != "Activities found") return $responses = false;

            return $response->Data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function setactivities_nonstrava($data)
    {
        try {
            $responses = Curl::to(config('services.restep.url').'/setactivities_nonstrava')
                         ->withContentType('application/json')
                         ->returnResponseObject()
                         ->withOption('USERPWD', config('services.restep.user').':'.config('services.restep.password'))
                         ->withData($data)
                         ->asJson()
                         ->returnResponseObject()
                         ->post();

            $response = $responses->content;
            if($response->status != "success") throw new CustomException("Ups: silahkan ulangi beberapa saat lagi.");

            return $response->Data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function validate_uid()
    {
        try {
            $responses = Curl::to(config('services.restep.url').'/validate_uid')
                         ->withContentType('application/json')
                         ->returnResponseObject()
                         ->withOption('USERPWD', config('services.restep.user').':'.config('services.restep.password'))
                         ->returnResponseObject()
                         ->post();

            $response = json_decode($responses->content);
            if($response->status != "success") throw new CustomException("Ups: silahkan ulangi beberapa saat lagi.");

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
