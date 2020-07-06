<?php

namespace App;

use App\Helpers\FilterString;
use App\Helpers\GoogleHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersAddresses extends Model
{
    protected $table = "users_addresses";

    public function insertUsersAddress($userRegister, $userId)
    {
        try {
            $userAddresses = new UsersAddresses();
            $userAddresses->street = FilterString::filter($userRegister['address']['street']);
            $userAddresses->district = FilterString::filter($userRegister['address']['district']);
            $userAddresses->zip_code = FilterString::filter($userRegister['address']['zipCode']);
            $userAddresses->city = FilterString::filter($userRegister['address']['city']);
            $userAddresses->state = FilterString::filter($userRegister['address']['state']);
            $userAddresses->number = FilterString::filter($userRegister['address']['number']);
            $userAddresses->complement = FilterString::filter($userRegister['address']['complement']);
            $userAddresses->users_id = $userId;
            $userAddresses->save();

            $googleHelper = new GoogleHelper();
            $coordinates = $googleHelper->getGeolocation($userRegister);

            $coor = $coordinates['data']['results'][0]['geometry']['location'];
            $lat = $coor['lat'];
            $lon = $coor['lng'];

            DB::insert("UPDATE users_addresses SET coordinate = POINT(?,?) WHERE users_id = ?", [$lat, $lon, $userId]);

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir endereço.");
        }
    }

    public function getCoordinates()
    {
        try {
            $result = DB::select("SELECT ST_X(coordinate) as latitude, ST_Y(coordinate) as longitude FROM users_addresses WHERE users_id = ?", [Auth::user()->id]);

            if (count($result) > 0) {
                return $result[0];
            } else {
                throw new \Exception("Erro ao buscar coordenadas.");
            }

        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar coordenadas.");
        }
    }
}
