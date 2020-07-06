<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ApiRequest
{
    public function getRequest($url)
    {
        try {
            $http = new Client;

            $response = $http->request("GET", $url);

            return [
                "status" => $response->getStatusCode(),
                "data" => json_decode((string)$response->getBody(), true)
            ];

        } catch (\Exception $e) {
            throw new \Exception("Erro ao fazer requisição GET");
        }
    }

    public function postRequest($url, $body)
    {
        try {
            $http = new Client;

            $response = $http->request("POST", $url, $body);

            if ($response->getStatusCode() == 200) {
                return json_decode((string)$response->getBody(), true);
            } else {
                throw new \Exception("Erro ao fazer requisição POST");
            }

        } catch (\Exception $e) {
            print_r($e->getMessage());
            throw new \Exception("Erro ao fazer requisição POST");
        }
    }
}