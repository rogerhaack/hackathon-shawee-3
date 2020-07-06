<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersCredentials extends Model
{
    protected $table = "users_credentials";

    public function insertCredential($code)
    {
        try {

            $cretential = new UsersCredentials();
            $cretential->code = $code['code'];
            $cretential->users_id = Auth::user()->id;
            $cretential->save();

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir credenciais");
        }
    }

    public function getCredentials()
    {
        try {

            $result = DB::table("users_credentials")
                ->select("id", "user_id_ml as userIdML", "token", "refresh_token AS refreshToken", "code", "expiration_date AS expirationDate")
                ->where("users_id", "=", Auth::user()->id)
                ->get();

            if (count($result) > 0) {
                return $result[0];
            } else {
                throw new \Exception("Nao existe credenciais para este usuario");
            }

        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar credenciais");
        }
    }

    public function updateCredentials($credentials, $credentialId)
    {
        try {

            $credential = UsersCredentials::find($credentialId);
            $credential->token = $credentials['token'];
            $credential->refresh_token = $credentials['refreshToken'];
            $credential->expiration_date = $credentials['expirationDate'];
            $credential->user_id_ml = $credentials['userIdML'];
            $credential->save();

        } catch (\Exception $e) {
            throw new \Exception("Erro ao atualizar credenciais");
        }
    }

}
