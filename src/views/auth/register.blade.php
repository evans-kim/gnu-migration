@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="mb_id" class="col-md-4 col-form-label text-md-right">아이디</label>

                            <div class="col-md-6">
                                <input id="mb_id" type="text" class="form-control{{ $errors->has('mb_id') ? ' is-invalid' : '' }}" name="mb_id" value="{{ old('mb_id') }}" required autofocus>

                                @if ($errors->has('mb_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mb_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mb_name" class="col-md-4 col-form-label text-md-right">실명</label>

                            <div class="col-md-6">
                                <input id="mb_name" type="text" class="form-control{{ $errors->has('mb_name') ? ' is-invalid' : '' }}" name="mb_name" value="{{ old('mb_name') }}" required autofocus>

                                @if ($errors->has('mb_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mb_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mb_nick" class="col-md-4 col-form-label text-md-right">닉네임</label>

                            <div class="col-md-6">
                                <input id="mb_nick" type="text" class="form-control{{ $errors->has('mb_nick') ? ' is-invalid' : '' }}" name="mb_nick" value="{{ old('mb_nick') }}" required autofocus>

                                @if ($errors->has('mb_nick'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mb_nick') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_email" class="col-md-4 col-form-label text-md-right">이메일주소</label>

                            <div class="col-md-6">
                                <input id="mb_email" type="email" class="form-control{{ $errors->has('mb_email') ? ' is-invalid' : '' }}" name="mb_email" value="{{ old('mb_email') }}" required>

                                @if ($errors->has('mb_email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mb_email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mb_password" class="col-md-4 col-form-label text-md-right">패스워드</label>

                            <div class="col-md-6">
                                <input id="mb_password" type="password" class="form-control{{ $errors->has('mb_password') ? ' is-invalid' : '' }}" name="mb_password" required>

                                @if ($errors->has('mb_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mb_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">패스워드 확인</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="mb_password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
