@extends('layouts.app')

@section('content')
    <div class="container">
        @include('teamwork.includes.messages')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3>
                    Teams
                    <div class="btn-group float-right" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                                data-target="#createModalCenter">
                            <i class="fa fa-plus"></i> Create team
                        </button>
                    </div>
                </h3>
                <table class="table table-striped">
                    @if ($teams->count())
                        <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th></th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    @endif

                    <tbody>
                    @forelse($teams as $team)
                        <tr>
                            <td class="font-weight-bold">{{ $team->name }}</td>
                            <td>
                                <small class="text-muted">{{ $team->isOwnerAuthCheck() }}</small>
                            </td>
                            <td>
                                @if(is_null(auth()->user()->currentTeam) || auth()->user()->currentTeam->getKey() !== $team->getKey())
                                    <a href="{{route('teams.switch', $team)}}" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-sign-in"></i> Switch
                                    </a>
                                @else
                                    <small class="text-muted font-weight-bold">Current team</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('teams.members.show', $team)}}" class="btn btn-sm btn-dark">
                                    <i class="fa fa-users"></i> Members
                                </a>

                                @if($team->isOwnerAuth())

                                    <a href="{{route('teams.edit', $team)}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>

                                    <form style="display: inline-block;"
                                          action="{{ route('teams.destroy', $team) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash-o"></i>
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <div class="jumbotron jumbotron-fluid text-center">
                            <div class="container">
                                <h1 class="display-4">Oh!</h1>
                                <p class="lead">You don't have teams available!</p>
                            </div>
                        </div>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div hidden class="col-md-4">
                <small class="text-muted">Create a new team</small>
                <form class="form-horizontal" method="POST" action="{{route('teams.store')}}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label">Name</label>

                        <input id="team-name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required>

                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary btn-block">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('teamwork.create')

    <script>
        if (window.location.hash === '#create') {
            $('#createModalCenter').modal('show');
        }

        $('#createModalCenter').on('hide.bs.modal', function () {
            window.location.hash = '#';
        });

        $('#createModalCenter').on('shown.bs.modal', function () {
            $('#team-name').focus();
            window.location.hash = '#create';
        });
    </script>
@endsection
