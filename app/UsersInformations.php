<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersInformations extends Model
{
    protected $table = "users_informations";

    public function insertInformations($userRegister, $userId)
    {
        try {

            $userInformation = new UsersInformations();
            $userInformation->document = preg_replace('/[^0-9]/', '', $userRegister['document']);
            $userInformation->type = $userRegister['type'];
            $userInformation->users_id = $userId;
            $userInformation->save();

        } catch (\Exception $e) {

            throw new \Exception("Erro ao inserir informações.");
        }
    }
}
