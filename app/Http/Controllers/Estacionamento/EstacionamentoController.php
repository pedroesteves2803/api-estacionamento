<?php

namespace App\Http\Controllers\Estacionamento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateEstacionamentoRequest;
use App\Http\Resources\EstacionamentoResource;
use App\Models\Estacionamento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EstacionamentoController extends Controller
{

    public function __construct(protected Estacionamento $repository)
    {}

    public function index()
    {
        $estacionamentos = $this->repository::with('carros')->get();

        return EstacionamentoResource::collection($estacionamentos);
    }

    public function create()
    {
        //
    }


    public function store(StoreUpdateEstacionamentoRequest $request)
    {
        $data = $request->validated();

        $estacionamento = $this->repository::create($data);

        return new EstacionamentoResource($estacionamento);
    }


    public function show(string $id)
    {
        $estacionamento = $this->repository::with('carros')->findOrFail($id);

        return new EstacionamentoResource($estacionamento);
    }


    public function edit(string $id)
    {

    }

    public function update(StoreUpdateEstacionamentoRequest $request, string $id)
    {
        $data = $request->validated();
        $estacionamento = $this->repository::findOrFail($id);
        $estacionamento->update($data);

        return new EstacionamentoResource($estacionamento);
    }

    public function destroy(string $id)
    {
        $estacionamento = $this->repository::findOrFail($id);

        $estacionamento->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
