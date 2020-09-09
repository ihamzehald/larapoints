<?php

namespace App\Http\Controllers\API\V1\Auth\JWT;

use App\Mail\SendResetPasswordOTPMail;
use App\Models\ResetPasswordOTP;
use App\Models\ResetPasswordOTPVerification;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Helpers\Constants;
use \App\Http\Controllers\API\V1\APIController;

class JwtAuthController extends APIController
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware("auth:api_jwt")
            ->except([
                "login",
                "sendResetPasswordOTP",
                "verifyOTP",
                "resetPassword",
                "register"
            ]);
    }

    /**
     * Register a new user
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/Register",
     *   tags={"Auth"},
     *   summary="JWT Register",
     *   description="Register a new user",
     *   operationId="jwtRegister",
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
     *                   property="name",
     *                   description="User name",
     *                   type="string",
     *                   example="Hamza al darawsheh"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="User password",
     *                   type="string",
     *                   example="larapoints123"
     *               ),
     *              @OA\Property(
     *                   property="password_confirmation",
     *                   description="User password confirmation",
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                         @OA\Property(
     *                              property="id",
     *                              type="integer",
     *                              description="User id",
     *                              ),
     *                          @OA\Property(
     *                              property="access_token",
     *                              type="string",
     *                              description="JWT access token",
     *                              ),
     *                          @OA\Property(
     *                              property="token_type",
     *                              type="string",
     *                              description="Token type"
     *                              ),
     *                          @OA\Property(
     *                              property="expires_in",
     *                              type="integer",
     *                              description="Token expiration in miliseconds",
     *                              ),
     *                         @OA\Property(
     *                              property="name",
     *                              type="string",
     *                              description="User name"
     *                              ),
     *                        @OA\Property(
     *                              property="email",
     *                              type="string",
     *                              description="User email"
     *                              ),
     *                       @OA\Property(
     *                              property="updated_at",
     *                              type="string",
     *                              description="User updated at"
     *                              ),
     *                       @OA\Property(
     *                              property="created_at",
     *                              type="string",
     *                              description="User created at"
     *                              )
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "User logged in successfully",
     *                         "data": {
     *                                   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sYXJhcG9pbnRzLmxvY1wvYXBpXC92MlwvYXV0aFwvand0XC9yZWdpc3RlciIsImlhdCI6MTU5OTUwNDc0NCwiZXhwIjoxNTk5NTA4MzQ0LCJuYmYiOjE1OTk1MDQ3NDQsImp0aSI6Ik4zTW9Mek9xMHFlS2xmdnIiLCJzdWIiOjE3LCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIn0.7oAcjHhMK8kUyQpyXIfpmMC4vk12wJa7PR9KwRYLFMo",
     *                                    "token_type": "bearer",
     *                                    "expires_in": 3600,
     *                                    "name": "Hamzeh test",
     *                                    "email": "ihamzehald15@gmail.com",
     *                                    "updated_at": "2020-09-07T18:52:24.000000Z",
     *                                    "created_at": "2020-09-07T18:52:24.000000Z",
     *                                    "id": 17
     *                                    },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|confirmed|min:8',
            'email' => 'required|email|unique:users',
        ]);

        $userData = request(['name','email', 'password']);

        $newUser =  User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $message = "User registered successfully";

        $token = auth("api_jwt")->attempt($userData);
        $tokenData = $this->generateAccessTokenDetails($token);

        $response = array_merge($tokenData, $newUser->toArray());

        return $this->sendResponse(Constants::HTTP_SUCCESS, $message, $response);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return mixed
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                          @OA\Property(
     *                              property="access_token",
     *                              type="string",
     *                              description="JWT access token",
     *                              ),
     *                          @OA\Property(
     *                              property="token_type",
     *                              type="string",
     *                              description="Token type"
     *                              ),
     *                          @OA\Property(
     *                              property="expires_in",
     *                              type="integer",
     *                              description="Token expiration in miliseconds",
     *                              ),
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "User logged in successfully",
     *                         "data": {
     *                                      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                                      "token_type": "bearer",
     *                                      "expires_in": 3600
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="wrong_credentials",
     *                              type="string",
     *                              description="Wrong credentials error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Wrong credentials",
     *                         "data": null,
     *                         "errors": {
     *                                      "wrong_credentials": "The provided credentials don't match our records"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth("api_jwt")->attempt($credentials)) {
            $errors = [
                "wrong_credentials" => "The provided credentials don't match our records"
            ];

            return $this->sendResponse(
                Constants::HTTP_UNAUTHORIZED,
                "Wrong credentials",
                null,
                $errors
            );
        }

        return $this->sendResponse(
            Constants::HTTP_SUCCESS,
            "User logged in successfully",
            $this->generateAccessTokenDetails($token)
        );
    }


    /**
     * Log the user out (Invalidate the token).
     *
     *  @return mixed
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                 example={
     *                         "message": "User logged out successfully",
     *                         "data": null,
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */

    public function logout()
    {
        auth("api_jwt")->logout();
        $message = "User logged out successfully";
        return $this->sendResponse(Constants::HTTP_SUCCESS, $message, null);
    }

    /**
     * Refresh a token.
     *
     * @return mixed
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                          @OA\Property(
     *                              property="access_token",
     *                              type="string",
     *                              description="JWT access token",
     *                              ),
     *                          @OA\Property(
     *                              property="token_type",
     *                              type="string",
     *                              description="Token type"
     *                              ),
     *                          @OA\Property(
     *                              property="expires_in",
     *                              type="integer",
     *                              description="Token expiration in miliseconds",
     *                              ),
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "JWT token refresh successfully",
     *                         "data": {
     *                                      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                                      "token_type": "bearer",
     *                                      "expires_in": 3600
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */

    public function refresh()
    {
        $newToken = auth("api_jwt")->refresh();
        $data = $this->generateAccessTokenDetails($newToken);
        $message = "JWT token refresh successfully";

        return $this->sendResponse(Constants::HTTP_SUCCESS, $message, $data);
    }

    /**
     * Send OTP code to a user email
     *
     * @param Request $request
     * @return mixed as API response
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                  @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "OTP email sent successfully",
     *                         "data": null,
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
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

                        if (empty(Mail::failures())) {
                            $message = "OTP email sent successfully";

                            return $this->sendResponse(Constants::HTTP_SUCCESS, $message);
                        }
                    }
                }
            }
        }

        $message = "Oops, something went wrong while trying to send your OTP";

        return $this->sendResponse(Constants::HTTP_ERROR, $message);
    }


    /**
     * Verify OTP code and return temporary verification code
     *
     * @param $request
     *
     * Verification steps:
     * 1- check if the OTP exists.
     * 2- check if the OTP not expired based on the OTP_LIFETIME const
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/password/otp/verify",
     *   tags={"Auth"},
     *   summary="Verify OTP code",
     *   description="Verify OTP code and return temporary verification code",
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                  @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="response data",
     *                         @OA\Property(
     *                              property="verification_token",
     *                              type="string",
     *                              description="Reset password verification token",
     *                          ),
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "OTP verified successfully",
     *                         "data": {
     *                                      "verification_token": "WuKr7Rmrka5jNYu9y6..."
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
    public function verifyOTP(Request $request)
    {

        $request->validate(['otp' => 'required']);

        $otp = $request->get("otp", null);

        $resetPasswordOTPModel = ResetPasswordOTP::where("otp", $otp)
            ->where("status", Constants::RESET_PASSWORD_OTP_CREATED)
            ->first();

        if ($resetPasswordOTPModel) {
            if ($this->isOTPValid($resetPasswordOTPModel, Constants::OTP_LIFETIME)) {
                // generate otp tmp verification token

                $isUniqueToken = false;

                // TODO: move this logic to generateOTPVerificationToken

                while (!$isUniqueToken) {
                    $uniqueOTPVerificationToken = $this->generateOTPVerificationToken();
                    $resetPasswordOTPVerification = ResetPasswordOTPVerification::where('token', $uniqueOTPVerificationToken)
                        ->exists();

                    if (!$resetPasswordOTPVerification) {
                        $resetPasswordOTPVerificationModel = new ResetPasswordOTPVerification;
                        $resetPasswordOTPVerificationModel->user_id = $resetPasswordOTPModel->user_id;
                        $resetPasswordOTPVerificationModel->otp_id = $resetPasswordOTPModel->id;
                        $resetPasswordOTPVerificationModel->token = $uniqueOTPVerificationToken;
                        $resetPasswordOTPVerificationModel->status = Constants::RESET_PASSWORD_OTP_VERIFICATION_CREATED;

                        if ($resetPasswordOTPVerificationModel->save()) {
                            $resetPasswordOTPModel->status = Constants::RESET_PASSWORD_OTP_ACTIVATED;
                            if ($resetPasswordOTPModel->save()) {
                                $message = "OTP verified successfully";
                                $data = [
                                    "verification_token" => $uniqueOTPVerificationToken
                                ];

                                return $this->sendResponse(Constants::HTTP_SUCCESS, $message, $data);
                            }
                        }
                    }
                }
            } else {
                $message = "This OTP expired";
                return $this->sendResponse(Constants::HTTP_ERROR, $message);
            }
        } else {
            $message = "This OTP not valid";
            return $this->sendResponse(Constants::HTTP_ERROR, $message);
        }

        $message = "Oops, something went wrong";
        return $this->sendResponse(Constants::HTTP_ERROR, $message);
    }


    /**
     * Reset user password
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/password/reset",
     *   tags={"Auth"},
     *   summary="Reset user password",
     *   description="Reset user password",
     *   operationId="jwtPasswordReset",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="verification_token",
     *                   description="verification token from (/auth/jwt/password/otp/verify) end point",
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
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                  @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "Password reset successfully",
     *                         "data": null,
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */

    public function resetPassword(Request $request)
    {
        $request->validate([
            'verification_token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $verificationToken = $request->get("verification_token", null);
        $password = $request->get("password", null);

        $resetPasswordOTPVerificationModel = ResetPasswordOTPVerification::where("token", $verificationToken)
            ->where("status", Constants::RESET_PASSWORD_OTP_CREATED)
            ->first();

        if ($resetPasswordOTPVerificationModel) {
            if ($this->isOTPVerificationTokenValid($resetPasswordOTPVerificationModel, Constants::OTP_VERIFICATION_TOKEN_LIFETIME)) {
                $user = User::where('id', $resetPasswordOTPVerificationModel->user_id)->first();
                if ($user) {
                    $user->password = Hash::make($password);
                    if ($user->save()) {
                        $resetPasswordOTPVerificationModel->status = Constants::RESET_PASSWORD_OTP_ACTIVATED;
                        if ($resetPasswordOTPVerificationModel->save()) {
                            $message = "Password reset successfully";
                            return $this->sendResponse(Constants::HTTP_SUCCESS, $message);
                        }
                    }
                }
            }
        }

        $message = "Something went wrong while trying to reset your password";
        return $this->sendResponse(Constants::HTTP_ERROR, $message);
    }
}
