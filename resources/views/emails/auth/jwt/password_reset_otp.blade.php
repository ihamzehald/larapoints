@extends('layouts.email')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('auth_jwt.password_reset_request_otp_title') }}</div>

                    {{__("auth_jwt.password_reset_request_otp_body", ["otp" => $otp->otp])}}
                </div>
            </div>
        </div>
    </div>
@endsection


