<?php

namespace App\Http\Controllers\Parking;

use App\Dtos\Parking\OutputParkingDTO;
use App\Dtos\Parking\ParkingDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParkingController extends Controller
{
    public function __construct(protected Parking $parking)
    {
    }

    public function index()
    {
        $parkings = $this->parking::all();

        $parkingDTOs = collect($parkings)->map(function ($parking) {
            return new OutputParkingDTO(
                $parking->id,
                $parking->name,
                $parking->numberOfVacancies,
                $parking->active,
                $parking->created_at,
                $parking->cars,
                $parking->employees,
            );
        })->all();

        return ParkingResource::collection(
            collect($parkingDTOs)
        );
    }

    public function store(Request $request)
    {
        $dto = new ParkingDTO(
            ...$request->only([
                'name',
                'numberOfVacancies',
                'active',
            ])
        );

        $parking = $this->parking::create($dto->toArray());

        return $this->outputResponse($parking);
    }

    public function show(string $id)
    {
        $parking = $this->getParkingById($id);

        return $this->outputResponse($parking);
    }

    public function update(Request $request, string $id)
    {
        $parking = $this->getParkingById($id);

        $dto = new ParkingDTO(
            ...$request->only([
                'name',
                'numberOfVacancies',
                'active',
            ])
        );

        $parking->update($dto->toArray());

        return $this->outputResponse($parking);
    }

    public function destroy(string $id)
    {
        $parking = $this->getParkingById($id);

        $parking->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse($parking)
    {
        $error = [];

        if (is_null($parking)) {
            $error = [
                'erro'    => true,
                'message' => 'Registro não encontrado',
            ];
        }
        $outputDto = new OutputParkingDTO(
            $parking['id'] ?? null,
            $parking['name'] ?? null,
            $parking['numberOfVacancies'] ?? null,
            $parking['active'] ?? null,
            $parking['created_at'] ?? null,
            $parking['cars'] ?? null,
            $parking['employees'] ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? null
        );

        return new ParkingResource($outputDto);
    }

    private function getParkingById($parkingId)
    {
        $parking = $this->parking::with('cars')->find($parkingId);

        if (!$parking) {
            return $this->outputResponse($parking);
        }

        return $parking;
    }
}
