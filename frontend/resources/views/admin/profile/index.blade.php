@extends('admin')

@section('content')
<div class="page-header">
    <h3 class="page-title">My Profile</h3>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    <div class="avatar-circle-profile">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
                <h4 class="card-title mt-3">{{ Auth::user()->name }}</h4>
                <p class="text-muted">{{ Auth::user()->email }}</p>
                <p class="text-muted">
                    <small>Member since: {{ Auth::user()->created_at->format('d F Y') }}</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Profil</h4>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Card -->
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ubah Password</h4>
                <form method="POST" action="{{ route('profile.update-password') }}">
                    @csrf
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                               id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control"
                               id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle-profile {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 48px;
        margin: 0 auto;
    }

    .profile-avatar {
        margin-bottom: 20px;
    }
</style>
@endsection
