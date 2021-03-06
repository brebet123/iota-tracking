<?php

namespace App\Http\Controllers\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Services\RestepService;
use App\Helper;
use App\User;
use App\Model\ActiveUser;
use App\Model\UserClient;
use App\Model\GlobalParam AS GP;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        try {
            $user = User::where('api_token', $request->app_key)->first();

            if(!$user) throw new CustomException("Token tidak terdaftar");
            $password = isset($user->password) ?  $user->password : GC::IS_NULL;
            $profile = clone $user;

            if ($user) {
                $active_user = ActiveUser::where('user_id', $user->id)->where('company_code', $user->company_code)->where('email_client', $request->email_client)->first();

                if(!$active_user) {
                    $active_user = new ActiveUser;
                    $user_client = new UserClient;
                    $user_client->email_client = $request->email_client;
                    $user_client->name_client = $request->name_client;
                    $user_client->id_client = $request->id_client;
                    $user_client->company_code = $user->company_code;
                    $user_client->save();
                    $accessToken = self::createJwt($user_client);
                    $refreshToken = self::createJwt($user_client, TRUE);
                
                } else {
                    $user_client = UserClient::where('company_code', $user->company_code)->where('email_client', $request->email_client)->first();
                    $user_client->name_client = $request->name_client;
                    $user_client->save();
                    $accessToken = $active_user->access_token;
                    $refreshToken = $active_user->refresh_token;
                }

                $active_user->user_id = $user->id;
                $active_user->access_token = $accessToken;
                $active_user->refresh_token = $refreshToken;
                $active_user->company_code = $user->company_code;
                $active_user->company_id = $user->company_id;
                $active_user->email_client = $request->email_client;
                $active_user->save();

                $users = new \stdClass;
                $users->name = $user->name;
                $users->company_code = $user->company_code;
                $users->access_token = $accessToken;
                // $users->refresh_token = $refreshToken;
            }

            else throw new CustomException("Email atau password salah.");
            $profile->access_token = $user->access_token;
            $profile->refresh_token = $user->refresh_token;

            return Helper::responseData($users);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function createJwt($data = NULL, $is_refresh_token = FALSE) {
        $issued_at = time();
        // Hanya meng-encode atribut: id, nama, email
        // unset($data->no_ponsel, $data->tempat_lahir, $data->tanggal_lahir, $data->id_firebase);
        // $payload = [
        //     'iss' => "restep-online", // Issuer of the token
        //     'sub' => $data, // Subject of the token
        //     'iat' => $issued_at, // Time when JWT was issued.
        //     'exp' => $is_refresh_token
        //         ?($issued_at + 60*60*24*30) // Waktu kadaluarsa 30 hari
        //         :($issued_at + 120*120) // Waktu kadaluarsa 2 jam
        // ];

        $payload = [
            'iss' => "restep-online", // Issuer of the token
            'sub' => $data, // Subject of the token
            'iat' => $issued_at // Time when JWT was issued.
        ];

        JWT::$leeway = 60; // $leeway dalam detik
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function register(Request $request) {
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['api_token'] = self::createJwt($request);
        // dd($data['api_token']);

        $user = new User;

        unset($data['password_confirmation']);
        
        foreach($data as $key => $value) {
            $user->{$key} = $value;
        }

        if($user->save()) {
            return Helper::responseData($user);
        }
    }

    public function refreshToken(Request $request) {
        $data = RestepService::validate_uid();

        return Helper::responseData($data);

    }

    public function version(Request $request) {
        try {
            $data = GP::version();

            return Helper::responseData($data);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
