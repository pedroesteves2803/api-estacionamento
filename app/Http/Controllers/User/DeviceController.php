<?php

namespace App\Http\Controllers\User;

use App\Dtos\Register\DeviceDTO;
use App\Dtos\Register\OutputDeviceDTO;
use App\Exceptions\Register\FailureCreateUserException;
use App\Exceptions\RequestFailureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeviceResource;
use App\Models\User;
use App\Services\Utils\UtilsRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class DeviceController.
 *
 * @OA\Tag(
 *     name="Registro",
 *     description="API endpoints de dispositivo"
 * )
 */
class DeviceController extends Controller
{
    public const NUMBER_OF_PARAMETERS = 4;
    public const WELCOME = 'Bem vindo!!';

    public function __construct(
        protected User $user,
        protected UtilsRequestService $utilsRequestService
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/register/device",
     *     summary="Criar novo dispositivo",
     *     tags={"Registro"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/DeviceDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/DeviceResource"
     *         )
     *     )
     * )
     */
    public function store(
        Request $request
    ): DeviceResource {
        try {
            $this->utilsRequestService->verifiedRequest($request->all(), self::NUMBER_OF_PARAMETERS);

            $user = $this->createUser($request);

            return $this->outputResponse($user);
        } catch (RequestFailureException $e) {
            return $this->outputResponse(null, $e->getMessage());
        } catch (FailureCreateUserException $e) {
            return $this->outputResponse(null, $e->getMessage());
        }
    }

    private function createRegisterDTO(
        Request $request
    ): DeviceDTO {
        $fields = $request->only([
            'device_name',
            'email',
            'password',
            'password_confirmation',
        ]);

        return new DeviceDTO(
            $fields['device_name'],
            $fields['email'],
            $fields['password'],
            $fields['password_confirmation']
        );
    }

    private function createUser(
        Request $request
    ): User|FailureCreateUserException {
        $dto = $this->createRegisterDTO($request);

        $user = $this->user::create([
            'device_name' => $dto->device_name,
            'email'       => $dto->email,
            'password'    => Hash::make($dto->password),
        ]);

        if (is_null($user)) {
            throw new FailureCreateUserException('Não foi possível criar o usuário.');
        }

        return $user;
    }

    private function outputResponse(
        User|null $user,
        string $message = 'Registro não encontrado'
    ): DeviceResource {
        $error = [];

        if (is_null($user)) {
            $error = [
                'erro'    => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputDeviceDTO(
            $user['id'] ?? null,
            $user['device_name'] ?? null,
            $user['email'] ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? self::WELCOME,
        );

        return new DeviceResource($outputDto);
    }
}
