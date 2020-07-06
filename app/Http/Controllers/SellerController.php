<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestWithdrawal;
use App\Trip;

class SellerController extends Controller
{
    private $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function requestWithdrawal(RequestWithdrawal $requestWithdrawal)
    {
        try {
            $requestWithdrawalValidated = $requestWithdrawal->validated();

            $this->trip->requestWithdrawal($requestWithdrawalValidated);

            return response()->success("Sucesso");
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function verifyWithdrawalStatus()
    {
        try {
            $result = $this->trip->verifyWithdrawalStatus();

            return response()->success("Sucesso", $result);
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

}
