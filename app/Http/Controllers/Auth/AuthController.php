<?php

namespace App\Http\Controllers\Auth;

use App\Dtos\Auth\AuthDTO;
use App\Dtos\Auth\OutputAuthDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\SecurityScheme(
 *     name="Autenticação",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
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
    public function auth(Request $request)
    {
        $requestData = $request->only([
            'email',
            'password',
            'device_name',
        ]);

        $authDTO = new AuthDTO(...$requestData);

        $user = User::where('email', $authDTO->email)->first();

        if (!$user) {
            return $this->outputResponse(null);
        }

        $user->tokens()->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return $this->outputResponse($token);
    }

    private function outputResponse($token)
    {
        $error = [];

        if (is_null($token)) {
            $error = [
                'erro'    => true,
                'message' => 'Registro não encontrado',
            ];
        }

        $outputDto = new OutputAuthDTO(
            $token ?? null,
            $error['erro'] ?? false,
            $error['message'] ?? '',
        );

        return new AuthResource($outputDto);
    }
}
