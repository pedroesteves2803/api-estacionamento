<?php

namespace App\Http\Controllers\Cars;

use App\Dtos\Cars\CarsDTO;
use App\Dtos\Cars\OutputCarsDTO;
use App\Exceptions\Car\FailureCreateCarException;
use App\Exceptions\Car\FailureExitCarException;
use App\Exceptions\Car\FailureGetCarByParkingIdAndCarIdCarException;
use App\Exceptions\Car\FailureUpdateCarException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Models\Parking;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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
    public const NUMBER_OF_PARAMETERS = 4;

    public function __construct(
        protected Car $cars,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/car/{parking_id}",
     *     summary="Buscar carros por ID do estacionamento",
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
    public function index(
        string $parkingId
    ) {
        $cars = $this->cars::where('parking_id', $parkingId)->get();

        $carsDTOs = $this->mapToOutputCarsDTO($cars);

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
    public function store(
        Request $request
    ): CarResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $car = $this->createCar($request);

            return $this->outputResponse($car);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureCreateCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function show(
        string $parkingId,
        string $id
    ): CarResource {
        try {
            $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

            return $this->outputResponse($car);
        } catch (FailureGetCarByParkingIdAndCarIdCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    ): CarResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $car = $this->updateCar($request, $parkingId, $id);

            return $this->outputResponse($car);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureUpdateCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureGetCarByParkingIdAndCarIdCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function registersCarExit(
        string $parkingId,
        string $id
    ): CarResource {
        try {
            $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

            if ($car->erro) {
                return $car;
            }

            $this->exitCar($car);

            return $this->outputResponse($car);
        } catch (FailureGetCarByParkingIdAndCarIdCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureExitCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function destroy(
        string $parkingId,
        string $id
    ): JsonResponse|CarResource {
        try {
            $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

            $car->delete();

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (FailureGetCarByParkingIdAndCarIdCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    private function outputResponse(
        Car|null $car,
        string $message = 'Registro não encontrado'
    ): CarResource {
        $error = [];

        if (is_null($car)) {
            $error = [
                'error'   => true,
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
            $error['error'] ?? false,
            $error['message'] ?? null,
        );

        return new CarResource($outputDto);
    }

    private function getCarByParkingIdAndCarId(
        int $parkingId,
        int $carId
    ): Car|FailureGetCarByParkingIdAndCarIdCarException {
        $car = $this->cars::where([
            'parking_id' => $parkingId,
            'id'         => $carId,
        ])->first();

        if (is_null($car)) {
            throw new FailureGetCarByParkingIdAndCarIdCarException('Não foi possível localizar o carro.');
        }

        return $car;
    }

    private function mapToOutputCarsDTO(
        Collection $cars
    ): array {
        return $cars->map(function ($car) {
            return OutputCarsDTO::fromModel($car);
        })->all();
    }

    private function createCar(
        Request $request
    ): Car|FailureCreateCarException {
        $dto = $this->createCarDTO($request);

        $dto->toArray()['input'] = now();

        $car = $this->cars::create($dto->toArray());

        if (is_null($car)) {
            throw new FailureCreateCarException('Não foi possível criar o carro.');
        }

        $car = $this->cars::find($car['id']);

        return $car;
    }

    private function updateCar(
        Request $request,
        string $parkingId,
        string $id
    ): Car|FailureUpdateCarException {
        $dto = $this->createCarDTO($request);

        $car = $this->getCarByParkingIdAndCarId($parkingId, $id);

        $car->update($dto->toArray());

        if (is_null($car)) {
            throw new FailureUpdateCarException('Não foi possível atualizar o carro.');
        }

        return $car;
    }

    private function exitCar(
        Car $car
    ): Car {
        $car->output = now();
        $car->save();

        if (is_null($car)) {
            throw new FailureExitCarException('Estacionamento não existe!');
        }

        return $this->cars::find($car['id']);
    }

    private function checkParkingExistence(
        string $parkingId
    ) {
        if (!Parking::where('id', $parkingId)->exists()) {
            throw new NoParkingException('Estacionamento não existe!');
        }
    }

    private function createCarDTO(
        Request $request
    ): CarsDTO {
        $fields = $request->only([
            'plate',
            'model',
            'color',
            'parking_id',
        ]);

        return new CarsDTO(
            $fields['plate'],
            $fields['model'],
            $fields['color'],
            $fields['parking_id']
        );
    }
}
