<?php

namespace App;

use App\Helpers\GoogleHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Trip extends Model
{
    protected $table = "trip";

    public function requestWithdrawal($requestWithdrawal)
    {
        DB::beginTransaction();
        try {
            $usersAddresses = new UsersAddresses();
            $sale = new Sale();
            $package = new Package();
            $product = new Product();
            $saleProduct = new SaleProduct();
            $googleHelper = new GoogleHelper();

            $coordinatesInitial = $usersAddresses->getCoordinates();
            $coordinatesFinal = $googleHelper->getPostOfficeLocation($coordinatesInitial->latitude, $coordinatesInitial->longitude);

            $tripId = $this->insertTrip($coordinatesInitial->latitude, $coordinatesInitial->longitude, $coordinatesFinal['lat'], $coordinatesFinal['lng']);

            foreach ($requestWithdrawal as $item) {
                $saleId = $sale->insertSale($item['saleId']);

                $package->insertPackage($tripId, $saleId);
                $productId = $product->insertProduct($item);

                $saleProduct->insertSaleProduct($saleId, $productId);

            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
//            throw new \Exception("Erro ao solicitar corrida");
        }
    }

    private function insertTrip($startLat, $startLon, $endLat, $endLon)
    {
        try {
            DB::insert("INSERT INTO trip (start_coordinate,end_coordinate,created_at,updated_at) VALUES (POINT(?,?),POINT(?,?),NOW(),NOW());", [$startLat, $startLon, $endLat, $endLon]);
            $id = DB::select("SELECT LAST_INSERT_ID() AS id;");

            if (count($id) > 0) {
                return $id[0]->id;
            } else {
                throw new \Exception("Erro ao buscar id");
            }

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir corrida");
        }
    }

    public function getTrip()
    {
        try {
            $googleHelper = new GoogleHelper();

            $result = DB::select("
                SELECT 
                    t.id as tripId,
                    u.name,
                    ST_X(start_coordinate) AS `startLatitude`,
                    ST_Y(start_coordinate) AS `startLongitude`,
                        ST_X(end_coordinate) AS `endLatitude`,
                    ST_Y(end_coordinate) AS `endLongitude`
                FROM
                    `trip` AS `t`
                        INNER JOIN
                    `package` AS `p` ON `t`.`id` = `p`.`trip_id`
                        INNER JOIN
                    `sale` AS `s` ON `s`.`id` = `p`.`sale_id`
                        INNER JOIN
                    `users` AS `u` ON `u`.`id` = `s`.`users_id`
                WHERE
                    `t`.`users_vehicles_id` IS NULL
                LIMIT 1;
                ");


            if (count($result) > 0) {
                $startLocation = $googleHelper->getAddressByGeolocation($result[0]->startLatitude, $result[0]->startLongitude);
                $startAddress = $startLocation[0]['formatted_address'];

                $endLocation = $googleHelper->getAddressByGeolocation($result[0]->endLatitude, $result[0]->endLongitude);
                $endAddress = $endLocation[0]['formatted_address'];

                return [
                    "tripId" => $result[0]->tripId,
                    "sellerName" => $result[0]->name,
                    "startAddress" => [
                        "address" => $startAddress,
                        "latitude" => $result[0]->startLatitude,
                        "longitude" => $result[0]->startLongitude
                    ],
                    "endAddress" => [
                        "address" => $endAddress,
                        "latitude" => $result[0]->endLatitude,
                        "longitude" => $result[0]->endLongitude
                    ]
                ];
            } else {
                return [];
            }


        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar corrida");
        }
    }

    public function acceptRunning($tripId)
    {
        try {
            $usersVehiclesId = DB::table("users as u")
                ->join("users_vehicles as uv", "u.id", "=", "uv.users_id")
                ->where("u.id", "=", Auth::user()->id)
                ->get();

            if (count($usersVehiclesId) > 0) {

                $trip = Trip::find($tripId['tripId']);
                $trip->users_vehicles_id = $usersVehiclesId[0]->id;
                $trip->save();

            } else {
                throw new \Exception("Não existe nenhum veiculo para este usuário");
            }

        } catch (\Exception $e) {
            throw new \Exception("Erro ao aceitar corrida");
        }
    }

    public function verifyWithdrawalStatus()
    {
        try {
            $products = [];

            $result = DB::table("trip as t")
                ->select("t.users_vehicles_id", "pa.label")
                ->join("package as p", "t.id", "=", "p.trip_id")
                ->join("sale as s", "s.id", "=", "p.sale_id")
                ->join("sale_product as sp", "sp.sale_id", "=", "s.id")
                ->join("product as pa", "pa.id", "=", "sp.product_id")
                ->where("s.users_id", "=", Auth::user()->id)
                ->orderBy("t.created_at", "DESC")
                ->get();


            foreach ($result as $prod) {
                $status = $prod->users_vehicles_id ? "Aceito" : "Aguardando";

                array_push($products, ["title" => $prod->label, "status" => $status]);
            }

            return $products;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            throw new \Exception("Erro ao verificar status de corridas");
        }
    }

    public function getCoordinates($coordinatesValidated){
        try {
            $googleHelper = new GoogleHelper();

            return $googleHelper->getCoordinates($coordinatesValidated);

        } catch (\Exception $e) {
            throw new \Exception("Erro ao buscar coordenadas");
        }
    }
}
