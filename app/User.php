<?php

namespace App;

use App\Helpers\FilterString;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Função para efetuar login no sistema
    public function login($userLogin)
    {
        try {

            if (Auth::attempt(['email' => $userLogin['email'], 'password' => $userLogin['password']])) {

                $user = Auth::user();
                $this->deleteUserToken($user->id);

                $token = $this->createUserToken($user);

                $userInfo = $this->getUserInfo($user->id);

                return [
                    "user" => $userInfo,
                    "token" => $token
                ];

            } else {
                throw new \Exception("Usuário ou senha inválidos.");
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    // Função pata criação de um usuário no sistema
    public function register($userRegister)
    {
        DB::beginTransaction();
        try {

            if (!self::userExist($userRegister['email'])) {

                $user = new User();
                $user->name = FilterString::filter($userRegister['name']);
                $user->email = FilterString::filter($userRegister['email']);
                $user->password = bcrypt($userRegister['password']);
                $user->save();
                $userId = $user->id;

                $usersInformations = new UsersInformations();
                $usersInformations->insertInformations($userRegister, $userId);

                $userAddresses = new UsersAddresses();
                $userAddresses->insertUsersAddress($userRegister, $userId);

                $usersPhones = new UsersPhones();
                $usersPhones->insertUsersPhones($userRegister, $userId);

                if ($userRegister['type'] === 'D') {
                    $usersVehicles = new UsersVehicles();
                    $usersVehicles->insertUsersVehicles($userRegister, $userId);
                }

                DB::commit();

                return $this->login(["email" => $userRegister['email'], "password" => $userRegister['password']]);

            } else {
                throw new \Exception("Usuario ja existe");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function getUser()
    {
        try {
            return Auth::user();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Função para deletar token de um usuário
    private function deleteUserToken($userId)
    {
        try {

            DB::select("DELETE FROM `oauth_access_tokens` WHERE `name`='App' and `user_id` = ?", [$userId]);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    // Função para criar token de um usuário
    private function createUserToken($user)
    {
        return $user->createToken('App', [])->accessToken;
    }

    // Função para verificar se usuário exixte na base com base no email
    private function userExist($email)
    {
        return DB::table("users")
            ->where("email", "=", $email)
            ->exists();
    }

    // Função para buscar informações básicas de um usuário
    private function getUserInfo($userId)
    {
        try {

            $result = DB::table("users as u")
                ->select("u.email", "ui.document", "u.name", "ui.type")
                ->join("users_informations as ui", "u.id", "=", "ui.users_id")
                ->where("u.id", "=", $userId)
                ->get();

            $mlLogged = DB::table("users_credentials")
                ->where("users_id", "=", $userId)
                ->exists();

            if (count($result) > 0) {
                $res = $result[0];

                $res->mlLogged = $mlLogged;

                return $res;
            } else {
                return [];
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


}
