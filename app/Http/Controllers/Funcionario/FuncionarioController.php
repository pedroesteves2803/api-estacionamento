<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateFuncionarioRequest;
use App\Http\Resources\FuncionarioResource;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FuncionarioController extends Controller
{

    public function __construct(protected Funcionario $repository)
    {}

    public function index()
    {
        $funcionarios = $this->repository::all();

        return FuncionarioResource::collection($funcionarios);
    }

    public function create()
    {
        //
    }

    public function store(StoreUpdateFuncionarioRequest $request)
    {
        $data = $request->validated();

        $funcionario = $this->repository::create($data);

        return new FuncionarioResource($funcionario);
    }

    public function show(string $id)
    {
        $funcionarios = $this->repository::findOrFail($id);

        return new FuncionarioResource($funcionarios);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(StoreUpdateFuncionarioRequest $request, string $id)
    {
        $data = $request->validated();
        $funcionario = $this->repository::findOrFail($id);
        $funcionario->update($data);

        return new FuncionarioResource($funcionario);
    }

    public function destroy(string $id)
    {
        $funcionario = $this->repository::findOrFail($id);

        $funcionario->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
