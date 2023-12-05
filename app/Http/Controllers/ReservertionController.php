<?php

namespace App\Http\Controllers;

use App\Dtos\Reservations\OutputReservationDTO;
use App\Dtos\Reservations\ReservationDTO;
use App\Exceptions\FailureCreateReservationException;
use App\Exceptions\FailureGetReservationByParkingIdAndReservationIdException;
use App\Exceptions\NoCarException;
use App\Exceptions\NoVacancyException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Resources\ReservationResource;
use App\Models\Car;
use App\Models\Parking;
use App\Models\Reservations;
use App\Models\Vacancy;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReservertionController extends Controller
{
    public const NUMBER_OF_PARAMETERS = 5;

    public function __construct(
        protected Reservations $reservations,
        protected UtilsRequestService $utilsRequestService
    ) {
    }


    public function index(
        string $parkingId
    )
    {
        $reservations = $this->reservations::where('parking_id', $parkingId)->get();

        $reservationsDTOs = $this->mapToOutputReservationsDTO($reservations);

        return ReservationResource::collection(
            collect($reservationsDTOs)
        );
    }


    public function store(
        Request $request
    ): ReservationResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkCarExistence($request->car_id);

            $this->checkVacancyExistence($request->vacancy_id);

            $this->checkParkingExistence($request->parking_id);

            $reservation = $this->createReservation($request);

            return $this->outputResponse($reservation);
        } catch (NoParkingException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (NoCarException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (NoVacancyException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureCreateReservationException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(
        string $parkingId,
        string $id
    ): ReservationResource{
        try {
            $reservation = $this->getReservationByParkingIdAndCarId($parkingId, $id);

            return $this->outputResponse($reservation);
        } catch (FailureGetReservationByParkingIdAndReservationIdException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }

    private function mapToOutputReservationsDTO(
        Collection $reservations
    ): array {
        return $reservations->map(function ($reservation) {
            return OutputReservationDTO::fromModel($reservation);
        })->all();
    }

    private function outputResponse(
        Reservations|null $reservation,
        string $message = 'Registro não encontrado'
    ): ReservationResource {
        $error = [];

        if (is_null($reservation)) {
            $error = [
                'error'   => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputReservationDTO(
            $reservation['id'] ?? null,
            $reservation['parking_id'] ?? null,
            $reservation['vacancy_id'] ?? null,
            $reservation['car_id'] ?? null,
            $reservation['start_date'] ?? null,
            $reservation['end_date'] ?? null,
            $reservation['status'] ?? null,
            $error['error'] ?? false,
            $error['message'] ?? null,
        );

        return new ReservationResource($outputDto);
    }

    private function checkParkingExistence(
        string $parkingId
    ) {
        if (!Parking::where('id', $parkingId)->exists()) {
            throw new NoParkingException('Estacionamento não existe!');
        }
    }

    private function checkCarExistence(
        string $carId
    ) {
        if (!Car::where('id', $carId)->exists()) {
            throw new NoCarException('Carro não existe!');
        }
    }

    private function checkVacancyExistence(
        string $vacancyId
    ) {
        if (!Vacancy::where('id', $vacancyId)->exists()) {
            throw new NoVacancyException('Vaga não existe!');
        }
    }

    private function createReservationDTO(
        Request $request
    ): ReservationDTO {
        $fields = $request->only([
            'parking_id',
            'vacancy_id',
            'car_id',
            'start_date',
            'end_date',
            'status',
        ]);

        return new ReservationDTO(
            $fields['parking_id'],
            $fields['vacancy_id'],
            $fields['car_id'],
            $fields['start_date'],
            $fields['end_date'] ?? null,
            $fields['status']
        );
    }

    private function createReservation(
        Request $request
    ): Reservations|FailureCreateReservationException {
        $dto = $this->createReservationDTO($request);

        $reservation = $this->reservations::create($dto->toArray());

        if (is_null($reservation)) {
            throw new FailureCreateReservationException('Não foi possível criar a reserva.');
        }

        $reservation = $this->reservations::find($reservation['id']);

        return $reservation;
    }

    private function getReservationByParkingIdAndCarId(
        int $parkingId,
        int $carId
    ): Reservations|FailureGetReservationByParkingIdAndReservationIdException {
        $reservation = $this->reservations::where([
            'parking_id' => $parkingId,
            'id'         => $carId,
        ])->first();

        if (is_null($reservation)) {
            throw new FailureGetReservationByParkingIdAndReservationIdException('Não foi possível localizar a reserva.');
        }

        return $reservation;
    }
}
