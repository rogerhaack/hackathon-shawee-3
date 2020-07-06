<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    protected $table = "sale_product";

    public function insertSaleProduct($saleId, $productId)
    {
        try {

            $saleProduct = new SaleProduct();
            $saleProduct->sale_id = $saleId;
            $saleProduct->product_id = $productId;
            $saleProduct->save();

        } catch (\Exception $e) {
            throw new \Exception("Erro ao linkar produto e venda");
        }
    }
}
