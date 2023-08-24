<?php

namespace App\Http\Controllers\Parking;

use App\Dtos\Parking\OutputParkingDTO;
use App\Dtos\Parking\ParkingDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ParkingController.
 *
 * @OA\Tag(
 *     name="Estacionamento",
 *     description="API endpoints de estacionamentos"
 * )
 */
class ParkingController extends Controller
{
    public function __construct(protected Parking $parking)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/parking",
     *     summary="Buscar todas as informações",
     *     tags={"Estacionamento"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/ParkingResource")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/parking",
     *     summary="Criar novo estacionamento",
     *     tags={"Estacionamento"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/ParkingDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ParkingResource"
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/parking/{id}",
     *     summary="Buscar estacionamento pelo id",
     *     tags={"Estacionamento"},
     *
     *     @OA\Parameter(
     *         name="id",
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
     *             ref="#/components/schemas/ParkingResource"
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $parking = $this->getParkingById($id);

        if ($parking->erro) {
            return $parking;
        }

        return $this->outputResponse($parking);
    }

    /**
     * @OA\Patch(
     *     path="/api/parking/{id}",
     *     summary="Atualizar estacionamento pelo id",
     *     tags={"Estacionamento"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/ParkingDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ParkingResource"
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $parking = $this->getParkingById($id);

        if ($parking->erro) {
            return $parking;
        }

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

    /**
     * @OA\Delete(
     *     path="/api/parking/{id}",
     *     summary="Deletar por ID",
     *     tags={"Estacionamento"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do estacionamento",
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
    public function destroy(string $id)
    {
        $parking = $this->getParkingById($id);

        if ($parking->erro) {
            return $parking;
        }

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
