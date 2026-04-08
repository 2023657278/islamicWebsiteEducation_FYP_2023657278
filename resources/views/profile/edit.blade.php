@extends('admin.adminhome')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit mr-2"></i>Edit Profile</h4>
                </div>
                
                <div class="card-body">
                    {{-- Error Alerts --}}
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    @endif
                    
                    {{-- Success Alerts --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    @endif
                    
                    {{-- Profile Image Preview Section --}}
                    <div class="text-center mb-4">
                        <div class="profile-image-container mx-auto mb-3">
                            @php
                                // Check explicitly on 'public' disk
                                $hasImage = $user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists('profile_images/' . $user->profile_image);
                            @endphp
                            
                            @if($hasImage)
                            {{-- Asset points to public/storage/profile_images --}}
                            <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" 
                                 class="img-fluid rounded-circle profile-img-edit" 
                                 alt="Profile Image"
                                 id="profileImagePreview"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #2b6cb0;">
                            <div class="mt-3">
                                <form action="{{ route('profile.deleteImage') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to remove your profile image?')">
                                            <i class="fas fa-trash mr-1"></i> Remove Current Image
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="avatar-circle-lg mx-auto" id="avatarPreview" 
                                 style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 48px; margin: 0 auto; cursor: pointer;">
                                <span class="initials-lg">{{ $user->avatar_initials ?? substr($user->name, 0, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Main Update Form --}}
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Image Input --}}
                        <div class="form-group">
                            <label for="profile_image" class="font-weight-bold">Profile Image</label>
                            
                            <input type="file" 
                                   name="profile_image" 
                                   id="profile_image" 
                                   class="form-control-file @error('profile_image') is-invalid @enderror"
                                   accept="image/*">
                                   
                            <small class="form-text text-muted">
                                Supported formats: JPG, PNG, GIF (Max: 2MB)
                            </small>

                            <div id="fileSizeDisplay" class="small text-muted mt-1" style="display: none;"></div>
                            @error('profile_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-danger small mt-1 file-error-message" style="display: none;"></div>
                        </div>
                        
                        {{-- Name Input --}}
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Full Name *</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}"
                                   required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Email Input --}}
                        <div class="form-group">
                            <label for="email" class="font-weight-bold">Email Address *</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Phone Input --}}
                        <div class="form-group">
                            <label for="phone" class="font-weight-bold">Phone Number</label>
                            <input type="text" 
                                   name="phone" 
                                   id="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $user->phone_number) }}"
                                   placeholder="Optional">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        {{-- Password Section --}}
                        <h5 class="mb-3"><i class="fas fa-lock mr-2"></i>Change Password</h5>
                        <p class="text-muted mb-3">Leave blank if you don't want to change password</p>
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" 
                                   name="new_password" 
                                   id="new_password" 
                                   class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" 
                                   name="new_password_confirmation" 
                                   id="new_password_confirmation" 
                                   class="form-control">
                        </div>
                        
                        {{-- Submit Button --}}
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                                <i class="fas fa-save mr-2"></i> Update Profile
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('profile.show') }}" class="btn btn-link">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Clear errors
    function clearFileErrors() {
        $('#profile_image').removeClass('is-invalid');
        $('.file-error-message').hide().text('');
    }
    
    // File Selection
    $('#profile_image').change(function() {
        const file = this.files[0];
        const fileInput = $(this);
        clearFileErrors();
        
        if (file) {
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            let isValid = true;
            let errorMessage = '';

            if (file.size > maxSize) {
                isValid = false;
                errorMessage = 'File size must be less than 2MB';
            } else if (!validTypes.includes(file.type)) {
                isValid = false;
                errorMessage = 'Only JPG, PNG and GIF files are allowed';
            }
            
            if (isValid) {
                $('#fileSizeDisplay').text('Size: ' + formatFileSize(file.size)).show();
            } else {
                fileInput.addClass('is-invalid');
                $('.file-error-message').text(errorMessage).show();
                $('#fileSizeDisplay').hide();
                this.value = ''; 
            }
        } else {
            $('#fileSizeDisplay').hide();
        }
    });
    
    // Click avatar to trigger upload
    $('.profile-img-edit, .avatar-circle-lg').css('cursor', 'pointer').click(function() {
        $('#profile_image').click();
    });
    
    // Form submit
    $('#profileUpdateForm').submit(function(e) {
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Updating...');
    });
});
</script>

<style>
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.profile-img-edit, .avatar-circle-lg {
    cursor: pointer;
}
</style>
@endsection