<?php

namespace App\Http\Controllers\Employees;

use App\Dtos\Employees\EmployeesDTO;
use App\Dtos\Employees\OutputEmployeesDTO;
use App\Exceptions\Employees\FailureCreateEmployeesException;
use App\Exceptions\Employees\FailureGetEmployeeByParkingIdAndEmployeeIdException;
use App\Exceptions\Employees\FailureUpdateEmployeesException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeesResource;
use App\Models\Employees;
use App\Models\Parking;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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
    public const NUMBER_OF_PARAMETERS = 6;

    public function __construct(
        protected Employees $employees,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/employees/{parking_id}",
     *     summary="Buscar todas os funcionários",
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
    public function index(
        string $parkingId
    ) {
        $employees = $this->employees::where('parking_id', $parkingId)->get();

        $employeesDTOs = $this->mapToOutputEmployeesDTO($employees);

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
    public function store(
        Request $request
    ): EmployeesResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $employee = $this->createEmployee($request);

            return $this->outputResponse($employee);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureCreateEmployeesException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function show(
        string $parkingId,
        string $id
    ): EmployeesResource {
        try {
            $employee = $this->getEmployeeByParkingIdAndEmployeeId($parkingId, $id);

            if ($employee->erro) {
                return $employee;
            }

            return $this->outputResponse($employee);
        } catch (FailureGetEmployeeByParkingIdAndEmployeeIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function update(
        Request $request,
        string $parkingId,
        string $id
    ): EmployeesResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $employee = $this->updateEmployee($request, $parkingId, $id);

            return $this->outputResponse($employee);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureUpdateEmployeesException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureGetEmployeeByParkingIdAndEmployeeIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
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
    public function destroy(
        string $parkingId,
        string $id
    ): JsonResponse|EmployeesResource {
        try {
            $employee = $this->getEmployeeByParkingIdAndEmployeeId($parkingId, $id);

            if ($employee->erro) {
                return $employee;
            }

            $employee->delete();

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (FailureGetEmployeeByParkingIdAndEmployeeIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    private function outputResponse(
        Employees|null $employee,
        string $message = 'Registro não encontrado'
    ): EmployeesResource {
        $error = [];

        if (is_null($employee)) {
            $error = [
                'error'   => true,
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
            $error['error'] ?? false,
            $error['message'] ?? null,
        );

        return new EmployeesResource($outputDto);
    }

    private function getEmployeeByParkingIdAndEmployeeId(
        int $parkingId,
        int $employeeId
    ): Employees|FailureGetEmployeeByParkingIdAndEmployeeIdException {
        $employee = $this->employees::where([
            'parking_id' => $parkingId,
            'id'         => $employeeId,
        ])->first();

        if (is_null($employee)) {
            throw new FailureGetEmployeeByParkingIdAndEmployeeIdException('Não foi possível localizar o funcionário.');
        }

        return $employee;
    }

    private function mapToOutputEmployeesDTO(
        Collection $employees
    ): array {
        return $employees->map(function ($employee) {
            return OutputEmployeesDTO::fromModel($employee);
        })->all();
    }

    private function checkParkingExistence(
        string $parkingId
    ) {
        if (!Parking::where('id', $parkingId)->exists()) {
            throw new NoParkingException('Estacionamento não existe!');
        }
    }

    private function createEmployee(
        Request $request
    ): Employees|FailureCreateEmployeesException {
        $dto = $this->createEmployeeDTO($request);

        $employee = $this->employees::create($dto->toArray());

        if (is_null($employee)) {
            throw new FailureCreateEmployeesException('Não foi possível criar o funcionário.');
        }

        $car = $this->employees::find($employee['id']);

        return $car;
    }

    private function updateEmployee(
        Request $request,
        string $parkingId,
        string $id
    ): Employees|FailureUpdateEmployeesException {
        $dto = $this->createEmployeeDTO($request);

        $employee = $this->getEmployeeByParkingIdAndEmployeeId($parkingId, $id);

        $employee->update($dto->toArray());

        if (is_null($employee)) {
            throw new FailureUpdateEmployeesException('Não foi possível atualizar o funcionário.');
        }

        return $employee;
    }

    private function createEmployeeDTO(
        Request $request
    ): EmployeesDTO {
        $fields = $request->only([
            'name',
            'cpf',
            'email',
            'office',
            'active',
            'parking_id',
        ]);

        return new EmployeesDTO(
            $fields['name'],
            $fields['cpf'],
            $fields['email'],
            $fields['office'],
            $fields['active'],
            $fields['parking_id']
        );
    }
}
