<?php

namespace App\Http\Controllers\Cars;

use App\Dtos\Cars\CarsDTO;
use App\Dtos\Cars\OutputCarsDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CarsController.
 *
 * @OA\Tag(
 *     name="Carros",
 *     description="API endpoints de carros"
 * )
 */
class CarsController extends Controller
{
    public function __construct(protected Car $cars)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/car",
     *     summary="Buscar todas as informações",
     *     tags={"Carros"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/CarResource")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/car",
     *     summary="Criar novo carro",
     *     tags={"Carros"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/CarsDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/CarResource"
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if($this->verifiedRequest($request->all(), 4)){
            return $this->outputResponse(null);
        };

        if (empty($request->all())) {
            return $this->outputResponse(null, 'Não foi possivel adicionar um novo carro!');
        }

        if(!Parking::where('id', $request->id)->exists()){
            return $this->outputResponse(null, 'Estacionamento não existe!');
        };

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

    /**
     * @OA\Get(
     *     path="/api/car/{parking_id}/{car_id}",
     *     summary="Buscar carro por ID do estacionamento e ID do carro",
     *     tags={"Carros"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\Parameter(
     *         name="parking_id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="car_id",
     *         in="path",
     *         required=true,
     *         description="ID do carro",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/CarResource"
     *         )
     *     )
     * )
     */
    public function show(string $parkingId, string $id)
    {
        $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

        if ($car->erro) {
            return $car;
        }

        return $this->outputResponse($car);
    }

    /**
     * @OA\Patch(
     *     path="/api/car/{parking_id}/{car_id}",
     *     summary="Atualizar carro por ID do estacionamento e ID do carro",
     *     tags={"Carros"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\Parameter(
     *         name="parking_id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="car_id",
     *         in="path",
     *         required=true,
     *         description="ID do carro",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/CarsDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/CarResource"
     *         )
     *     )
     * )
     */
    public function update(
        Request $request,
        string $parkingId,
        string $id
    )
    {
        if($this->verifiedRequest($request->all(), 4)){
            return $this->outputResponse(null);
        };

        if(!Parking::where('id', $request->id)->exists()){
            return $this->outputResponse(null, 'Estacionamento não existe!');
        };

        $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

        if ($car->erro) {
            return $car;
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

    /**
     * @OA\Patch(
     *     path="/api/car/output/{parking_id}/{car_id}",
     *     summary="Adicionar saida para por ID do estacionamento e ID do carro",
     *     tags={"Carros"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\Parameter(
     *         name="parking_id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="car_id",
     *         in="path",
     *         required=true,
     *         description="ID do carro",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/CarResource"
     *         )
     *     )
     * )
     */
    public function registersCarExit(string $parkingId, string $id)
    {
        $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

        if ($car->erro) {
            return $car;
        }

        $car->output = now();
        $car->save();

        $car = $this->cars::find($car['id']);

        return $this->outputResponse($car);
    }

    /**
     * @OA\Delete(
     *     path="/api/car/{parking_id}/{car_id}",
     *     summary="Deletar carro por ID do estacionamento e ID do funcionário",
     *
     *     tags={"Carros"},
     *
     *     @OA\Parameter(
     *         name="parking_id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="car_id",
     *         in="path",
     *         required=true,
     *         description="ID do carro",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="No content"
     *     )
     * )
     */
    public function destroy(string $parkingId, string $id)
    {
        $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

        if ($car->erro) {
            return $car;
        }

        $car->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse($car, $message = 'Registro não encontrado')
    {
        $error = [];

        if (is_null($car)) {
            $error = [
                'erro'    => true,
                'message' => $message,
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
            $error['message'] ?? null,
        );

        return new CarResource($outputDto);
    }

    private function getCarByParkingIdAndCarId($parkingId, $carId)
    {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $carId,
        ])->first();

        if (!$car) {
            return $this->outputResponse($car);
        }

        return $car;
    }

    private function verifiedRequest(array $request, int $numberOfParameters)
    {
        if(count($request) < $numberOfParameters){
            return true;
        }

        return false;
    }
}
