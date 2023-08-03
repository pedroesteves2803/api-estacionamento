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
    {}

    public function index()
    {

        $parking = $this->parking::all();

        return ParkingResource::collection($parking);
    }

    public function store(Request $request)
    {
        $dto = new ParkingDTO(
            ...$request->only([
                'name',
                'numberOfVacancies',
                'active'
            ])
        );

        $parking = $this->parking::create($dto->toArray());

        return $this->outputResponse($parking);
    }

    public function show(string $id)
    {
        $parking = $this->parking::with('cars')->findOrFail($id);

        return $this->outputResponse($parking);
    }

    public function update(Request $request, string $id)
    {
        $dto = new ParkingDTO(
            ...$request->only([
                'name',
                'numberOfVacancies',
                'active'
            ])
        );

        $parking = $this->parking::findOrFail($id);

        $parking->update($dto->toArray());

        return $this->outputResponse($parking);
    }

    public function destroy(string $id)
    {
        $parking = $this->parking::findOrFail($id);

        $parking->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse(Parking $parking){
        $output =  new OutputParkingDTO(
            $parking['id'],
            $parking['name'],
            $parking['numberOfVacancies'],
            $parking['active'],
            $parking['created_at'],
            // $parking['cars'],
            // $parking['employees'],
        );

        return response()->json($output->toArray());
    }
}
