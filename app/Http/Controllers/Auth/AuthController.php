<?php

namespace App\Http\Controllers\Auth;

use App\Dtos\Auth\AuthDTO;
use App\Dtos\Auth\OutputAuthDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\SecurityScheme(
 *     name="Autenticação",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="API token",
 *     securityScheme="Autenticação"
 *
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar na API",
     *     tags={"Autenticação"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/AuthDTO")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Resposta bem sucedida",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/AuthResource"
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $requestData = $request->only([
            'email',
            'password',
        ]);

        $authDTO = new AuthDTO(...$requestData);

        if (!Auth::attempt($authDTO->toArray())) {
            return $this->outputResponse(null);
        }

        $user = Auth::user();

        $user->tokens()->delete();

        $token = $user->createToken($user->device_name)->plainTextToken;

        return $this->outputResponse($token);
    }

    private function outputResponse($token, $message = 'Usuário não encontrado!')
    {
        $error = [];

        if (is_null($token)) {
            $error = [
                'error'   => true,
                'message' => $message,
            ];
        }

        $outputDto = new OutputAuthDTO(
            $token ?? null,
            $error['error'] ?? false,
            $error['message'] ?? null,
        );

        return new AuthResource($outputDto);
    }
}
