<?php

namespace App\Http\Controllers\Parking;

use App\Dtos\Vacancies\OutputVacancyDTO;
use App\Dtos\Vacancies\VacancyDTO;
use App\Dtos\Vacancies\VacancyUpdateDTO;
use App\Exceptions\FailureGetVacancyByParkingByIdException;
use App\Exceptions\FailureGetVacancyByParkingIdAndVacancyException;
use App\Exceptions\FailureUpdateVacancyException;
use App\Exceptions\Parking\FailureCreateParkingException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacanciesResource;
use App\Models\Parking;
use App\Models\Vacancy;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function show(
        string $parkingId,
        string $id
    ) {
        try {
            $vacancy = $this->getVacancyByParkingById($parkingId, $id);

            return $this->outputResponse($vacancy);
        } catch (FailureGetVacancyByParkingByIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    public function update(
        Request $request,
        string $parkingId,
        string $id
    ) {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $vacancy = $this->updateVacancy($request, $parkingId, $id);

            return $this->outputResponse($vacancy);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureUpdateVacancyException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureGetVacancyByParkingByIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    public function destroy(
        string $parkingId,
        string $id
    ): JsonResponse|VacanciesResource {
        try {
            $vacancy = $this->getVacancyByParkingById($parkingId, $id);

            $vacancy->delete();

            return response()->json([], Response::HTTP_NO_CONTENT);
        } catch (FailureGetVacancyByParkingIdAndVacancyException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    private function outputResponse(
        Vacancy|null $vacancy,
        string $message = 'Registro não encontrado'
    ): VacanciesResource {
        $error = [];

        if (is_null($vacancy)) {
            $error = [
                'error'   => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputVacancyDTO(
            $vacancy['id'] ?? null,
            $vacancy['parking_id'] ?? null,
            $vacancy['number'] ?? null,
            $vacancy['available'] ?? null,
            $vacancy['created_at'] ?? null,
            $error['error'] ?? false,
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

    private function createUpdateVacancyDTO(
        Request $request
    ): VacancyUpdateDTO {
        $fields = $request->only([
            'parking_id',
            'number',
            'available',
        ]);

        return new VacancyUpdateDTO(
            $fields['parking_id'],
            $fields['number'],
            $fields['available']
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

        $lastVacancy = $this->vacancy::where('parking_id', $dto->parking_id)->orderBy('id', 'desc')->first();

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

    private function getVacancyByParkingById(
        string $parkingId,
        int $vacancyId
    ): Vacancy|FailureGetVacancyByParkingByIdException {
        $vacancy = $this->vacancy::where([
            'parking_id' => $parkingId,
            'id'         => $vacancyId,
        ])->first();

        if (!$vacancy) {
            throw new FailureGetVacancyByParkingByIdException('Não foi possível localizar a vaga.');
        }

        return $vacancy;
    }

    private function updateVacancy(
        Request $request,
        string $parkingId,
        string $vacancyId
    ): Vacancy {
        $dto = $this->createUpdateVacancyDTO($request);

        $vacancy = $this->getVacancyByParkingById($parkingId, $vacancyId);

        $vacancy->update($dto->toArray());

        if (is_null($vacancy)) {
            throw new FailureUpdateVacancyException('Não foi possível atualizar a vaga.');
        }

        return $vacancy;
    }
}
