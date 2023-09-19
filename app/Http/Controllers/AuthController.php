<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /** @SWG\Post(
     *     path="/api/auth/login",
     *     tags={"Login"},
     *     summary="Login işlemi",
     *     description="Login işlemi",
     *     @SWG\Parameter(
     *          name="email",
     *          description="User e-mail address",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *          name="password",
     *          description="User password",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="login is successful",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string"
     *             )
     *          )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */
    /** @SWG\Post(
     *     path="/api/auth/register",
     *     tags={"Register"},
     *     summary="Register işlemi",
     *     description="Register işlemi",
     *     @SWG\Parameter(
     *          name="email",
     *          description="User e-mail address",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Parameter(
     *          name="password",
     *          description="User password",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *      @SWG\Parameter(
     *          name="name",
     *          description="Name",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *      @SWG\Parameter(
     *          name="phone",
     *          description="Phone",
     *          required=true,
     *          type="string",
     *          in="query"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="register is successful",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="token",
     *                  type="string"
     *             )
     *          )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */
    /** @SWG\Post(
     *     path="/api/myProfile",
     *     tags={"Profil"},
     *     summary="Profil bilgisi",
     *     description="Profil bilgisi",
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile data",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="user",
     *                  type="integer"),
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */
    /** @SWG\Post(
     *     path="/api/logout",
     *     tags={"Logout"},
     *     @SWG\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          type="string",
     *          in="header"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="profile logout",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="Cikis yapildi."
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *     )
     * )
     */
    public function register(Request $request)
    {

        // return response()->json([
        //     'success' => true,
        //     'request' => $request], 201);

        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'phone' => 'required|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone
        ]);
        $user = $user->save();

        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Giriş Yapilamadi Girilen Bilgileri Kontrol Ediniz.'
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ], 201);
    }

    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();

            return response()->json(array('errors' => $errors));
        }
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Bilgiler hatalidir kontrol ediniz.'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personel Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Cikis yapildi.'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function authenticate(Request $request)
    {
        $user = [];
        if (Auth::check()) {
            $user = $request->user();
        }
        unset($user['email_verified_at']);
        return response()->json([
            'user' => $user,
            'isLoggedIn' => Auth::check(),

        ], 200);
    }
}
