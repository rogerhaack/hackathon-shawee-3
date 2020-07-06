<?php

namespace App;

use App\Helpers\FilterString;
use Illuminate\Database\Eloquent\Model;

class UsersVehicles extends Model
{
    protected $table = "users_vehicles";

    public function insertUsersVehicles($userRegister, $userId)
    {
        try {

            $userVehicles = new UsersVehicles();
            $userVehicles->height = $userRegister['car']['measures']['height'];
            $userVehicles->width = $userRegister['car']['measures']['width'];
            $userVehicles->length = $userRegister['car']['measures']['length'];
            $userVehicles->model = FilterString::filter($userRegister['car']['model']);
            $userVehicles->license_plate = FilterString::filter($userRegister['car']['plate']);
            $userVehicles->users_id = $userId;
            $userVehicles->save();

        } catch (\Exception $e) {
            print_r($e->getMessage());
            throw new \Exception("Erro ao inserir veiculo.");
        }
    }
}
