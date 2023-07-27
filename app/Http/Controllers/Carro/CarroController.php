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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateCarroRequest $request)
    {
        $data = $request->validated();

        $data['entrada'] = now();

        $carro = $this->repository::create($data);

        return new CarroResource($carro);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $carro = $this->repository::findOrFail($id);

        return new CarroResource($carro);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateCarroRequest $request, string $id)
    {
        $data = $request->validated();
        $carro = $this->repository::findOrFail($id);
        $carro->update($data);

        return new CarroResource($carro);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $carro = $this->repository::findOrFail($id);

        $carro->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}

