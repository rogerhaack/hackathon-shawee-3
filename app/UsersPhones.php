<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersPhones extends Model
{
    protected $table = "users_phones";

    public function insertUsersPhones($userRegister, $userId)
    {
        try {

            $userPhones = new UsersPhones();
            $userPhones->area_code = $userRegister['phone']['areaCode'];
            $userPhones->number = $userRegister['phone']['number'];
            $userPhones->users_id = $userId;
            $userPhones->save();

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir telefone.");
        }
    }
}
