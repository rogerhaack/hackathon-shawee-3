<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptRunning;
use App\Http\Requests\Coordinates;
use App\Trip;

class DriverController extends Controller
{
    private $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function searchSeller()
    {
        try {
            $result = $this->trip->getTrip();

            return response()->success("Sucesso", $result);
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function acceptRunning(AcceptRunning $acceptRunning)
    {
        try {
            $acceptRunningValidated = $acceptRunning->validated();

            $this->trip->acceptRunning($acceptRunningValidated);

            return response()->success("Sucesso");
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function getCoordinates(Coordinates $coordinates)
    {
        try {
            $coordinatesValidated = $coordinates->validated();

            $result = $this->trip->getCoordinates($coordinatesValidated);

            return response()->success("Sucesso", $result);
        } catch (\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

}
