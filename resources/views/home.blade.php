@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('You are logged in!') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Имеющиеся у юзера токены</div>

                    <div class="card-body">
                        {{--                        @php(dd(auth()->user()->tokens)) @endphp--}}
                        @foreach (auth()->user()->tokens as $token)
                            {{--                        {{ $token->$id }} - {{ $token->plainTextToken }}--}}
                            {{ $token['id'] }} - {{ $token['name'] }} - {{ implode(', ', $token['abilities']) }}<br>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Создание токенов для пользователя</div>

                    <div class="card-body">
                        <form action="{{ route('create-token') }}" method="POST">
                            @csrf

                            @foreach ($rights as $right)
                            <p><label><input type="checkbox" name="right{{$right->id}}">
                                    <span>{{ $right->right }}</span></label></p>
                            @endforeach

                            <input id="token_name" name="token_name" autofocus required>
                            <button type="submit" class="btn btn-secondary">
                                Создать
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
