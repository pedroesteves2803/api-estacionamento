<?php

namespace App\Http\Controllers\Employees;

use App\Dtos\Employees\EmployeesDTO;
use App\Dtos\Employees\OutputEmployeesDTO;
use App\Dtos\Error\ErrorDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeesResource;
use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmployeesController extends Controller
{

    public function __construct(protected Employees $employees)
    {}

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
                'parking_id'
            ])
        );

        $employee = $this->employees::create($dto->toArray());

        return $this->outputResponse($employee);
    }

    public function show(string $parkingId, string $id)
    {
        $employee = $this->employees::find($id);

        $employee = $this->employees::where([
            'parking_id' => $parkingId,
            'id' => $id
        ])->first();

        if(empty($employee)){
            return $this->outputErrorResponse($id);
        }

        return $this->outputResponse($employee);
    }

    public function update(Request $request, string $id)
    {
        $employee = $this->employees::find($id);

        if(empty($employee)){
            return $this->outputErrorResponse($id);
        }

        $dto = new EmployeesDTO(
            ...$request->only([
                'name',
                'cpf',
                'email',
                'office',
                'active',
                'parking_id'
            ])
        );

        $employee->update($dto->toArray());

        return $this->outputResponse($employee);
    }

    public function destroy(string $parkingId, string $id)
    {
        $employee = $this->employees::where([
            'parking_id' => $parkingId,
            'id' => $id
        ])->first();

        if(empty($employeemployeees)){
            return $this->outputErrorResponse($id);
        }

        $employee->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse(Employees $employee){

        $outputDto =  new OutputEmployeesDTO(
            $employee['id'],
            $employee['name'],
            $employee['cpf'],
            $employee['email'],
            $employee['office'],
            $employee['active'],
            $employee['parking_id']
        );

        return new EmployeesResource($outputDto);
    }

    private function outputErrorResponse(int $id){
        $error = new ErrorDTO("Registro {$id} não encontrado", Response::HTTP_NOT_FOUND);
        return response()->json($error->toArray(), Response::HTTP_NOT_FOUND);
    }
}
