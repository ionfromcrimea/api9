@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Сохраните нижеуказанный токен для дальнейшего использования</div>
                    <div class="card-body">
                            {{ $plainToken }}
                            <br><br>
                            <a href="{{ route('home') }}" class="btn btn-secondary">Назад</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
