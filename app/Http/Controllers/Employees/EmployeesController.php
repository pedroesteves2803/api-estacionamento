<?php

namespace App\Http\Controllers\Employees;

use App\Dtos\Employees\EmployeesDTO;
use App\Dtos\Employees\OutputEmployeesDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeesResource;
use App\Models\Employees;
use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ParkingController.
 *
 * @OA\Tag(
 *     name="Funcionários",
 *     description="API endpoints de funcionários"
 * )
 */
class EmployeesController extends Controller
{
    public function __construct(protected Employees $employees)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/employees",
     *     summary="Buscar todas os funcionários",
     *     tags={"Funcionários"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/EmployeesResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $employees = $this->employees::all();

        $employeesDTOs = collect($employees)->map(function ($employee) {
            return new OutputEmployeesDTO(
                $employee->id,
                $employee->name,
                $employee->cpf,
                $employee->email,
                $employee->office,
                $employee->active,
                $employee->parking_id
            );
        })->all();

        return EmployeesResource::collection(
            collect($employeesDTOs)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/employees",
     *     summary="Criar novo funcionário",
     *     tags={"Funcionários"},
     *     security={{ "Autenticação": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/EmployeesDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/EmployeesResource"
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if ($this->verifiedRequest($request->all(), 6)) {
            return $this->outputResponse(null);
        }

        if(!Parking::where('id', $request->id)->exists()){
            return $this->outputResponse(null, 'Estacionamento não existe!');
        };

        $dto = new EmployeesDTO(
            ...$request->only([
                'name',
                'cpf',
                'email',
                'office',
                'active',
                'parking_id',
            ])
        );

        $employee = $this->employees::create($dto->toArray());

        return $this->outputResponse($employee);
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{parking_id}/{employees_id}",
     *     summary="Buscar funcionário por ID do estacionamento e ID do funcionário",
     *     tags={"Funcionários"},
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
     *         name="employees_id",
     *         in="path",
     *         required=true,
     *         description="ID do Funcionário",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/EmployeesResource"
     *         )
     *     )
     * )
     */
    public function show(string $parkingId, string $id)
    {
        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

        if ($employee->erro) {
            return $employee;
        }

        return $this->outputResponse($employee);
    }

    /**
     * @OA\Patch(
     *     path="/api/employees/{parking_id}/{employees_id}",
     *     summary="Atualizar funcionário por ID do estacionamento e ID do funcionário",
     *     tags={"Funcionários"},
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
     *         name="employees_id",
     *         in="path",
     *         required=true,
     *         description="ID do funcionário",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/EmployeesDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/EmployeesResource"
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $parkingId, string $id)
    {
        if ($this->verifiedRequest($request->all(), 6)) {
            return $this->outputResponse(null);
        }

        if(!Parking::where('id', $request->id)->exists()){
            return $this->outputResponse(null, 'Estacionamento não existe!');
        };

        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

        if ($employee->erro) {
            return $employee;
        }

        $dto = new EmployeesDTO(
            ...$request->only([
                'name',
                'cpf',
                'email',
                'office',
                'active',
                'parking_id',
            ])
        );

        $employee->update($dto->toArray());

        return $this->outputResponse($employee);
    }

    /**
     * @OA\Delete(
     *     path="/api/employees/{parking_id}/{employees_id}",
     *     summary="Deletar funcionário por ID do estacionamento e ID do funcionário",
     *     tags={"Funcionários"},
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
     *         name="employees_id",
     *         in="path",
     *         required=true,
     *         description="ID do funcionário",
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
        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

        if ($employee->erro) {
            return $employee;
        }

        $employee->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse($employee, $message = 'Registro não encontrado')
    {
        $error = [];

        if (is_null($employee)) {
            $error = [
                'erro'    => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputEmployeesDTO(
            $employee['id'] ?? null,
            $employee['name'] ?? null,
            $employee['cpf'] ?? null,
            $employee['email'] ?? null,
            $employee['office'] ?? null,
            $employee['active'] ?? null,
            $employee['parking_id'] ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? null,
        );

        return new EmployeesResource($outputDto);
    }

    private function getEmployeeByParkingIdAndCarId($parkingId, $employeeId)
    {
        $employee = $this->employees::where([
            'parking_id' => $parkingId,
            'id'         => $employeeId,
        ])->first();

        if (!$employee) {
            return $this->outputResponse($employee);
        }

        return $employee;
    }

    private function verifiedRequest(array $request, int $numberOfParameters)
    {
        if (count($request) < $numberOfParameters) {
            return true;
        }

        return false;
    }
}
