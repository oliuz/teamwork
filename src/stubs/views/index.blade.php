@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">
                            Teams
                            <div class="btn-group float-right" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal"
                                        data-target="#createModalCenter">
                                    <i class="fa fa-plus"></i> Create team
                                </button>
                            </div>
                        </h1>
                        <table class="table table-striped">
                            <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($teams as $team)
                                <tr>
                                    <td>{{$team->name}}</td>
                                    <td>
                                        @if(auth()->user()->isOwnerOfTeam($team))
                                            <span class="badge badge-success">Owner</span>
                                        @else
                                            <span class="badge badge-primary">Member</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null(auth()->user()->currentTeam) || auth()->user()->currentTeam->getKey() !== $team->getKey())
                                            <a href="{{route('teams.switch', $team)}}" class="btn btn-sm btn-secondary">
                                                <i class="fa fa-sign-in"></i> Switch
                                            </a>
                                        @else
                                            <span class="badge badge-secondary">Current team</span>
                                        @endif

                                        <a href="{{route('teams.members.show', $team)}}" class="btn btn-sm btn-dark">
                                            <i class="fa fa-users"></i> Members
                                        </a>

                                        @if(auth()->user()->isOwnerOfTeam($team))

                                            <a href="{{route('teams.edit', $team)}}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-pencil"></i> Edit
                                            </a>

                                            <form style="display: inline-block;"
                                                  action="{{route('teams.destroy', $team)}}" method="post">
                                                @csrf
                                                <input type="hidden" name="_method" value="DELETE"/>
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('teamwork.create')

@endsection
