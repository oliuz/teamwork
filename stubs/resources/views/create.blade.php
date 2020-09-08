<div class="modal fade" id="createModalCenter" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form class="form-horizontal" method="POST" action="{{route('teams.store', '#create')}}">
                @csrf
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title mb-3" id="exampleModalCenterTitle">Create a new team</h5>

                    <div class="form-group">

                        <input id="team-name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" placeholder="Enter name" required>

                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <button class="btn btn-primary btn-block btn-lg">Save</button>

                </div>
            </form>
        </div>
    </div>
</div>
