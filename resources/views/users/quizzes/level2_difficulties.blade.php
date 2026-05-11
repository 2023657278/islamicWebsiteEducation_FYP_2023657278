@extends('users.students')

@section('content')
@php 
    $mode = request('mode', 'solo'); 
    $isPvp = ($mode === 'pvp');
    $userPts = Auth::user()->pvp_points;
@endphp

<div class="container-fluid p-0 text-start">
    {{-- Header --}}
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <a href="{{ route('student.quizzes.select_mode', $subject->id) }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Mode Selection
                </a>
                <h2 class="fw-bold mt-2">
                    {{ $subject->subject_name }} 
                    <span class="text-{{ $isPvp ? 'warning' : 'primary' }} fw-light">
                        / {{ $isPvp ? 'PVP Battle Royale' : 'Solo Skill Training' }}
                    </span>
                </h2>
                <p class="text-muted">
                    {{ $isPvp ? 'Challenge warriors in a randomized arena battle.' : 'Master each level to unlock the next challenge.' }}
                </p>
            </div>
            
            {{-- 🏆 User Current Rank Display --}}
            <div class="text-end">
                <div class="p-3 bg-white shadow-sm border rounded-4">
                    <small class="text-muted d-block fw-bold text-uppercase">Your Current Ranking</small>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4 fw-black text-primary">{{ number_format($userPts) }} <small class="fs-6">PTS</small></span>
                        @if($userPts >= 300)
                            <span class="badge bg-warning text-dark rounded-pill px-3">GOLD RANK</span>
                        @elseif($userPts >= 100)
                            <span class="badge bg-secondary text-white rounded-pill px-3">SILVER RANK</span>
                        @else
                            <span class="badge bg-bronze rounded-pill px-3" style="background: #CD7F32; color: white;">BRONZE RANK</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Privacy Toggle (Only for PvP) --}}
    @if($isPvp)
    <div class="d-flex justify-content-center mb-4">
        <div class="form-check form-switch p-3 bg-white rounded-pill shadow-sm border">
            <input class="form-check-input ms-0 me-2" type="checkbox" id="isPublic" checked style="cursor: pointer;">
            <label class="form-check-label fw-bold text-muted mb-0" for="isPublic" style="cursor: pointer;">
                <i class="fas fa-globe-asia me-1 text-primary"></i> Make Mission Public
            </label>
        </div>
    </div>
    @endif

    <div class="row g-4">
        @foreach(['Easy', 'Medium', 'Hard'] as $level)
            @php 
                // 🔒 PROGRESSION GATING LOGIC
                $ptsNeeded = ($level == 'Medium') ? 100 : (($level == 'Hard') ? 300 : 0);
                
                // If PvP: Check Points. If Solo: Check progress array from controller.
                if($isPvp) {
                    $isAllowed = $userPts >= $ptsNeeded;
                } else {
                    $isAllowed = in_array($level, $allowed);
                }

                $color = $level == 'Easy' ? 'success' : ($level == 'Medium' ? 'warning' : 'danger');
                $levelStats = $stats[$level] ?? ['done' => 0, 'total' => 0, 'avg' => 0];
                $maxWinPts = ['Easy' => 15, 'Medium' => 30, 'Hard' => 70];
            @endphp
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden {{ !$isAllowed ? 'bg-light opacity-75' : '' }}" style="transition: 0.3s;">
                    <div style="height: 6px; background-color: var(--bs-{{ $color }});"></div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <div class="mb-3">
                            <div class="bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-circle d-inline-flex p-3">
                                @if(!$isAllowed)
                                    <i class="fas fa-lock fa-2x"></i>
                                @else
                                    <i class="fas {{ $isPvp ? 'fa-fire-alt' : 'fa-medal' }} fa-2x"></i>
                                @endif
                            </div>
                        </div>

                        <h3 class="fw-bold text-dark mb-1">{{ $level }}</h3>
                        
                        @if($isPvp)
                            <div class="mt-3 mb-4">
                                @if(!$isAllowed)
                                    <span class="text-danger fw-bold small">
                                        <i class="fas fa-exclamation-circle me-1"></i> Requires {{ $ptsNeeded }} PTS to unlock
                                    </span>
                                @else
                                    <span class="badge bg-dark text-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-trophy me-1"></i> Up to +{{ $maxWinPts[$level] }} Ranking Points
                                    </span>
                                @endif
                                <p class="text-muted small mt-2">10 Randomized Arena Questions</p>
                            </div>
                        @else
                            <div class="mt-3 mb-4">
                                <div class="progress" style="height: 10px; border-radius: 5px; background: rgba(0,0,0,0.05);">
                                    @php $percent = ($levelStats['total'] > 0) ? ($levelStats['done'] / $levelStats['total']) * 100 : 0; @endphp
                                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $percent }}%"></div>
                                </div>
                                <small class="text-muted d-block mt-2">{{ $levelStats['done'] }}/{{ $levelStats['total'] }} Missions Completed</small>
                            </div>
                        @endif

                        <div class="mt-auto">
                            @if($isAllowed)
                                @if($isPvp)
                                    <button type="button" onclick="startPvp('{{ $subject->id }}', '{{ $level }}')" 
                                            class="btn btn-{{ $color }} w-100 rounded-pill fw-black py-2 shadow-sm border-0">
                                        DEPLOY {{ strtoupper($level) }} MISSION
                                    </button>
                                @else
                                    <a href="{{ route('student.quizzes.topics_diff', [$subject->id, $level]) }}" 
                                       class="btn btn-{{ $color }} w-100 rounded-pill fw-black py-2 shadow-sm border-0">
                                        ENTER LEVEL
                                    </a>
                                @endif
                            @else
                                <div class="p-2 bg-white border border-dashed rounded-pill small text-muted fw-bold">
                                    <i class="fas fa-lock me-1"></i> CONTENT LOCKED
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- JAVASCRIPT SYNC --}}
<script>
    function startPvp(subjectId, level) {
        const isPublic = document.getElementById('isPublic').checked ? 1 : 0;
        // Show a confirmation before creating a mission
        if(confirm(`Are you ready to deploy a ${level} mission?`)) {
            window.location.href = `/student/quizzes/create_pvp/${subjectId}/${level}?is_public=${isPublic}`;
        }
    }
</script>

<style>
    .card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
    .fw-black { font-weight: 900; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
@endsection