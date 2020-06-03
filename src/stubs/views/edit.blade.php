@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">

                    <div class="card-body">
                        <small class="text-muted">Edit team</small>
                        <a href="{{ route('teams.index') }}" class="btn btn-sm btn-secondary float-right">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <h4>
                            {{ $team->name }}

                        </h4>
                        <form class="form-horizontal" method="POST" action="{{route('teams.update', $team)}}">
                            @csrf @method('PUT')

                            <div class="form-group">

                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                       value="{{ old('name', $team->name) }}"
                                       placeholder="Enter name">

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fa fa-btn fa-save"></i>Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
