<?php

namespace App\Helpers;


use App\UsersCredentials;

class MercadoLivreHelper
{
    private $urlBase = "https://api.mercadolibre.com";
    private $apiRequest;
    private $usersCredentials;

    private $allStatus = [
        "confirmed" => "Confirmado",
        "payment_required" => "Requer pagamaneto",
        "payment_in_process" => "Pagamento em progresso",
        "partially_paid" => "Parcialmente pago",
        "paid" => "Pago",
        "cancelled" => "Cancelado",
        "invalid" => "Invalida"
    ];

    public function __construct()
    {
        $this->apiRequest = new ApiRequest();
        $this->usersCredentials = new UsersCredentials();
    }

    private function getToken()
    {
        try {
            $time = round(microtime(true) * 1000) - 10800000;
            $credentials = $this->usersCredentials->getCredentials();

            if ($credentials->refreshToken !== null) {

                if ($credentials->expirationDate <= ($time + 1800000)) {
                    return $this->refreshToken($credentials->refreshToken);
                } else {
                    return $credentials->token;
                }

            } else {
                return $this->generateToken();
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function refreshToken($refreshToken)
    {
        try {
            $time = round(microtime(true) * 1000) - 10800000;
            $code = $this->usersCredentials->getCredentials();

            $credentials = $this->apiRequest->postRequest($this->urlBase . "/oauth/token?grant_type=refresh_token&client_id=" . env("APP_ID") . "&client_secret=" . env("SECRET_KEY") . "&refresh_token=" . $refreshToken, []);

            $this->usersCredentials->updateCredentials(
                [
                    "token" => $credentials['access_token'],
                    "refreshToken" => $credentials['refresh_token'],
                    "userIdML" => $credentials['user_id'],
                    "expirationDate" => $time + 21600000
                ],
                $code->id
            );

            return $credentials['access_token'];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function generateToken()
    {
        try {
            $time = round(microtime(true) * 1000) - 10800000;
            $code = $this->usersCredentials->getCredentials();

            $credentials = $this->apiRequest->postRequest($this->urlBase . "/oauth/token?grant_type=authorization_code&client_id=" . env("APP_ID") . "&client_secret=" . env("SECRET_KEY") . "&code=" . $code->code . "&redirect_uri=http://localhost:3000/verify-token", []);

            $this->usersCredentials->updateCredentials(
                [
                    "token" => $credentials['access_token'],
                    "refreshToken" => $credentials['refresh_token'],
                    "userIdML" => $credentials['user_id'],
                    "expirationDate" => $time + 21600000
                ],
                $code->id
            );

            return $credentials['access_token'];

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getAllSales()
    {
        try {
            $token = $this->getToken();
            $credentials = $this->usersCredentials->getCredentials();

            $sales = [];

            $response = $this->apiRequest->getRequest("{$this->urlBase}/orders/search?seller={$credentials->userIdML}&access_token={$token}");

            foreach ($response['data']['results'] as $item) {

                $product = $item['order_items'][0];
                $productInfo = $this->getProductInfo($product['item']['id']);

                $allDimensions = "";
                $dimensions = "";

                if (isset($item['shipping']['shipping_items'])) {
                    $allDimensions = explode(",", $item['shipping']['shipping_items'][0]['dimensions']);
                    $dimensions = explode("x", $allDimensions[0]);
                }

                $saleId = $item['id'];
                $productName = $product['item']['title'];
                $quantity = $product['quantity'];
                $price = $item['total_amount'];
                $photo = $productInfo['thumbnail'];
                $status = $this->allStatus[$item['status']];
                $mlProductId = $product['item']['id'];
                $length = isset($dimensions[0]) ? intval($dimensions[0]) : 0;
                $width = isset($dimensions[1]) ? intval($dimensions[1]) : 0;
                $height = isset($dimensions[2]) ? intval($dimensions[2]) : 0;
                $weight = isset($allDimensions[1]) ? intval($allDimensions[1]) : 0;

                array_push($sales, [
                    "mlProductId" => $mlProductId,
                    "saleId" => $saleId,
                    "title" => $productName,
                    "quantity" => $quantity,
                    "price" => $price,
                    "photo" => $photo,
                    "status" => $status,
                    "length" => $length,
                    "width" => $width,
                    "height" => $height,
                    "weight" => $weight
                ]);

            }

            return $sales;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductInfo($mlCode)
    {
        try {
            $token = $this->getToken();

            $response = $this->apiRequest->getRequest("{$this->urlBase}/items?ids={$mlCode}&access_token={$token}");

            if (count($response['data']) > 0) {
                return $response['data'][0]['body'];
            } else {
                throw new \Exception("Erro ao consultar produdo");
            }

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}