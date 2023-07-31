<?php

namespace App\Http\Controllers\Carro;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCarroRequest;
use App\Http\Resources\CarroResource;
use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CarroController extends Controller
{

    public function __construct(protected Carro $repository)
    {}

    public function index()
    {
        $carros = $this->repository::all();

        return CarroResource::collection($carros);
    }

    public function create()
    {
        //
    }

    public function store(StoreUpdateCarroRequest $request)
    {
        $data = $request->validated();

        $data['entrada'] = now();

        $carro = $this->repository::create($data);

        return new CarroResource($carro);
    }

    public function show(string $id)
    {
        $carro = $this->repository::findOrFail($id);

        return new CarroResource($carro);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(StoreUpdateCarroRequest $request, string $id)
    {
        $data = $request->validated();
        $carro = $this->repository::findOrFail($id);
        $carro->update($data);

        return new CarroResource($carro);
    }

    public function registersCarExit(string $id)
    {
        $carro = $this->repository::findOrFail($id);
        $carro->saida = now();
        $carro->save();

        return new CarroResource($carro);
    }

    public function destroy(string $id)
    {
        $carro = $this->repository::findOrFail($id);

        $carro->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}

