<?php

namespace App\Http\Controllers\Employees;

use App\Dtos\Employees\EmployeesDTO;
use App\Dtos\Employees\OutputEmployeesDTO;
use App\Exceptions\Employees\FailureCreateEmployeesException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeesResource;
use App\Models\Employees;
use App\Models\Parking;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
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
    const NUMBER_OF_PARAMETERS = 6;

    public function __construct(
        protected Employees $employees,
        protected UtilsRequestService $utilsRequestService
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/api/employees",
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
    )
    {
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
    public function store(Request $request)
    {
        try{

        $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

        $this->checkParkingExistence($request->parking_id);

        $employee = $this->createEmployee($request);

        return $this->outputResponse($employee);

        }catch(NoParkingException $e){
            return $this->outputResponse(null, $e->getMessage());
        }catch(RequestFailureException $e){
            return $this->outputResponse(null, $e->getMessage());
        }catch(FailureCreateEmployeesException $e){
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
        if ($this->utilsRequestService->verifiedRequest($request->all(), 6)) {
            return $this->outputResponse(null);
        }

        if(!Parking::where('id', $request->parking_id)->exists()){
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

    private function mapToOutputEmployeesDTO(
        Collection $employees
    ) : array
    {

        return $employees->map(function ($employee) {
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
    }

    private function checkParkingExistence(
        string $parkingId
    )
    {
        if (!Parking::where('id', $parkingId)->exists()) {
            throw new NoParkingException('Estacionamento não existe!');
        }
    }

    private function createEmployee(
        Request $request
    ) : Employees | FailureCreateEmployeesException
    {
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

        if (is_null($employee)) {
            throw new FailureCreateEmployeesException('Não foi possível criar o funcionário.');
        }

        $car = $this->employees::find($employee['id']);

        return $car;
    }
}
