<?php

namespace App\Http\Controllers\Cars;

use App\Dtos\Cars\CarsDTO;
use App\Dtos\Cars\OutputCarsDTO;
use App\Dtos\Error\ErrorDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\Parking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarsController extends Controller
{

    public function __construct(protected Car $cars)
    {}

    public function index()
    {
        $carros = $this->cars::all();

        return CarResource::collection($carros);
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

        $car = $this->cars::create($dto->toArray());
        $car = $this->cars::find($car->id);

        return $this->outputResponse($car);

    }

    public function show(string $id)
    {
        $car = $this->cars::find($id);

        if(empty($car)){
            return $this->outputErrorResponse($id);
        }

        return new CarResource($car);
    }

    public function update(Request $request, string $id)
    {
        $car = $this->cars::find($id);

        if(empty($car)){
            return $this->outputErrorResponse($id);
        }

        $dto = new CarsDTO(
            ...$request->only([
                'plate',
                'model',
                'color',
                'parking_id',
            ])
        );

        $car->update($dto->toArray());

        return new CarResource($car);
    }

    public function registersCarExit(string $id)
    {
        $car = $this->cars::find($id);

        if(empty($car)){
            return $this->outputErrorResponse($id);
        }

        $car->output = now();
        $car->save();

        return new CarResource($car);
    }

    public function destroy(string $id)
    {
        $car = $this->cars::find($id);

        if(empty($car)){
            return $this->outputErrorResponse($id);
        }

        $car->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse(Car $car){

        $output =  new OutputCarsDTO(
            $car['id'],
            $car['plate'],
            $car['model'],
            $car['color'],
            $car['input']
        );

        return response()->json($output->toArray());
    }

    private function outputErrorResponse(int $id){

        $error = new ErrorDTO("Registro {$id} não encontrado", Response::HTTP_NOT_FOUND);
        return response()->json($error->toArray(), Response::HTTP_NOT_FOUND);
    }
}

