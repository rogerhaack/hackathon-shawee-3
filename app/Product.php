<?php

namespace App;

use App\Helpers\FilterString;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "product";

    public function insertProduct($prod)
    {
        try {

            $product = new Product();
            $product->label = FilterString::filter($prod['title']);
            $product->length = intval($prod['length']);
            $product->width = intval($prod['width']);
            $product->height = intval($prod['height']);
            $product->weight = intval($prod['weight']);
            $product->id_mlb = $prod['mlProductId'];
            $product->save();

            return $product->id;

        } catch (\Exception $e) {
            throw new \Exception("Erro ao inserir produto");
        }
    }
}
