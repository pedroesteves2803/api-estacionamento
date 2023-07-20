<?php

namespace App\Http\Controllers\Estacaionamento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateEstacionamentoRequest;
use App\Http\Resources\EstacionamentoResource;
use App\Models\Estacionamento;
use Illuminate\Http\Request;

class EstacionamentoController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $estacionamentos = Estacionamento::all();

        return EstacionamentoResource::collection($estacionamentos);
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
    public function store(StoreUpdateEstacionamentoRequest $request)
    {
        $data = $request->validated();

        $estacionamento = Estacionamento::create($data);

        return new EstacionamentoResource($estacionamento);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
