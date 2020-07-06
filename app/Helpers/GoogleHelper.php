<?php

namespace App\Helpers;


class GoogleHelper
{
    private $baseUrl = "https://maps.googleapis.com/maps/api";
    private $apiRequest;
    private $googleToken;

    public function __construct()
    {
        $this->apiRequest = new ApiRequest();
        $this->googleToken = env("GOOGLE_API");
    }

    public function getGeolocation($address)
    {
        try {

            $addressFormat = $this->formatAddress($address);

            $response = $this->apiRequest->getRequest("{$this->baseUrl}/geocode/json?new_forward_geocoder=true&address={$addressFormat}&key={$this->googleToken}");

            return $response;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar geolocalização");
        }
    }

    public function getPostOfficeLocation($fromLat, $fromLon)
    {
        try {
//            $response = $this->apiRequest->getRequest("{$this->baseUrl}/place/findplacefromtext/json?input=AGF%20Correios&inputtype=textquery&fields=formatted_address,name,geometry&location={$fromLat},{$fromLon}&key={$this->googleToken}");

//            if (count($response['data']['candidates']) > 0) {
//                return $response['data']['candidates'][0]['geometry']['location'];
//            } else {
//                throw new \Exception("Não foi encontrado nenhuma agencia dos correios");
//            }

            return [
                "lat" => "-29.6836",
                "lng" => "-51.0982752"
            ];

        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar geolocalização dos correios");
        }
    }

    public function getAddressByGeolocation($lat, $lon)
    {
        try {

            $response = $this->apiRequest->getRequest("{$this->baseUrl}/geocode/json?latlng={$lat},{$lon}&key={$this->googleToken}");

            if (count($response['data']['results']) > 0) {

                return $response['data']['results'];

            } else {
                throw new \Exception("Nao foi encontrada endereço por geolocalização");
            }

        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar geolocalização");
        }
    }

    private function formatAddress($address)
    {
        return $address['address']['street'] . "+"
            . $address['address']['district'] . "+"
            . $address['address']['zipCode'] . "+"
            . $address['address']['city'] . "+"
            . $address['address']['state'] . "+"
            . $address['address']['number'];
    }


    public function getCoordinates($coordinatesValidated)
    {
        try {

            $response = $this->apiRequest->getRequest("{$this->baseUrl}/directions/json?origin={$coordinatesValidated['start']['latitude']},{$coordinatesValidated['start']['longitude']}&destination={$coordinatesValidated['end']['latitude']},{$coordinatesValidated['end']['longitude']}&key={$this->googleToken}");

            return ['points' => $response['data']['routes'][0]['overview_polyline']['points']];
        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar coordenadas");
        }

    }
}