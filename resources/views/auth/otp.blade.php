@extends('layouts.auth')

@section('content')
<div class="row w-100 mx-0">
    <div class="col-lg-5 mx-auto">
        <div class="auth-form-light text-left py-5 px-4 px-sm-5 shadow-sm">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" class="otp-form-logo">
            </div>

            <h6 class="fw-light mb-4 text-center">
                Masukkan kode OTP 6 digit yang dikirim ke email Anda.
            </h6>

            <div class="alert alert-info text-center" role="alert">
                <strong>Verifikasi OTP</strong>
            </div>

            <form class="pt-2" method="POST" action="{{ route('otp.verify') }}">
                @csrf

                <div class="form-group">
                    <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}">
                    <div class="otp-boxes">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" inputmode="text" maxlength="1"
                                class="otp-digit @error('otp') is-invalid @enderror"
                                data-index="{{ $i }}"
                                value="{{ old('otp') ? substr(old('otp'), $i, 1) : '' }}"
                                {{ $i === 0 ? 'autofocus' : '' }}>
                        @endfor
                    </div>

                    @error('otp')
                    <span class="invalid-feedback d-block text-center" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="d-grid gap-2 mb-2">
                    <button type="submit" class="btn btn-gradient-primary btn-lg auth-form-btn">
                        {{ __('Verifikasi') }}
                    </button>
                    <a class="btn btn-light btn-lg auth-form-btn" href="{{ route('login') }}">
                        {{ __('Kembali ke Login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style-page')
<style>
    .auth .auth-form-light {
        border-radius: 0.5rem;
    }

    .otp-form-logo {
        width: 100%;
        max-width: 170px;
        height: auto;
    }

    .otp-boxes {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .otp-digit {
        width: 46px;
        height: 54px;
        text-align: center;
        border: 1px solid #ced4da;
        border-radius: 0.35rem;
        font-size: 1.25rem;
        font-weight: 600;
        outline: none;
        text-transform: uppercase;
    }

    .otp-digit:focus {
        border-color: #b66dff;
        box-shadow: 0 0 0 0.2rem rgba(182, 109, 255, 0.2);
    }

    .otp-digit.is-invalid {
        border-color: #fe7c96;
    }
</style>
@endpush

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpInput = document.getElementById('otp');
        const digits = Array.from(document.querySelectorAll('.otp-digit'));

        function syncOtp() {
            otpInput.value = digits.map(function (input) {
                return input.value || '';
            }).join('');
        }

        digits.forEach(function (input, index) {
            input.addEventListener('input', function () {
                input.value = input.value.replace(/\s/g, '').slice(0, 1);
                syncOtp();

                if (input.value && index < digits.length - 1) {
                    digits[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            input.addEventListener('paste', function (event) {
                event.preventDefault();
                const pasted = (event.clipboardData || window.clipboardData).getData('text').replace(/\s/g, '').slice(0, 6);

                pasted.split('').forEach(function (char, i) {
                    if (digits[i]) {
                        digits[i].value = char;
                    }
                });

                syncOtp();
                digits[Math.min(pasted.length, digits.length - 1)].focus();
            });
        });

        syncOtp();
    });
</script>
@endpush
