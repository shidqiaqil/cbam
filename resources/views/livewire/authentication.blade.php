<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="mb-3">Sign In</h3>

        @if(session('message_error'))
        <div class="alert alert-danger">{{ session('message_error') }}</div>
        @endif

        <form wire:submit.prevent="signIn">
            <div class="mb-3">
                <label class="form-label">Employee ID</label>
                <input wire:model.defer="id_employee" type="text" class="form-control" />
                @error('id_employee') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input wire:model.defer="password" type="password" class="form-control" />
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary">Sign In</button>
                <a href="{{ url('/sso?ses=') }}" class="btn btn-link">Sign in with SSO</a>
            </div>
        </form>
    </div>
</div>