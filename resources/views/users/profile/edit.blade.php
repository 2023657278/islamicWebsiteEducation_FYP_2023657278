@extends('users.students')

@section('content')
<div class="container-fluid">
    
    {{-- Error/Success Messages --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <h2 class="fw-bold">Edit Profile</h2>
        <p class="text-muted">Update your personal information and photo.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        
        {{-- ✅ Main Update Form (Note: enctype is added) --}}
        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-5 align-items-center">
                <div class="col-md-12">
                    <label class="form-label fw-bold text-muted small mb-3">Profile Picture</label>
                    <div class="d-flex align-items-center gap-4">
                        
                        <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; background: #E6FFFA; border: 2px solid #eee; position: relative;">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-success fw-bold fs-3">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <input type="file" name="profile_image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                            
                            @if($user->profile_image)
                                <button type="submit" form="deleteImageForm" class="btn btn-sm btn-outline-danger border-0 text-start" style="width: fit-content;">
                                    <i class="fas fa-trash me-2"></i> Remove Photo
                                </button>
                            @endif
                            
                            <small class="text-muted" style="font-size: 0.8rem;">Supported: JPG, PNG. Max size: 5MB.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Full Name</label>
                    <input type="text" name="name" class="form-control form-control-lg" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Email Address</label>
                    <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email', $user->email) }}" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control form-control-lg" value="{{ old('phone_number', $user->phone_number) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">New Password (Optional)</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Leave blank to keep current password">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4 fw-bold" style="background: #008f78; border: none;">Save Changes</button>
                <a href="{{ route('student.profile.show') }}" class="btn btn-light px-4 border fw-bold">Cancel</a>
            </div>
        </form>

        {{-- ✅ Separate Hidden Form for Deleting Image --}}
        {{-- We use this separate form so clicking "Remove" doesn't submit the main update form --}}
        <form id="deleteImageForm" action="{{ route('student.profile.deleteImage') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>
</div>
@endsection