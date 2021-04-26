<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserClient extends CompModel
{
    
    static function getName($param) {
        $name = UserClient::where('email_client', $param)->select('name_client')->first();

        return $name->name_client;
    }
}
