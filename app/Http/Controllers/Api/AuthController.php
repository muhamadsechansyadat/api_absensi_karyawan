<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
    * @OA\Post(
    * path="/api/auth/login",
    * tags={"Auth"},
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"email", "password"},
    *               @OA\Property(property="email", type="text"),
    *               @OA\Property(property="password", type="password"),
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Login Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
    * @OA\Post(
    * path="/api/auth/register",
    * tags={"Auth"},
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"name","email", "password", "password_confirmation"},
    *               @OA\Property(property="name", type="text"),
    *               @OA\Property(property="email", type="text"),
    *               @OA\Property(property="password", type="password"),
    *               @OA\Property(property="password_confirmation", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'res' => true,
            'message' => 'User successfully registered',
            'data' => $user
        ], 201);
    }

    /**
    * @OA\Post(
    * path="/api/auth/logout",
    * tags={"Auth"},
    * security={{"bearer_token":{}}},
    *      @OA\Response(
    *          response=201,
    *          description="Logout Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Logout Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function logout() {
        auth()->logout();
        return response()->json([
            'res' => true,
            'message' => 'User successfully signed out'
        ]);
    }

    /**
    * @OA\Post(
    * path="/api/auth/refresh",
    * tags={"Auth"},
    * security={{"bearer_token":{}}},
    *      @OA\Response(
    *          response=201,
    *          description="Refresh Token Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Refresh Token Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
    * @OA\Get(
    * path="/api/auth/user-profile",
    * tags={"Auth"},
    * security={{"bearer_token":{}}},
    *      @OA\Response(
    *          response=201,
    *          description="Get Profile Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Get Profile Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function userProfile() {
        return response()->json([
            'res' => true,
            'data' => auth()->user()
        ]);
    }

    protected function createNewToken($token){
        return response()->json([
            'res' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
