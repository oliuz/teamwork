@extends('layouts.app')

@section('content')
    <div class="container">
        @include('teamwork.includes.messages')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <small class="text-muted">Members of team</small>
                <h4>
                    {{ $team->name }}
                    <a href="{{ route('teams.index') }}" class="btn btn-sm btn-secondary float-right">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </h4>
                <table class="table table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    @foreach($team->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($team->isOwnerAuth())
                                    @if(auth()->user()->getKey() !== $user->getKey())
                                        <form style="display: inline-block;"
                                              action="{{route('teams.members.destroy', [$team, $user])}}"
                                              method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash-o"></i>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            @if($team->isOwnerAuth())
                <div class="col-md-4">
                    <small class="text-muted">Invite to team </small>
                    <small class="font-weight-bold">{{ $team->name }}</small>
                    <form class="form-horizontal" method="post" action="{{ route('teams.members.invite', $team) }}">
                        @csrf
                        <div class="form-group">
                            <label class="control-label">E-Mail Address</label>

                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}">

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-btn fa-envelope-o"></i> Invite to Team
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
        @if($team->isOwnerAuth())
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <small class="text-muted">Pending invitations</small>
                    <table class="table table-striped">
                        @if ($team->invites->count())
                            <thead class="thead-dark">
                            <tr>
                                <th>E-Mail</th>
                                <th>Sent</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        @endif

                        @forelse($team->invites as $invite)
                            <tr>
                                <td>{{ $invite->email }}</td>
                                <td>
                                    <small class="text-muted">{{ $invite->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('teams.members.resend_invite', $invite) }}"
                                       class="btn btn-sm btn-secondary">
                                        <i class="fa fa-envelope-o"></i>
                                        Resend invite
                                    </a>
                                    <form style="display: inline-block;"
                                          action="{{ route('teams.members.invite_destroy', $invite) }}"
                                          method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                            Delete Invite
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <div class="jumbotron jumbotron-fluid text-center">
                                <div class="container">
                                    <h1 class="display-4">Oh!</h1>
                                    <p class="lead">You don't have pending invitations!</p>
                                </div>
                            </div>
                        @endforelse
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
