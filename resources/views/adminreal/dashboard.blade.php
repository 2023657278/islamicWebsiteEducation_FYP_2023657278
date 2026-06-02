@extends('adminreal.master')

@section('content')
<div class="container-fluid text-light">

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
                        <div>
                            <p class="text-uppercase small font-weight-bold text-muted mb-1">{{ $card['label'] }}</p>
                            <h1 class="font-weight-bold mb-0 text-white tracking-tight">{{ $card['value'] }}</h1>
                        </div>
                        <div class="p-3 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <i class="fas {{ $card['icon'] }} fa-xl" style="color: {{ $card['color'] }}"></i>
                        </div>
                    </div>
                    <small class="text-xs text-muted-dark"><i class="fas fa-chart-line mr-1 text-secondary"></i> {{ $card['desc'] }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 3. ANALYTICS GRAPHICS SECTION --}}
    <div class="row mb-4">
        {{-- Doughnut Chart: Account Distribution Ratio --}}
        <div class="col-lg-4 mb-3">
            <div class="card bg-dark border-secondary h-100 shadow" style="background-color: #121214 !important;">
                <div class="card-header border-bottom border-secondary bg-transparent py-3">
                    <h6 class="card-title text-uppercase font-weight-bold mb-0 text-info" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-pie-chart mr-2"></i> Account Density Profile</h6>
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

        {{-- Dynamic Density Benchmarks --}}
        <div class="col-lg-8 mb-3">
            <div class="card bg-dark border-secondary h-100 shadow" style="background-color: #121214 !important;">
                <div class="card-header border-bottom border-secondary bg-transparent py-3">
                    <h6 class="card-title text-uppercase font-weight-bold mb-0 text-info" style="font-size: 0.8rem; letter-spacing: 1px;"><i class="fas fa-sliders-h mr-2"></i> Live Ecosystem Vectors</h6>
                </div>
                <div class="card-body">
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

    {{-- 4. BOTTOM ROW: TELEMETRY ENGINE & SECURITY LOGS --}}
    <div class="row">
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
                                    <th>SYSTEM VECTOR</th>
                                    <th>STATUS FLAG</th>
                                    <th>DIAGNOSTIC TELEMETRY VALUE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Student Directory Matrix</td>
                                    <td><span class="text-success"><i class="fas fa-check-circle mr-1"></i> Scaling</span></td>
                                    <td>Dynamic Database Index active (Total records: {{ $stats['students'] }})</td>
                                </tr>
                                <tr>
                                    <td>Telegram Webhook Gateway</td>
                                    <td><span class="text-info"><i class="fas fa-sync-alt fa-spin mr-1"></i> Listening</span></td>
                                    <td>Awaiting inbound handshakes ({{ $systemMetrics['telegram_linked'] }} accounts configured)</td>
                                </tr>
                                <tr>
                                    <td>Academic Resource Registry</td>
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

{{-- Chart.js and Custom Engine Execution scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Live System Clock Logic
        function clockTicker() {
            const timeStr = new Date().toLocaleTimeString('en-US', { hour12: false });
            const dateStr = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', weekday: 'short' });
            document.getElementById('live-timestamp').innerHTML = `<span class="text-secondary mr-2">${dateStr}</span> <span class="text-white font-weight-bold">${timeStr}</span>`;
        }
        setInterval(clockTicker, 1000); clockTicker();

        // 2. High Tech Doughnut Metric execution
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
    });
</script>

<style>
    /* Styling adjustments to match high-tech tech design matrix */
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
</style>
@endsection