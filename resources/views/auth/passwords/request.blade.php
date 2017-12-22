@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Đặt lại mật khẩu</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if (isset($success))
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="control-label">
                                    Yêu cầu mật khẩu mới đã được gửi đến địa chỉ email: <strong>{{$email}}</strong>.
                                </label>
                            </div>

                            <form class="form-horizontal" method="POST" action="{{ route('password.reset') }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="email" class="col-md-4 control-label">Mã xác thực:</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="security_key"
                                               value="" required>
                                </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="col-md-4 control-label">Mật khẩu:</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="password"
                                               value="" required>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="col-md-4 control-label">Nhập lại mật khẩu:</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="password_confirmation"
                                               value="" required>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Tiếp tục
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">Địa chỉ email:</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email"
                                               value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Gửi yêu cầu
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
