<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    protected $table = "sale";

    public function insertSale($mlCode)
    {
        try {

            $sale = new Sale();
            $sale->sale_code_mlb = $mlCode;
            $sale->users_id = Auth::user()->id;
            $sale->save();

            return $sale->id;

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir");
        }
    }
}
