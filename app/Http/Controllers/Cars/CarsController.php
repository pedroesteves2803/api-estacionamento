<?php

namespace App\Http\Controllers\Cars;

use App\Dtos\Cars\CarsDTO;
use App\Dtos\Cars\OutputCarsDTO;
use App\Dtos\Error\ErrorDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarsController extends Controller
{
    public function __construct(protected Car $cars)
    {
    }

    public function index()
    {
        $cars = $this->cars::all();

        $carsDTOs = collect($cars)->map(function ($car) {
            return new OutputCarsDTO(
                $car->id,
                $car->plate,
                $car->model,
                $car->color,
                $car->input,
                $car->parking_id,
            );
        })->all();

        return CarResource::collection(
            collect($carsDTOs)
        );
    }

    public function store(Request $request)
    {
        $dto = new CarsDTO(
            ...$request->only([
                'plate',
                'model',
                'color',
                'parking_id',
            ])
        );

        $dto->toArray()['input'] = now();

        $car = $this->cars::create($dto->toArray());

        $car = $this->cars::find($car['id']);

        return $this->outputResponse($car);
    }

    public function show(string $parkingId, string $id)
    {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $id,
        ])->first();

        if (empty($car)) {
            return $this->outputErrorResponse();
        }

        return $this->outputResponse($car);
    }

    public function update(Request $request, string $parkingId, string $id)
    {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $id,
        ])->first();

        if (!$car) {
            return $this->outputResponse($car);
        }

        $dto = new CarsDTO(
            ...$request->only([
                'plate',
                'model',
                'color',
                'parking_id',
            ]),
        );

        $car->update($dto->toArray());

        return $this->outputResponse($car);
    }

    public function registersCarExit(string $parkingId, string $id)
    {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $id,
        ])->first();

        if (!$car) {
            return $this->outputResponse($car);
        }

        $car->output = now();
        $car->save();

        $car = $this->cars::find($car['id']);

        return $this->outputResponse($car);
    }

    public function destroy(string $parkingId, string $id)
    {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $id,
        ])->first();

        if (!$car) {
            return $this->outputResponse($car);
        }

        $car->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse($car)
    {
        $error = [];

        if (is_null($car)) {
            $error = [
                'erro'    => true,
                'message' => 'Registro não encontrado',
            ];
        }

        $outputDto = new OutputCarsDTO(
            $car['id'] ?? null,
            $car['plate'] ?? null,
            $car['model'] ?? null,
            $car['color'] ?? null,
            $car['input'] ?? null,
            $car['parking_id'] ?? null,
            $car['output'] ?? null,
            $car['amountToBePaid'] ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? '',
        );

        return new CarResource($outputDto);
    }

    private function outputErrorResponse()
    {
        $error = new ErrorDTO('Registro não encontrado', Response::HTTP_NOT_FOUND);

        return response()->json($error->toArray(), Response::HTTP_NOT_FOUND);
    }
}
