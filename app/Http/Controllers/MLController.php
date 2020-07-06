<?php

namespace App\Http\Controllers;

use App\Helpers\MercadoLivreHelper;
use App\Http\Requests\CredentialML;
use App\UsersCredentials;

class MLController extends Controller
{
    private $usersCredentials;
    private $mlHelper;

    public function __construct(UsersCredentials $usersCredentials, MercadoLivreHelper $mercadoLivreHelper)
    {
        $this->usersCredentials = $usersCredentials;
        $this->mlHelper = $mercadoLivreHelper;
    }

    function insertCode(CredentialML $credentialML)
    {
        try {
            $credentialMLValidated = $credentialML->validated();

            $this->usersCredentials->insertCredential($credentialMLValidated);

            return response()->success("Sucesso");
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    function getAllSales()
    {
        try {
            $result = $this->mlHelper->getAllSales();

            return response()->success("Sucesso", $result);
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

}
