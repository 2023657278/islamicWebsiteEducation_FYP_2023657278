@extends('adminreal.master')

@section('content')
<div class="container-fluid text-light">

    {{-- POPUP SYSTEM ALERTS FOR ERROR OR SUCCESS CONTEXT FEEDBACKS --}}
    @if($errors->has('msg'))
        <div class="alert alert-danger alert-dismissible fade show font-weight-bold border-0 shadow mb-4" role="alert" style="background-color: rgba(220, 53, 69, 0.15); color: #ff6b6b; text-align: left; position: relative; z-index: 10;">
            <i class="fas fa-shield-alt mr-2"></i> {{ $errors->first('msg') }}
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show font-weight-bold border-0 shadow mb-4" role="alert" style="background-color: rgba(40, 167, 69, 0.15); color: #2ecc71; text-align: left; position: relative; z-index: 10;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 1. CONTROL TERMINAL STATUS ROW --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-3 rounded d-flex justify-content-between align-items-center" style="background: #121214; border: 1px solid #27272a;">
                <div class="d-flex align-items-center">
                    <span class="pulse-indicator mr-2"></span>
                    <strong class="text-uppercase tracking-wider small text-muted">Core Engine Status:</strong>
                    <span class="badge badge-success ml-2 py-1 px-2 text-xs">LIVE / ENCRYPTED</span>
                </div>
                <div class="small text-muted font-weight-bold" id="live-timestamp"></div>
            </div>
        </div>
    </div>

    {{-- 2. TELEMETRY STATS CARDS --}}
    <div class="row mb-4">
        @php
            $cards = [
                ['label' => 'Total Registered Students', 'value' => $stats['students'], 'icon' => 'fa-user-graduate', 'color' => '#3b82f6', 'desc' => 'Infinitely scalable database records'],
                ['label' => 'Active Academic Staff', 'value' => $stats['teachers'], 'icon' => 'fa-chalkboard-teacher', 'color' => '#10b981', 'desc' => 'Verified instructor channels'],
                ['label' => 'Allocated Classes', 'value' => $stats['groups'], 'icon' => 'fa-cubes', 'color' => '#f59e0b', 'desc' => 'Active form groups'],
                ['label' => 'Platform Learning Assets', 'value' => $stats['resources'], 'icon' => 'fa-folder-open', 'color' => '#06b6d4', 'desc' => 'Uploaded files & resources']
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 mb-3">
            <div class="card bg-dark h-100 border-secondary shadow-lg card-glow" style="border-left: 4px solid {{ $card['color'] }} !important; background-color: #121214 !important;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-left">
                            <p class="text-uppercase small font-weight-bold text-muted mb-1">{{ $card['label'] }}</p>
                            <h1 class="font-weight-bold mb-0 text-white tracking-tight">{{ $card['value'] }}</h1>
                        </div>
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <i class="fas {{ $card['icon'] }} fa-xl" style="color: {{ $card['color'] }}"></i>
                        </div>
                    </div>
                    <div class="text-left">
                        <small class="text-xs text-muted-dark"><i class="fas fa-chart-line mr-1 text-secondary"></i> {{ $card['desc'] }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. ANALYTICS GRAPHICS SECTION --}}
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card bg-dark border-secondary h-100 shadow" style="background-color: #121214 !important;">
                <div class="card-header border-bottom border-secondary bg-transparent py-3">
                    <h6 class="card-title text-uppercase font-weight-bold mb-0 text-info" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-chart-pie mr-2"></i> Account Density Profile</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div style="position: relative; height:200px;">
                        <canvas id="userRatioChart"></canvas>
                    </div>
                    <div class="row text-center mt-3 text-xs">
                        <div class="col-6 text-primary"><i class="fas fa-circle mr-1"></i> Students ({{ $ratios['student_percentage'] }}%)</div>
                        <div class="col-6 text-success"><i class="fas fa-circle mr-1"></i> Teachers ({{ $ratios['teacher_percentage'] }}%)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="card bg-dark border-secondary h-100 shadow" style="background-color: #121214 !important;">
                <div class="card-header border-bottom border-secondary bg-transparent py-3">
                    <h6 class="card-title text-uppercase font-weight-bold mb-0 text-info" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-sliders-h mr-2"></i> Live Ecosystem Vectors</h6>
                </div>
                <div class="card-body text-left">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted">Classroom Utilization Density (Average Students/Class)</span>
                            <span class="text-warning font-weight-bold">{{ $ratios['average_students_per_class'] }} Users/Unit</span>
                        </div>
                        <div class="progress bg-black-opaque" style="height: 6px; background-color: #1a1a1e;">
                            <div class="progress-bar bg-warning progress-bar-striped" style="width: {{ min(($ratios['average_students_per_class']/40)*100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted">Weekly Active Users Engagement Retention</span>
                            <span class="text-primary font-weight-bold">{{ $systemMetrics['active_sessions'] }} Online</span>
                        </div>
                        <div class="progress bg-black-opaque" style="height: 6px; background-color: #1a1a1e;">
                            <div class="progress-bar bg-primary" style="width: {{ $stats['students'] > 0 ? ($systemMetrics['active_sessions'] / $stats['students']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between text-xs mb-1">
                            <span class="text-muted">Telegram Bot Integration Handshakes</span>
                            <span class="text-info font-weight-bold">{{ $systemMetrics['telegram_linked'] }} Connected</span>
                        </div>
                        <div class="progress bg-black-opaque" style="height: 6px; background-color: #1a1a1e;">
                            <div class="progress-bar bg-info" style="width: {{ ($stats['students'] + $stats['teachers']) > 0 ? ($systemMetrics['telegram_linked'] / ($stats['students'] + $stats['teachers'])) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. SYSTEM MAINTENANCE & UTILITY SECTION --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark border-secondary rounded-3 shadow" style="background-color: #121214 !important; border-color: #27272a !important;">
                <div class="card-header border-bottom border-secondary d-flex align-items-center">
                    <h5 class="card-title font-weight-bold mb-0 text-warning" style="font-size: 1.1rem;">
                        <i class="fas fa-tools mr-2"></i> Root System Maintenance & Database Utilities
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div class="text-left">
                            <h6 class="font-weight-bold text-white mb-1">Purge Active System Timetables</h6>
                            <p class="text-muted text-sm mb-md-0">Wipes out all active class timetable rows currently mapped inside the database schema. Currently holding: <span class="badge badge-primary font-weight-bold px-2 py-1">{{ $stats['timetables'] }} records</span>.</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-danger font-weight-bold px-4" data-toggle="modal" data-target="#doubleVerificationModal">
                                <i class="fas fa-radiation-alt mr-2"></i> Reset Timetables
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. MASTER INFRASTRUCTURE SYSTEM LOGS --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark border-secondary shadow" style="background-color: #121214 !important;">
                <div class="card-header border-bottom border-secondary bg-transparent">
                    <h6 class="card-title text-uppercase font-weight-bold mb-0 text-danger" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-shield-alt mr-2"></i> Master Controller Infrastructure System Logs</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover table-borderless mb-0 text-xs text-muted-dark">
                            <thead style="background: #1e1e24; color: #64748b;">
                                <tr>
                                    <th class="text-left pl-4">SYSTEM VECTOR</th>
                                    <th class="text-left">STATUS FLAG</th>
                                    <th class="text-left">DIAGNOSTIC TELEMETRY VALUE</th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                <tr>
                                    <td class="pl-4">Student Directory Matrix</td>
                                    <td><span class="text-success"><i class="fas fa-check-circle mr-1"></i> Scaling</span></td>
                                    <td>Dynamic Database Index active (Total records: {{ $stats['students'] }})</td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Telegram Webhook Gateway</td>
                                    <td><span class="text-info"><i class="fas fa-sync-alt fa-spin mr-1"></i> Listening</span></td>
                                    <td>Awaiting inbound handshakes ({{ $systemMetrics['telegram_linked'] }} accounts configured)</td>
                                </tr>
                                <tr>
                                    <td class="pl-4">Academic Resource Registry</td>
                                    <td><span class="text-success"><i class="fas fa-check-circle mr-1"></i> Unbounded</span></td>
                                    <td>Storage vector expanding smoothly ({{ $stats['resources'] }} files registered)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin' || auth()->user()->role === 'teacher')
<div class="card shadow mb-4 border-left-success">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-file-upload mr-2"></i>Automated Al-Falah Bank PDF Ingestion Tool</h6>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success small mb-3">{{ session('success') }}</div>
        @endif

        <form action="{{ route('questions.bank.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row align-items-end">
                <div class="col-md-6 mb-2">
                    <label class="small font-weight-bold text-uppercase text-muted">Select Modul Al-Falah PDF File</label>
                    <input type="file" name="pdf_file" class="form-control-file border p-2 rounded bg-light" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold text-uppercase text-muted">Target Subject Mapping ID</label>
                    <input type="number" name="subject_id" class="form-control" value="1" required title="1 usually represents Pendidikan Islam">
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-play mr-1"></i> Trigger Ingestion
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- 🛑 AIR-TIGHT EXTRACTION FIX: Modal moved completely out of structural page flows to override AdminLTE layout trees --}}
<div class="modal fade" id="doubleVerificationModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-light border border-danger" style="border-radius: 12px; background-color: #121214 !important; box-shadow: 0 15px 45px rgba(0,0,0,0.75) !important;">
            <div class="modal-header border-bottom border-secondary bg-transparent">
                <h5 class="modal-title font-weight-bold text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> High-Level Security Reset Required</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('adminreal.timetables.resetAll') }}" method="POST" id="wipeTimetableForm">
                @csrf
                <div class="modal-body text-start">
                    <div class="alert alert-danger bg-transparent border-danger text-danger py-3 text-sm mb-4" style="line-height: 1.5; background-color: rgba(220, 53, 69, 0.05) !important;">
                        <i class="fas fa-info-circle mr-2"></i> <b>CRITICAL WARNING:</b> Executing this command will completely wipe out all classroom scheduling allocation configurations permanently across all teacher and student profile portals. This cannot be undone.
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-xs text-muted font-weight-bold text-uppercase mb-2 d-block text-left">
                            Type <span class="text-danger font-weight-bold">RESET TIMETABLE</span> to confirm deletion:
                        </label>
                        {{-- Forced style elements to ensure focus event capture --}}
                        <input type="text" name="confirmation_text" id="securityPassphraseInput" autocomplete="off"
                               class="form-control font-weight-bold text-md tracking-wide py-3" 
                               style="background-color: #1c1c1f !important; border: 1px solid #495057 !important; color: #ffffff !important; display: block !important; width: 100% !important; opacity: 1 !important; visibility: visible !important;"
                               placeholder="Type the exact phrase here" onkeyup="evaluateSecurityTier(this.value)">
                    </div>
                </div>
                
                <div class="modal-footer border-top border-secondary bg-transparent">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="finalWipeSubmitButton" class="btn btn-danger btn-sm px-4 font-weight-bold" disabled>
                        Confirm Final Purge <i class="fas fa-radiation-alt ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global functional space matching interceptor
    function evaluateSecurityTier(inputValue) {
        const targetString = "RESET TIMETABLE";
        const submitBtn = document.getElementById('finalWipeSubmitButton');
        
        if (inputValue.trim() === targetString) {
            submitBtn.removeAttribute('disabled');
            submitBtn.classList.remove('btn-danger');
            submitBtn.classList.add('btn-success'); 
        } else {
            submitBtn.setAttribute('disabled', 'true');
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-danger');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        function clockTicker() {
            const timeStr = new Date().toLocaleTimeString('en-US', { hour12: false });
            const dateStr = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', weekday: 'short' });
            document.getElementById('live-timestamp').innerHTML = `<span class="text-secondary mr-2">${dateStr}</span> <span class="text-white font-weight-bold">${timeStr}</span>`;
        }
        setInterval(clockTicker, 1000); clockTicker();

        const ctx = document.getElementById('userRatioChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Teachers'],
                datasets: [{
                    data: [{{ $stats['students'] }}, {{ $stats['teachers'] }}],
                    backgroundColor: ['#3b82f6', '#10b981'],
                    borderColor: '#121214',
                    borderWidth: 4,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
        
        // 🟢 FIXED: Force modal append extraction directly under body to remove pointer-events blocks
        $('#doubleVerificationModal').appendTo("body");
    });
</script>

<style>
    .card-glow:hover {
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15) !important;
    }
    .text-muted-dark { color: #64748b !important; }
    .pulse-indicator {
        width: 8px; height: 8px; background-color: #10b981; border-radius: 50%; display: inline-block;
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    /* 🟢 FORCE MODAL FIX: Override AdminLTE background layers block */
    .modal-backdrop {
        z-index: 1040 !important;
    }
    #doubleVerificationModal {
        z-index: 1050 !important;
    }
    #securityPassphraseInput {
        cursor: text !important;
    }
</style>
@endsection