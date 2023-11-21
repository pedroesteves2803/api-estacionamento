<?php

namespace App\Http\Controllers\Parking;

use App\Dtos\Parking\VacancyDTO;
use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\Vacancy;
use App\Services\Utils\UtilsRequestService;
use Exception;
use Illuminate\Http\Request;

class VacanciesController extends Controller
{
    public const NUMBER_OF_PARAMETERS = 3;

    public function __construct(
        protected Vacancy $vacancy,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $dto = $this->createVacancyDTO($request);

        $vacancy = $this->vacancy::create($dto->toArray());

        if (is_null($vacancy)) {
            throw new Exception('Não foi possível criar o estacionamento!');
        }
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

    private function createVacancyDTO(
        Request $request
    ): VacancyDTO {
        $fields = $request->only([
            'parking_id',
            'number',
            'available',
        ]);

        return new VacancyDTO(
            $fields['parking_id'],
            $fields['number'],
            $fields['available']
        );
    }
}
