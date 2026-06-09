<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battle Over | Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');

        body { 
            background: #020617; 
            color: white; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .results-container {
            width: 100%;
            max-width: 700px;
            perspective: 1000px;
        }

        .results-card { 
            background: #0f172a; 
            border-radius: 40px; 
            padding: 40px; 
            border: 4px solid #1e293b; 
            text-align: center; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
            animation: slideIn 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(50px) rotateX(-10deg); }
            to { opacity: 1; transform: translateY(0) rotateX(0); }
        }

        /* 🏆 PODIUM THEMES */
        .rank-item { 
            background: rgba(255,255,255,0.03); 
            border-radius: 20px; 
            padding: 18px 25px; 
            margin-bottom: 12px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
        }

        .rank-1 { 
            background: linear-gradient(90deg, rgba(251, 191, 36, 0.15), rgba(251, 191, 36, 0.05));
            border: 2px solid #fbbf24; 
            transform: scale(1.05);
            margin-bottom: 25px;
        }

        .rank-2 { border-left: 5px solid #94a3b8; }
        .rank-3 { border-left: 5px solid #b45309; }

        .crown-icon { color: #fbbf24; filter: drop-shadow(0 0 10px rgba(251, 191, 36, 0.5)); }
        
        .warrior-name { font-size: 1.2rem; font-weight: 800; }
        .rank-badge { 
            width: 35px; height: 35px; border-radius: 50%; 
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 900; margin-right: 15px;
        }

        .status-pill {
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 900;
        }

        .bg-winner { background: #064e3b; color: #34d399; border: 1px solid #34d399; }
        .bg-eliminated { background: #450a0a; color: #f87171; border: 1px solid #f87171; }
        .bg-surrendered { background: #1e293b; color: #94a3b8; border: 1px solid #94a3b8; }

        .leaderboard-scroll {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
            margin-top: 20px;
        }

        .leaderboard-scroll::-webkit-scrollbar { width: 6px; }
        .leaderboard-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

        .btn-home {
            background: #fbbf24;
            color: #000;
            font-weight: 900;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.4);
            background: #fcd34d;
            color: #000;
        }
    </style>
</head>
<body>

<div class="results-container">
    <div class="results-card">
        <div class="mb-4">
            <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
            <h1 class="fw-black text-white mb-0">BATTLE RESULTS</h1>
            <p class="text-muted">Room Code: <span class="text-primary fw-bold">{{ $room->room_code }}</span></p>
        </div>

        <div class="leaderboard-scroll">
            {{-- 🟢 SURGICAL FIX: Sort elements explicitly by health descending so index matching works flawlessly --}}
            @foreach($participants->sortByDesc('hp')->values() as $index => $p)
                @php
                    $rank = $index + 1;
                    $isWinner = ($rank === 1);
                    $statusClass = '';
                    $statusLabel = '';

                    if ($p->status === 'surrendered') {
                        $statusClass = 'bg-surrendered';
                        $statusLabel = 'SURRENDERED';
                    } elseif ($p->hp <= 0) {
                        $statusClass = 'bg-eliminated';
                        $statusLabel = 'ELIMINATED';
                    } else {
                        $statusClass = 'bg-winner';
                        $statusLabel = 'CHAMPION';
                    }

                    // 🟢 SURGICAL FIX: Exact alignment with controller points-pooling logic rules
                    $diff = strtolower($room->quiz->difficulty);
                    $playerCount = $participants->count();
                    $points = 0;

                    // Pool Range Tier A: 1 to 4 Players
                    if ($playerCount >= 1 && $playerCount <= 4) {
                        if ($diff == 'easy') {
                            $points = ($isWinner) ? 15 : -5;
                        } elseif ($diff == 'medium') {
                            $points = ($isWinner) ? 30 : -15;
                        } elseif ($diff == 'hard') {
                            $points = ($isWinner) ? 70 : -50;
                        }
                    } 
                    // Pool Range Tier B: 5 to 20 Players
                    else if ($playerCount >= 5 && $playerCount <= 20) {
                        if ($diff == 'easy') {
                            if ($rank === 1) $points = 20;
                            elseif ($rank === 2) $points = 15;
                            elseif ($rank === 3) $points = 10;
                            else $points = -5;
                        } elseif ($diff == 'medium') {
                            if ($rank === 1) $points = 45;
                            elseif ($rank === 2) $points = 30;
                            elseif ($rank === 3) $points = 20;
                            else $points = -15;
                        } elseif ($diff == 'hard') {
                            if ($rank === 1) $points = 100;
                            elseif ($rank === 2) $points = 70;
                            elseif ($rank === 3) $points = 50;
                            else $points = -50;
                        }
                    }
                @endphp

                <div class="rank-item {{ $isWinner ? 'rank-1' : 'rank-' . ($rank <= 3 ? $rank : 'default') }}">
                    <div class="d-flex align-items-center">
                        <div class="rank-badge {{ $isWinner ? 'bg-warning text-dark' : 'bg-primary text-white' }}">
                            {{ $rank }}
                        </div>
                        <div class="text-start">
                            <div class="warrior-name">
                                @if($isWinner) <i class="fas fa-crown crown-icon me-1"></i> @endif
                                {{ strtoupper($p->user->name) }}
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-heart text-danger me-1"></i> {{ $p->hp }} HP Remaining
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="status-pill {{ $statusClass }} mb-1">
                            {{ $statusLabel }}
                        </div>
                        <div class="small fw-bold text-info">
                            {{ $points > 0 ? '+' : '' }}{{ $points }} PTS
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            <a href="{{ route('student.quizzes.index') }}" class="btn-home">
                <i class="fas fa-house me-2"></i> RETURN TO LOBBY
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>