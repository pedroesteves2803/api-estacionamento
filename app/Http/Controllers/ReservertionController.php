<?php

namespace App\Http\Controllers;

use App\Dtos\Reservations\OutputReservationDTO;
use App\Dtos\Reservations\ReservationDTO;
use App\Exceptions\FailureCreateReservationException;
use App\Exceptions\Parking\NoParkingException;
use App\Exceptions\RequestFailureException;
use App\Http\Resources\ReservationResource;
use App\Models\Parking;
use App\Models\Reservations;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Http\Request;

class ReservertionController extends Controller
{
    public const NUMBER_OF_PARAMETERS = 5;

    public function __construct(
        protected Reservations $reservations,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $this->checkParkingExistence($request->parking_id);

            $reservation = $this->createReservation($request);

            return $this->outputResponse($reservation);
        } catch (NoParkingException $e) {
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
    public function show(string $id)
    {
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

    private function outputResponse(
        Reservations|null $car,
        string $message = 'Registro não encontrado'
    ): ReservationResource {
        $error = [];

        if (is_null($car)) {
            $error = [
                'error'   => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputReservationDTO(
            $car['id'] ?? null,
            $car['parking_id'] ?? null,
            $car['vacancy_id'] ?? null,
            $car['car_id'] ?? null,
            $car['start_date'] ?? null,
            $car['end_date'] ?? null,
            $car['status'] ?? null,
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
}
