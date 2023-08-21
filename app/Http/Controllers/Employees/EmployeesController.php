<?php

namespace App\Http\Controllers\Employees;

use App\Dtos\Employees\EmployeesDTO;
use App\Dtos\Employees\OutputEmployeesDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeesResource;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeesController extends Controller
{
    public function __construct(protected Employees $employees)
    {
    }

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

    public function store(Request $request)
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

        return $this->outputResponse($employee);
    }

    public function show(string $parkingId, string $id)
    {
        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

        return $this->outputResponse($employee);
    }

    public function update(Request $request, string $parkingId, string $id)
    {
        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

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

    public function destroy(string $parkingId, string $id)
    {
        $employee = $this->getEmployeeByParkingIdAndCarId($parkingId, $id);

        $employee->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse($employee)
    {
        $error = [];

        if (is_null($employee)) {
            $error = [
                'erro'    => true,
                'message' => 'Registro não encontrado',
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
            $error['message'] ?? '',
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
}
