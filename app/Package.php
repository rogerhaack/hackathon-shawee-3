<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = "package";

    public function insertPackage($tripId, $saleId)
    {
        try {

            $package = new Package();
            $package->trip_id = $tripId;
            $package->sale_id = $saleId;
            $package->save();

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir pacote");
        }
    }
}
