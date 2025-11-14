@extends('layouts.partials.client')

@section('title', 'Verify Email Address')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Xác Thực Email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Chúng tôi đã gửi lại link xác thực mới đến email của bạn.
                        </div>
                    @endif

                    Trước khi tiếp tục, vui lòng kiểm tra email của bạn để lấy link xác thực.
                    Nếu bạn không nhận được email,
                    <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                            bấm vào đây để gửi lại
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection