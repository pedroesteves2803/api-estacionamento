<?php

namespace App\Http\Controllers\Parking;

use App\Dtos\Vacancies\OutputVacancyDTO;
use App\Dtos\Vacancies\VacancyDTO;
use App\Exceptions\Parking\FailureCreateParkingException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacanciesResource;
use App\Models\Parking;
use App\Models\Vacancy;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class VacanciesController extends Controller
{
    public const NUMBER_OF_PARAMETERS = 2;

    public function __construct(
        protected Vacancy $vacancy,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    public function index(
        int $parkingId
    ) {
        $vacancies = $this->vacancy::where('parking_id', $parkingId)->get();

        $vacanciesDTOs = $this->mapToOutputVacanciesDTOs($vacancies);

        return VacanciesResource::collection(
            collect($vacanciesDTOs)
        );
    }

    public function store(Request $request)
    {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $vacancies = $this->createVacancy($request);

            $vacanciesDTOs = $this->mapToOutputVacanciesDTOs($vacancies);

            return VacanciesResource::collection(
                collect($vacanciesDTOs)
            );
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureCreateParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    public function show(string $id)
    {
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }

    private function outputResponse(
        Vacancy|null $vacancy,
        string $message = 'Registro não encontrado'
    ): VacanciesResource {
        $error = [];

        if (is_null($vacancy)) {
            $error = [
                'erro'    => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputVacancyDTO(
            $vacancy['id'] ?? null,
            $vacancy['parking_id'] ?? null,
            $vacancy['number'] ?? null,
            $vacancy['available'] ?? null,
            $vacancy['created_at'] ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? null
        );

        return new VacanciesResource($outputDto);
    }

    private function createVacancyDTO(
        Request $request
    ): VacancyDTO {
        $fields = $request->only([
            'number_of_vacancies',
            'parking_id',
        ]);

        return new VacancyDTO(
            $fields['number_of_vacancies'],
            $fields['parking_id']
        );
    }

    private function mapToOutputVacanciesDTOs(
        $vacancies
    ): array {
        return $vacancies->map(function ($vacancie) {
            return OutputVacancyDTO::fromModel($vacancie);
        })->all();
    }

    private function createVacancy(
        Request $request
    ): Collection {
        $dto = $this->createVacancyDTO($request);

        $lastVacancy = $this->vacancy::orderBy('id', 'desc')->first();

        $counter = !empty($lastVacancy) ? $lastVacancy->number : 0;

        $limit = $counter + $dto->number_of_vacancies;

        for ($i = $counter; $i < $limit; ++$i) {
            $body = [
                'parking_id' => $dto->parking_id,
                'number'     => $i + 1,
                'available'  => false,
            ];

            $this->vacancy::create($body);
        }

        $newVacancies = $this->vacancy::where('id', '>', $counter)->get();

        if (empty($newVacancies)) {
            throw new FailureCreateParkingException('Não foi possível criar o vagas!');
        }

        return $newVacancies;
    }

    private function checkParkingExistence(
        string $parkingId
    ) {
        if (!Parking::where('id', $parkingId)->exists()) {
            throw new NoParkingException('Estacionamento não existe!');
        }
    }
}
