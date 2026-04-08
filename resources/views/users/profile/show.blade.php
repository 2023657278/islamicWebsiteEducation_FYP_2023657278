@extends('users.students')

@section('content')
<style>
    /* Profile UI Styling */
    .profile-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid #eee; margin-bottom: 30px; }
    .profile-banner { height: 140px; background: #008f78; }
    .profile-content { padding: 0 40px 40px 40px; position: relative; }
    .avatar-box { width: 110px; height: 110px; background: white; padding: 6px; border-radius: 20px; position: absolute; top: -55px; left: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .avatar-inner { width: 100%; height: 100%; background: #E6FFFA; color: #008f78; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border-radius: 14px; overflow: hidden; }
    
    /* Info Box Design */
    .info-box { background: white; border: 1px solid #eee; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; height: 100%; transition: transform 0.2s; text-align: left; }
    .info-box:hover { transform: translateY(-3px); border-color: #008f78; }
    .info-icon { width: 45px; height: 45px; background: #F0FDF4; color: #16A34A; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    
    /* Achievements Section */
    .achieve-box { border-radius: 12px; padding: 25px; text-align: center; border: 1px solid #eee; transition: 0.3s; height: 100%; }
    .achieve-box.unlocked { background: #F0FDF4; border-color: #DCFCE7; }
    .achieve-box.locked { opacity: 0.6; filter: grayscale(1); background: #fafafa; }
</style>

<div class="container-fluid text-start">
    @if(session('success')) <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger border-0 shadow-sm mb-4">{{ session('error') }}</div> @endif

    {{-- 1. HEADER SECTION --}}
    <div class="profile-card shadow-sm">
        <div class="profile-banner"></div>
        <div class="profile-content">
            <div class="avatar-box">
                <div class="avatar-inner">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ substr($user->name, 0, 2) }}
                    @endif
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end pt-3" style="padding-left: 130px;">
                <div>
                    <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                    <p class="text-muted mb-0">Form 4 Student • PAI Class</p>
                </div>
                <a href="{{ route('student.profile.edit') }}" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius: 10px;">
                    <i class="fas fa-pen me-2"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    {{-- 2. INFO BOXES --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon"><i class="far fa-envelope"></i></div>
                <div><small class="text-muted d-block">Email</small><div class="fw-bold text-truncate" style="max-width: 150px;">{{ $user->email }}</div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon"><i class="fas fa-phone"></i></div>
                <div><small class="text-muted d-block">Phone</small><div class="fw-bold">{{ $user->phone_number ?? 'Not Set' }}</div></div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon text-white" style="background-color: #008f78;"><i class="fas fa-mosque"></i></div>
                <div class="w-100">
                    <small class="text-muted d-block mb-1">Prayer Zone (Melaka)</small>
                    {{-- ✅ EXPANDED MELAKA-ONLY PRAYER ZONES --}}
                    <select id="profilePrayerLoc" class="form-select form-select-sm border-0 p-0 fw-bold text-dark bg-transparent">
                        <option value="2.2775,102.1466">📍 Sg. Udang (MRSM)</option>
                        <option value="2.1896,102.2501">📍 Melaka City (Bandar Melaka)</option>
                        <option value="2.3133,102.4309">📍 Jasin</option>
                        <option value="2.3804,102.2089">📍 Alor Gajah</option>
                        <option value="2.3500,102.1100">📍 Masjid Tanah</option>
                        <option value="2.2478,102.2132">📍 Klebang</option>
                        <option value="2.2736,102.2964">📍 Ayer Keroh</option>
                        <option value="2.2270,102.3480">📍 Bemban</option>
                        <option value="2.1384,102.3421">📍 Umbai / Merlimau</option>
                        <option value="2.3020,102.1340">📍 Tanjung Bidara</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon"><i class="far fa-calendar"></i></div>
                <div><small class="text-muted d-block">Joined</small><div class="fw-bold">{{ $user->created_at->format('F Y') }}</div></div>
            </div>
        </div>
    </div>

    {{-- 3. TELEGRAM INTEGRATION --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 me-3 text-primary"><i class="fab fa-telegram-plane fa-2x"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Telegram Notifications</h5>
                    <p class="text-muted small mb-0">
                        @if($user->telegram_chat_id)
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Active</span>
                        @else
                            Connect your account to receive quiz results and reminders.
                        @endif
                    </p>
                </div>
            </div>
            @if($user->telegram_chat_id)
                <form action="{{ route('telegram.unlink') }}" method="POST">@csrf<button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">Disconnect</button></form>
            @else
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#telegramModal">
                    <i class="fas fa-link me-2"></i> Connect Telegram
                </button>
            @endif
        </div>
    </div>

    {{-- 4. ACHIEVEMENTS --}}
    <h4 class="fw-bold mb-4">Achievements</h4>
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="achieve-box unlocked shadow-sm">
                <div class="fs-1 mb-2">🏆</div>
                <h6 class="fw-bold mb-1">Top Performer</h6>
                <small class="text-muted">Highest Score: <strong>{{ $achievements->highest_score }}%</strong></small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->total_quizzes >= 5 ? 'unlocked' : 'locked' }} shadow-sm">
                <div class="fs-1 mb-2">🔥</div>
                <h6 class="fw-bold mb-1">Active Learner</h6>
                <small class="text-muted">{{ $achievements->total_quizzes }}/5 Quizzes Completed</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->perfect_scores >= 1 ? 'unlocked' : 'locked' }} shadow-sm">
                <div class="fs-1 mb-2">⭐</div>
                <h6 class="fw-bold mb-1">Quiz Master</h6>
                <small class="text-muted">{{ $achievements->perfect_scores }} Perfect Scores</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->telegram_status ? 'unlocked' : 'locked' }} shadow-sm">
                <div class="fs-1 mb-2">📱</div>
                <h6 class="fw-bold mb-1">Digital Learner</h6>
                <small class="text-muted">{{ $achievements->telegram_status ? 'Telegram Linked' : 'Link Telegram' }}</small>
            </div>
        </div>
    </div>
</div>

{{{-- TELEGRAM MODAL --}}
@if(!$user->telegram_chat_id)
<div class="modal fade" id="telegramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="modal-header border-0 pb-0 px-0 text-start">
                <h5 class="fw-bold">Connect Telegram</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-0">
                <p class="small text-muted mb-4">
                    Search for <strong class="text-primary">@PAI_MRSM_bot</strong> on Telegram <br>
                    and send this code to <strong class="text-dark">Islamic Learning Bot</strong>
                </p>
                <div class="bg-light p-3 d-inline-block rounded-3 mb-4">
                    <h2 class="mb-0 fw-bold text-dark">{{ $telegramCode }}</h2>
                </div>
                <form action="{{ route('telegram.verify') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">I Have Sent The Code</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selector = document.getElementById('profilePrayerLoc');
        const savedLoc = localStorage.getItem('prayerLoc');
        if(savedLoc) selector.value = savedLoc;
        
        selector.addEventListener('change', function() {
            localStorage.setItem('prayerLoc', this.value);
            this.style.color = '#008f78';
            setTimeout(() => this.style.color = '', 500);
        });
    });
</script>
@endsection