<?php

namespace App\Http\Controllers\Auth\JWT;


use App\Mail\SendResetPasswordOTPMail;
use App\ResetPasswordOTP;
use App\ResetPasswordOTPVerification;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Helpers\Validators;
use App\Http\Helpers\Generators;
use App\Http\Helpers\Constants;
use Illuminate\Support\Facades\Validator;

class JwtAuthController extends Controller
{

    use Validators;
    use Generators;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:api_jwt")
            ->except([
                "login",
                "sendResetPasswordOTP",
                "verifyOTP",
                "resetPassword"
            ]);
    }


    /**
     * Get a JWT via given credentials.
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/login",
     *   tags={"Auth"},
     *   summary="JWT login",
     *   description="Login a user and generate JWT token",
     *   operationId="jwtLogin",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="User email",
     *                   type="string",
     *                   example="ihamzehald@gmail.com"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="User password",
     *                   type="string",
     *                   example="larapoints123"
     *               ),
     *           )
     *       )
     *   ),
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         description="JWT access token"
     *                     ),
     *                     @OA\Property(
     *                         property="token_type",
     *                         type="string",
     *                         description="Token type"
     *                     ),
     *                     @OA\Property(
     *                         property="expires_in",
     *                         type="integer",
     *                         description="Token expiration in miliseconds",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                         "token_type": "bearer",
     *                         "expires_in": 3600
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     * )
     */

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth("api_jwt")->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }



    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/logout",
     *   tags={"Auth"},
     *   summary="Log the user out",
     *   description="Log the user out (Invalidate the token)",
     *   operationId="jwtLogout",
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                        @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Successfully logged out",
     *                     ),
     *                      )
     *              )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     *  security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
    public function logout()
    {
        auth("api_jwt")->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/refresh",
     *   tags={"Auth"},
     *   summary="Refresh a JWT token",
     *   description="Referesh a JWT token based on the passed JWT token in the header",
     *   operationId="jwtRefresh",
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         description="JWT access token"
     *                     ),
     *                     @OA\Property(
     *                         property="token_type",
     *                         type="string",
     *                         description="Token type"
     *                     ),
     *                     @OA\Property(
     *                         property="expires_in",
     *                         type="integer",
     *                         description="Token expiration in miliseconds",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                         "token_type": "bearer",
     *                         "expires_in": 3600
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     *   security={
     *         {"bearerJWTAuth": {}}
     *     }
     *
     * )
     */

    public function refresh()
    {
        return $this->respondWithToken(auth("api_jwt")->refresh());
    }

    /**
     * Send OTP code to a user email
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\ParameterBag
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/password/request/reset",
     *   tags={"Auth"},
     *   summary="Request reset password",
     *   description="Send OTP code to user email",
     *   operationId="jwtPasswordRequestReset",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="User email",
     *                   type="string",
     *                   example="ihamzehald@gmail.com"
     *               )
     *           )
     *       )
     *   ),
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                           type="array",
     *                         @OA\Items()
     *                         ),
     *                         description="data wraper"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response nessage"
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     * )
     */

    public function sendResetPasswordOTP(Request $request)
    {
        $this->isEmailValid($request);

        $isUniqueToken = false;
        $user = User::where('email', $request->get('email'))->first();
        $uniqueOTP = null;

        if ($user) {

            /**
             * Set all old OPT as expired for this user
             */

            ResetPasswordOTP::where('user_id', $user->id)
                ->update(["status" => Constants::RESET_PASSWORD_OTP_EXPIRED]);
            
            // TODO: move this logic to generateOTP

            while (!$isUniqueToken) {

                /**
                 * Generate a new OTP
                 */

                $uniqueOTP = $this->generateOTP();

                $resetPasswordOtp = ResetPasswordOTP::where('otp', $uniqueOTP)
                    ->exists();

                if (!$resetPasswordOtp) {
                    $resetPasswordOtpModel = new ResetPasswordOTP;

                    $resetPasswordOtpModel->user_id = $user->id;
                    $resetPasswordOtpModel->otp = $uniqueOTP;
                    $resetPasswordOtpModel->status = Constants::RESET_PASSWORD_OTP_CREATED;

                    if ($resetPasswordOtpModel->save()) {
                        $isUniqueToken = true;
                        Mail::to($user)->send(new SendResetPasswordOTPMail($user, $resetPasswordOtpModel));

                        if(empty(Mail::failures())){
                            return response()->json([
                                "data"=>[],
                                "message" => "OTP email sent successfully."
                            ], Constants::HTTP_SUCCESS);
                        }
                    }
                }
            }
        }

        return response()->json([
            "data"=>[],
            "message" => "Oops, something went went wrong while trying to send your OTP."
        ],
            Constants::HTTP_ERROR);

    }

    /**
     * Verify OTP code and return temporary verification code
     * @param $otp
     * Verification steps:
     * 1- check if the OTP exists.
     * 2- check if the OTP not expired based on the OTP_LIFETIME const
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/password/otp/verify",
     *   tags={"Auth"},
     *   summary="Verify OTP code",
     *   description="SVerify OTP code and return temporary verification code",
     *   operationId="jwtVerifyOTP",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="otp",
     *                   description="User OTP",
     *                   type="string",
     *                   example="wyHyJ9"
     *               )
     *           )
     *       )
     *   ),
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                           type="array",
     *                         @OA\Items(
     *                          @OA\Property(
     *                              property="verification_token",
     *                              type="string",
     *                              description="OTP verification token to be used on reset password"
     *                              )
     *                          )
     *                         ),
     *                         description="data wraper"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response nessage"
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     * )
     */

    public function verifyOTP(Request $request){

        $request->validate(['otp' => 'required']);

        $otp = $request->get("otp", null);

        $resetPasswordOTPModel = ResetPasswordOTP::where("otp", $otp)
                ->where("status", Constants::RESET_PASSWORD_OTP_CREATED)
                ->first();

        if($resetPasswordOTPModel){
            if($this->isOTPValid($resetPasswordOTPModel, Constants::OTP_LIFETIME)){
                // generate otp tmp verification token

                $isUniqueToken = false;

                // TODO: move this logic to generateOTPVerificationToken

                while(!$isUniqueToken){
                    $uniqueOTPVerificationToken = $this->generateOTPVerificationToken();
                    $resetPasswordOTPVerification = ResetPasswordOTPVerification::where('token', $uniqueOTPVerificationToken)
                        ->exists();

                    if(!$resetPasswordOTPVerification){
                        $resetPasswordOTPVerificationModel = new ResetPasswordOTPVerification;
                        $resetPasswordOTPVerificationModel->user_id = $resetPasswordOTPModel->user_id;
                        $resetPasswordOTPVerificationModel->otp_id = $resetPasswordOTPModel->id;
                        $resetPasswordOTPVerificationModel->token = $uniqueOTPVerificationToken;
                        $resetPasswordOTPVerificationModel->status = Constants::RESET_PASSWORD_OTP_VERIFICATION_CREATED;

                        if($resetPasswordOTPVerificationModel->save()){
                            $resetPasswordOTPModel->status = Constants::RESET_PASSWORD_OTP_ACTIVATED;
                            if($resetPasswordOTPModel->save()){
                                return response()->json([
                                    "data" => [
                                        "verification_token" => $uniqueOTPVerificationToken
                                    ],
                                    "message" => "OTP verified successfully"
                                ], Constants::HTTP_SUCCESS);
                            }
                        }
                    }
                }
            }else{
                return response()->json([
                    "data" => [],
                    "message" => "This OTP expired."
                ], Constants::HTTP_ERROR);
            }
        }else{
            return response()->json([
                "data" => [],
                "message" => "This OTP not valid."
            ], Constants::HTTP_ERROR);
        }

            return response()->json([
                "data" => [],
                "message" => "Oops, something went wrong."
            ], Constants::HTTP_ERROR);

    }

    /**
     * Reset user password
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/password/reset",
     *   tags={"Auth"},
     *   summary="Reset user password",
     *   description="Reset user password",
     *   operationId="jwtVerifyOTP",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="verification_token",
     *                   description="verification token from (/user/auth/jwt/password/otp/verify) end point",
     *                   type="string",
     *                   example="SEJJJvD8YbpfXDljgMI3RAXySokNXIRmPT7yB7H2NZcnNSHTJgWUVJyAaExGvDPo"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="New password",
     *                   type="string",
     *                   example="larapoints123"
     *               ),
     *               @OA\Property(
     *                   property="password_confirmation",
     *                   description="New password confirmation",
     *                   type="string",
     *                   example="larapoints123"
     *               ),
     *           )
     *       )
     *   ),
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(
     *                           type="array",
     *                         @OA\Items(
     *                          )
     *                         ),
     *                         description="data wraper"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response nessage"
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     * )
     */
    public function resetPassword(Request $request){
        $request->validate([
            'verification_token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $verificationToken =  $request->get("verification_token", null);
        $password = $request->get("password", null);

        $resetPasswordOTPVerificationModel = ResetPasswordOTPVerification::where("token", $verificationToken)
            ->where("status", Constants::RESET_PASSWORD_OTP_CREATED)
            ->first();

        if($resetPasswordOTPVerificationModel){

            if($this->isOTPVerificationTokenValid($resetPasswordOTPVerificationModel, Constants::OTP_VERIFICATION_TOKEN_LIFETIME)){
                $user = User::where('id', $resetPasswordOTPVerificationModel->user_id)->first();
                if($user){
                    $user->password = Hash::make($password);
                    if($user->save()){
                        $resetPasswordOTPVerificationModel->status = Constants::RESET_PASSWORD_OTP_ACTIVATED;
                        if($resetPasswordOTPVerificationModel->save()){
                            return response()->json([
                                "data" => [],
                                "message" => "Password reset successfully."
                            ], Constants::HTTP_SUCCESS);
                        }
                    }
                }
            }
        }

        return response()->json([
            "data" => [],
            "message" => "Something went wrong while trying to reset your password."
        ], Constants::HTTP_ERROR);

    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     * TODO: move this to helpers
     */
    protected function respondWithToken($token)
    {

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth("api_jwt")->factory()->getTTL() * 60
        ]);
    }

}


