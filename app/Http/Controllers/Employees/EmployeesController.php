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

    public function __construct(protected Employees $repository)
    {}

    public function index()
    {
        $employees = $this->repository::all();

        return EmployeesResource::collection($employees);
    }

    public function create()
    {
        //
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

        $employees = $this->repository::create($dto->toArray());
        $employees = $this->repository::find($employees->id);

        return $this->outputResponse($employees);
    }

    public function show(string $id)
    {
        $employees = $this->repository::find($id);

        if(empty($employees)){
            return $this->outputErrorResponse($id);
        }

        return new EmployeesResource($employees);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $employees = $this->repository::find($id);

        if(empty($employees)){
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

        $employees->update($dto->toArray());

        return new EmployeesResource($employees);
    }

    public function destroy(string $id)
    {
        $employees = $this->repository::find($id);

        if(empty($employees)){
            return $this->outputErrorResponse($id);
        }

        $employees->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    private function outputResponse(Employees $employees){

        $output =  new OutputEmployeesDTO(
            $employees['id'],
            $employees['name'],
            $employees['cpf'],
            $employees['email'],
            $employees['office'],
            $employees['active'],
            $employees['parking_id'],
        );

        return response()->json($output->toArray());
    }

    private function outputErrorResponse(int $id){
        $error = new ErrorDTO("Registro {$id} não encontrado", Response::HTTP_NOT_FOUND);
        return response()->json($error->toArray(), Response::HTTP_NOT_FOUND);
    }
}
