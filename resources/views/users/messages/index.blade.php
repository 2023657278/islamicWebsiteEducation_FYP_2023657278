@extends('users.students')

@section('content')
<style>
    .wa-list-container { background: white; border-radius: 12px; overflow: hidden; border: 1px solid #ddd; height: 85vh; display: flex; flex-direction: column; }
    .wa-header { background-color: #00a884; color: white; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; }
    .wa-contact-item { display: flex; align-items: center; padding: 12px 15px; border-bottom: 1px solid #f0f2f5; cursor: pointer; transition: 0.2s; text-decoration: none; color: inherit; }
    .wa-contact-item:hover { background-color: #f5f6f6; }
    .wa-avatar { width: 45px; height: 45px; border-radius: 50%; background-color: #dfe5e7; margin-right: 15px; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #fff; flex-shrink: 0; }
    .wa-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .wa-info { flex: 1; overflow: hidden; text-align: left; }
    .wa-time { font-size: 0.75rem; color: #667781; margin-left: 10px; }
    .section-title { padding: 10px 15px; background: #f0f2f5; font-size: 0.75rem; font-weight: bold; color: #54656f; text-transform: uppercase; text-align: left; }
</style>

<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="wa-list-container shadow-sm">
                <div class="wa-header">
                    <h5 class="mb-0 fw-bold">Messages PAI</h5>
                </div>
                
                <div style="overflow-y: auto; flex: 1;">
                    
                    {{-- ANNOUNCEMENTS --}}
                    <div class="section-title">Announcements</div>
                    
                    <a href="{{ route('student.messages.show', 'global') }}" class="wa-contact-item">
                        <div class="wa-avatar bg-danger"><i class="fas fa-university"></i></div>
                        <div class="wa-info">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-dark">School Announcements</span>
                                @if($globalChannel->last_message)
                                    <span class="wa-time">{{ $globalChannel->last_message->created_at->format('H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0 text-truncate">
                                {{ $globalChannel->last_message ? $globalChannel->last_message->message : 'No school news yet' }}
                            </p>
                        </div>
                    </a>

                    <a href="{{ route('student.messages.show', 'group') }}" class="wa-contact-item">
                        <div class="wa-avatar bg-warning"><i class="fas fa-bullhorn"></i></div>
                        <div class="wa-info">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-dark">Class Announcements</span>
                                @if($groupChannel->last_message)
                                    <span class="wa-time">{{ $groupChannel->last_message->created_at->format('H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0 text-truncate">
                                {{ $groupChannel->last_message ? $groupChannel->last_message->message : 'No class news yet' }}
                            </p>
                        </div>
                    </a>

                    {{-- TEACHERS --}}
                    <div class="section-title">My Teachers</div>
                    @foreach($teachers as $teacher)
                    <a href="{{ route('student.messages.show', $teacher->id) }}" class="wa-contact-item">
                        <div class="wa-avatar">
                            @if($teacher->profile_image)
                                {{-- 🟢 FIXED: Swapped to secure Storage URL loader rule --}}
                                <img src="{{ str_starts_with($teacher->profile_image, 'profile_images/') ? asset('storage/' . $teacher->profile_image) : asset('storage/profile_images/' . $teacher->profile_image) }}">
                            @else
                                <span class="text-secondary fw-bold">{{ substr($teacher->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div class="wa-info">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-dark">{{ $teacher->name }}</span>
                                @if($teacher->last_message)
                                    <span class="wa-time">{{ $teacher->last_message->created_at->format('H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-0 text-truncate">{{ $teacher->last_message ? $teacher->last_message->message : 'Tap to chat' }}</p>
                        </div>
                    </a>
                    @endforeach

                    {{-- CLASSMATES --}}
                    <div class="section-title">Classmates</div>
                    @foreach($classmates as $student)
                    <a href="{{ route('student.messages.show', $student->id) }}" class="wa-contact-item">
                        <div class="wa-avatar bg-light">
                            @if($student->profile_image)
                                {{-- 🟢 FIXED: Checks if profile_images folder naming prefix is present before compiling path URL --}}
                                <img src="{{ str_starts_with($student->profile_image, 'profile_images/') ? asset('storage/' . $student->profile_image) : asset('storage/profile_images/' . $student->profile_image) }}">
                            @else
                                <span class="text-secondary fw-bold small">{{ substr($student->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div class="wa-info">
                            <span class="fw-bold text-dark d-block">{{ $student->name }}</span>
                            <p class="text-muted small mb-0 text-truncate">{{ $student->last_message ? $student->last_message->message : 'Tap to chat' }}</p>
                        </div>
                    </a>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
@endsection