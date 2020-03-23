<?php

namespace App\Http\Controllers\Auth\JWT;


use App\Mail\SendResetPasswordOTPMail;
use App\ResetPasswordOTP;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Helpers\Validators;
use App\Http\Helpers\Generators;
use App\Http\Helpers\Constants;

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
            ->except(["login", "sendResetPasswordOTP"]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     *
     * @param Request $request
     * @return mixed|\Symfony\Component\HttpFoundation\ParameterBag
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
                        Mail::to($user)->send(new SendResetPasswordOTPMail($user, $resetPasswordOtpModel));

                        if(empty(Mail::failures())){
                            return response()->json(["data"=>[], "message" => "OTP email sent successfully."], 200);
                        }
                    }
                }
            }
        }

        return response()->json([
            "data"=>[],
            "message" => "Oops, something went went wrong while trying to send your OTP."
        ],
            400);

    }


}
