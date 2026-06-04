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
    .info-box { background: white; border: 1px solid #eee; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; height: 100%; transition: transform 0.2s, border-color 0.2s; text-align: left; }
    .info-box:hover { transform: translateY(-3px); border-color: #008f78; }
    .info-icon { width: 45px; height: 45px; background: #F0FDF4; color: #16A34A; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    
    /* Gamified Achievements Styling */
    .achieve-box { border-radius: 16px; padding: 25px; text-align: center; border: 1px solid #e2e8f0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%; position: relative; background: #ffffff; }
    .achieve-box:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
    .achieve-box.unlocked { background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-color: #bbf7d0; }
    .achieve-box.locked { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); opacity: 0.65; border-color: #e2e8f0; }
    .achieve-badge-status { position: absolute; top: 12px; right: 12px; font-size: 0.85rem; }
    .achieve-progress-track { height: 6px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; }
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
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" style="width: 500%; height: 500%; object-fit: cover;">
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

    {{-- 2. INFO BOXES GRID --}}
    <div class="row g-4 mb-4">
        {{-- Box A: Identity Matrix / Email --}}
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon"><i class="far fa-envelope"></i></div>
                <div><small class="text-muted d-block">Email</small><div class="fw-bold text-truncate" style="max-width: 150px;" title="{{ $user->email }}">{{ $user->email }}</div></div>
            </div>
        </div>

        {{-- Box B: Contact Vector --}}
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon"><i class="fas fa-phone"></i></div>
                <div><small class="text-muted d-block">Phone</small><div class="fw-bold">{{ $user->phone_number ?? 'Not Set' }}</div></div>
            </div>
        </div>
        
        {{-- Box C: Dynamic Malaysia Travel Prayer Selector Gateway --}}
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon text-white" style="background-color: #008f78;"><i class="fas fa-mosque"></i></div>
                <div class="w-100">
                    <small class="text-muted d-block mb-1">Prayer Zone State Selection</small>
                    <select id="profilePrayerLoc" class="form-select form-select-sm border-0 p-0 fw-bold text-dark bg-transparent" style="box-shadow: none; cursor: pointer;">
                        <option value="2.2775,102.1466">📍 Melaka (Sg. Udang MRSM)</option>
                        <option value="3.1319,101.6841">📍 Kuala Lumpur / Putrajaya</option>
                        <option value="1.4927,103.7414">📍 Johor (Johor Bahru)</option>
                        <option value="6.1184,100.3686">📍 Kedah (Alor Setar)</option>
                        <option value="6.1254,102.2386">📍 Kelantan (Kota Bharu)</option>
                        <option value="2.7258,101.9424">📍 Negeri Sembilan (Seremban)</option>
                        <option value="3.8126,103.3256">📍 Pahang (Kuantan)</option>
                        <option value="4.5921,101.0901">📍 Perak (Ipoh)</option>
                        <option value="6.4449,100.2048">📍 Perlis (Kangar)</option>
                        <option value="5.4141,100.3288">📍 Pulau Pinang (George Town)</option>
                        <option value="1.5533,110.3592">📍 Sarawak (Kuching)</option>
                        <option value="5.9788,116.0753">📍 Sabah (Kota Kinabalu)</option>
                        <option value="3.0738,101.5183">📍 Selangor (Shah Alam)</option>
                        <option value="5.3302,103.1408">📍 Terengganu (Kuala Terengganu)</option>
                        <option value="5.2831,115.2443">📍 Labuan</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Box D: Academic No. Maktab & Enrolled Matrix --}}
        <div class="col-md-3">
            <div class="info-box shadow-sm">
                <div class="info-icon" style="background-color: #eff6ff; color: #3b82f6;"><i class="fas fa-id-card"></i></div>
                <div>
                    <small class="text-muted d-block">No. Maktab Tracking ID</small>
                    <div class="fw-bold text-primary mb-0" style="font-size: 1.05rem;">{{ $user->no_maktab ?? 'N/A' }}</div>
                    <span class="text-muted" style="font-size: 0.72rem;">Joined: {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. TELEGRAM NOTIFICATIONS HUB --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 me-3 text-primary"><i class="fab fa-telegram-plane fa-2x"></i></div>
                <div>
                    <h5 class="fw-bold mb-1">Telegram System Notifications Channel</h5>
                    <p class="text-muted small mb-0">
                        @if($user->telegram_chat_id)
                            <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Sync Connection Active</span>
                        @else
                            Connect your account to receive quiz results and reminders directly to your device.
                        @endif
                    </p>
                </div>
            </div>
            @if($user->telegram_chat_id)
                <form action="{{ route('telegram.unlink') }}" method="POST">@csrf<button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">Disconnect Link</button></form>
            @else
                <a href="{{ route('telegram.connect') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fab fa-telegram-plane me-2"></i> Connect Telegram
                </a>
            @endif
        </div>
    </div>

    {{-- 4. GAMIFIED INSIGHT ACHIEVEMENTS SECTION --}}
    <h4 class="fw-bold mb-4"><i class="fas fa-medal text-warning mr-2 me-2"></i>Unlocked System Accomplishments</h4>
    <div class="row g-4 mb-5">
        {{-- Achievement 1: Score Leaderboard --}}
        <div class="col-md-3">
            <div class="achieve-box unlocked shadow-sm">
                <span class="achieve-badge-status text-success"><i class="fas fa-lock-open"></i></span>
                <div class="fs-1 mb-2" style="font-size: 2.8rem;">🏆</div>
                <h6 class="fw-bold text-dark mb-1">Top Performer</h6>
                <p class="text-xs text-muted mb-3" style="font-size: 0.75rem;">Secure a high grade on quiz modules</p>
                <div class="bg-light rounded p-2 text-sm font-weight-bold">
                    Record: <strong class="text-success">{{ $achievements->highest_score }}%</strong>
                </div>
            </div>
        </div>

        {{-- Achievement 2: Engagement Activity Progress Tracker --}}
        @php $quizProgressPercent = min(($achievements->total_quizzes / 5) * 100, 100); @endphp
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->total_quizzes >= 5 ? 'unlocked' : 'locked' }} shadow-sm">
                <span class="achieve-badge-status {{ $achievements->total_quizzes >= 5 ? 'text-success' : 'text-muted' }}">
                    <i class="fas {{ $achievements->total_quizzes >= 5 ? 'fa-lock-open' : 'fa-lock' }}"></i>
                </span>
                <div class="fs-1 mb-2" style="font-size: 2.8rem;">🔥</div>
                <h6 class="fw-bold text-dark mb-1">Active Learner</h6>
                <p class="text-xs text-muted mb-3" style="font-size: 0.75rem;">Complete 5 quiz evaluations</p>
                
                <div class="w-100 mt-2">
                    <div class="d-flex justify-content-between text-xs mb-1" style="font-size: 0.72rem;">
                        <span class="text-muted">Progress Parameters</span>
                        <strong>{{ $achievements->total_quizzes }} / 5</strong>
                    </div>
                    <div class="achieve-progress-track">
                        <div class="progress-bar bg-success" style="width: {{ $quizProgressPercent }}%; height: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Achievement 3: Mastery Vector --}}
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->perfect_scores >= 1 ? 'unlocked' : 'locked' }} shadow-sm">
                <span class="achieve-badge-status {{ $achievements->perfect_scores >= 1 ? 'text-success' : 'text-muted' }}">
                    <i class="fas {{ $achievements->perfect_scores >= 1 ? 'fa-lock-open' : 'fa-lock' }}"></i>
                </span>
                <div class="fs-1 mb-2" style="font-size: 2.8rem;">⭐</div>
                <h6 class="fw-bold text-dark mb-1">Quiz Master</h6>
                <p class="text-xs text-muted mb-3" style="font-size: 0.75rem;">Earn a perfect score sheet mark</p>
                <div class="badge {{ $achievements->perfect_scores >= 1 ? 'badge-success bg-success text-white' : 'badge-secondary bg-secondary text-white' }} px-3 py-1.5 rounded-pill">
                    {{ $achievements->perfect_scores }} Unlocked Tier
                </div>
            </div>
        </div>

        {{-- Achievement 4: Channel Link Handshake --}}
        <div class="col-md-3">
            <div class="achieve-box {{ $achievements->telegram_status ? 'unlocked' : 'locked' }} shadow-sm">
                <span class="achieve-badge-status {{ $achievements->telegram_status ? 'text-success' : 'text-muted' }}">
                    <i class="fas {{ $achievements->telegram_status ? 'fa-lock-open' : 'fa-lock' }}"></i>
                </span>
                <div class="fs-1 mb-2" style="font-size: 2.8rem;">📱</div>
                <h6 class="fw-bold text-dark mb-1">Digital Learner</h6>
                <p class="text-xs text-muted mb-3" style="font-size: 0.75rem;">Pair profile with Telegram Bot</p>
                <span class="text-xs font-weight-bold d-block mt-2 {{ $achievements->telegram_status ? 'text-success' : 'text-muted' }}">
                    <i class="fas {{ $achievements->telegram_status ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                    {{ $achievements->telegram_status ? 'Gateway Linked' : 'Link Pending' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- TELEGRAM HUB MODAL --}}
@if(!$user->telegram_chat_id)
<div class="modal fade" id="telegramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4">
            <div class="modal-header border-0 pb-0 px-0 text-start">
                <h5 class="fw-bold">Connect Telegram Gateway Channel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center px-0">
                <p class="small text-muted mb-4">
                    Search for <strong class="text-primary">@PAI_MRSM_bot</strong> on Telegram <br>
                    and send this code parameters to the <strong class="text-dark">Islamic Learning Bot</strong>
                </p>
                <div class="bg-light p-3 d-inline-block rounded-3 mb-4">
                    <h2 class="mb-0 fw-bold text-dark tracking-wider">{{ $telegramCode ?? 'WAITING' }}</h2>
                </div>
                <form action="{{ route('telegram.verify') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">I Have Sent The Verification Code</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 5. LOCAL STORAGE SYNC STORAGE EXECUTION SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pSelect = document.getElementById('profilePrayerLoc');

    // 1. Check if an active selection footprint exists inside LocalStorage
    if(localStorage.getItem('prayerLoc')) {
        pSelect.value = localStorage.getItem('prayerLoc');
    }

    // 2. Add change listener to store updates dynamically when user travels states
    pSelect.addEventListener('change', function() {
        localStorage.setItem('prayerLoc', this.value);
        
        // Optional Event dispatch alert: If a student updates their zone from the profile page, 
        // this keeps values persistent globally across dashboard widgets if opened in another tab.
        if (typeof fetchPrayerTimes === "function") {
            const coords = this.value.split(',');
            fetchPrayerTimes(coords[0], coords[1]);
        }
    });
});
</script>
@endsection