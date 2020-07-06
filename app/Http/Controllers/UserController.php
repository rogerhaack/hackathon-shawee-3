<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Http\Requests\UserRegister;
use App\User;

class UserController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(UserLogin $userLogin)
    {
        try {
            $userLoginValidated = $userLogin->validated();

            $result = $this->user->login($userLoginValidated);

            return response()->success("Sucesso", $result);

        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function register(UserRegister $userRegister)
    {
        try {
            $userRegisterValidated = $userRegister->validated();

            $result = $this->user->register($userRegisterValidated);

            return response()->success("Sucesso", $result);

        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function getUser()
    {
        try {
            $result = $this->user->getUser();

            return response()->success("Sucesso", $result);
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

}
