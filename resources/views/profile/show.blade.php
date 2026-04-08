@extends('admin.adminhome')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user mr-2"></i>My Profile</h4>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    @endif
                    
                    <div class="text-center mb-5">
                        <div class="profile-image-container mx-auto mb-3">
                            @php
                                // FIX: Use the 'public' disk explicitly
                                $hasImage = $user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists('profile_images/' . $user->profile_image);
                            @endphp
                            
                            @if($hasImage)
                            <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" 
                                 class="img-fluid rounded-circle profile-img" 
                                 alt="Profile Image"
                                 style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #2b6cb0;">
                            @else
                            <div class="avatar-circle-lg mx-auto" 
                                 style="width: 150px; height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 48px; margin: 0 auto;">
                                <span class="initials-lg">{{ $user->avatar_initials ?? substr($user->name, 0, 2) }}</span>
                            </div>
                            @endif
                        </div>
                        <h3 class="mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <p class="text-muted">
                            <i class="fas fa-user-circle mr-1"></i> 
                            Member since {{ $user->created_at->format('F Y') }}
                        </p>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-user mr-2"></i>Full Name
                                </div>
                                <div class="col-md-8">{{ $user->name }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-envelope mr-2"></i>Email Address
                                </div>
                                <div class="col-md-8">{{ $user->email }}</div>
                            </div>

                            @if($user->phone_number)
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-phone mr-2"></i>Phone Number
                                </div>
                                <div class="col-md-8">{{ $user->phone_number }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Information Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-user-shield mr-2"></i>Account Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-plus mr-2"></i>Account Created
                                </div>
                                <div class="col-md-8">
                                    {{ $user->created_at->format('l, F d, Y') }}
                                    <small class="text-muted d-block">
                                        ({{ $user->created_at->diffForHumans() }})
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-check mr-2"></i>Last Updated
                                </div>
                                <div class="col-md-8">
                                    {{ $user->updated_at->format('l, F d, Y') }}
                                    <small class="text-muted d-block">
                                        ({{ $user->updated_at->diffForHumans() }})
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold text-primary">
                                    <i class="fas fa-user-check mr-2"></i>Account Status
                                </div>
                                <div class="col-md-8">
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                    @if($user->email_verified_at)
                                    <span class="badge badge-info ml-2">
                                        <i class="fas fa-envelope mr-1"></i> Verified Email
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-home mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection