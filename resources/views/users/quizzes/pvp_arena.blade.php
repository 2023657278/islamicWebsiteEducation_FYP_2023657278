<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combat Arena | {{ $room->room_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background: #020617; color: white; min-height: 100vh; padding: 20px; }
        .battle-card { background: #0f172a; border-radius: 30px; padding: 40px; border: 4px solid #1e293b; height: 100%; }
        .hp-bar { height: 25px; background: #334155; border-radius: 50px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3); }
        .hp-fill { height: 100%; background: linear-gradient(90deg, #ef4444, #b91c1c); transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .option-btn { 
            background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.1); 
            color: white; padding: 20px; border-radius: 20px; transition: 0.3s; text-align: left; font-weight: 600;
        }
        .option-btn:hover:not(:disabled) { background: rgba(255,255,255,0.1); transform: scale(1.02); border-color: #3b82f6; }
        .mini-warrior { display: flex; align-items: center; gap: 15px; background: rgba(30, 41, 59, 0.5); padding: 12px; border-radius: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row g-4">
        {{-- PLAYER FOCUS --}}
        <div class="col-lg-8">
            <div class="battle-card shadow-2xl">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <div>
                        <h1 class="fw-black text-warning mb-0">{{ strtoupper(Auth::user()->name) }}</h1>
                        <span class="text-muted small">LEVEL {{ strtoupper($room->quiz->difficulty) }} WARRIOR</span>
                    </div>
                    <div style="width: 350px;">
                        <div class="d-flex justify-content-between mb-1"><small class="fw-bold">HEALTH</small><small id="hpVal">{{ $me->hp }}%</small></div>
                        <div class="hp-bar mb-3"><div id="myHp" class="hp-fill" style="width: {{ $me->hp }}%"></div></div>
                    </div>
                </div>

                <div id="gameplayArea">
                    <div class="text-center py-5" id="waitingArea">
                        <div class="spinner-border text-warning mb-3" style="width: 3rem; height: 3rem;"></div>
                        <h2 class="fw-bold">Awaiting Host Command...</h2>
                    </div>

                    <div id="questionArea" style="display: none;">
                        <h2 id="qText" class="fw-bold mb-5 lh-base"></h2>
                        <div id="optionsGrid" class="d-grid gap-3"></div>
                    </div>
                </div>
            </div>
            
            @if(Auth::id() === $room->host_id)
            <div class="mt-4 text-center">
                <button onclick="triggerNextRound()" class="btn btn-warning btn-xl px-5 rounded-pill fw-black shadow-lg">
                    EXECUTE NEXT ROUND <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
            @endif
        </div>

        {{-- LIVE LEADERBOARD --}}
        <div class="col-lg-4">
            <div class="p-4 bg-slate-900 rounded-4 h-100 border border-slate-800">
                <h4 class="fw-bold mb-4 text-primary"><i class="fas fa-crosshairs me-2"></i> Targeted Warriors</h4>
                <div id="warriorList">
                    @foreach($room->participants as $p)
                        <div class="mini-warrior" id="p-{{ $p->user_id }}">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($p->user->name) }}&background=random" class="rounded-circle" width="45">
                            <div class="flex-grow-1">
                                <div class="small fw-bold d-flex justify-content-between">
                                    <span>{{ $p->user->name }}</span>
                                    <span class="hp-text">{{ $p->hp }}%</span>
                                </div>
                                <div class="hp-bar mt-1" style="height: 6px;"><div class="hp-fill" style="width: {{ $p->hp }}%"></div></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const roomCode = "{{ $room->room_code }}";
    const quizData = @json($room->quiz->questions);
    let currentIdx = -1;

    function syncArena() {
        fetch(`/student/quizzes/pvp/${roomCode}/status`)
            .then(res => res.json())
            .then(data => {
                if (data.current_index > currentIdx) {
                    currentIdx = data.current_index;
                    displayQuestion(currentIdx);
                }
                data.participants.forEach(p => {
                    const row = document.getElementById(`p-${p.user_id}`);
                    if (row) {
                        row.querySelector('.hp-fill').style.width = p.hp + "%";
                        row.querySelector('.hp-text').innerText = p.hp + "%";
                    }
                    if (p.user_id == {{ Auth::id() }}) {
                        document.getElementById('myHp').style.width = p.hp + "%";
                        document.getElementById('hpVal').innerText = p.hp + "%";
                    }
                });
            });
    }

    function displayQuestion(idx) {
        if (!quizData[idx]) return;
        const q = quizData[idx];
        document.getElementById('waitingArea').style.display = 'none';
        document.getElementById('questionArea').style.display = 'block';
        document.getElementById('qText').innerText = q.question_text;
        
        const grid = document.getElementById('optionsGrid');
        grid.innerHTML = '';
        q.options.forEach(opt => {
            const btn = document.createElement('button');
            btn.className = 'option-btn';
            btn.innerHTML = opt.option_text;
            btn.onclick = () => submitStrike(q.id, opt.id, btn);
            grid.appendChild(btn);
        });
    }

    async function submitStrike(qId, optId, btn) {
        document.querySelectorAll('.option-btn').forEach(b => b.disabled = true);
        const res = await fetch(`/student/quizzes/pvp/${roomCode}/strike`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ question_id: qId, answer: optId })
        });
        const data = await res.json();
        btn.style.background = data.is_correct ? '#059669' : '#dc2626';
    }

    function triggerNextRound() {
        fetch(`/student/quizzes/pvp/${roomCode}/next`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    }

    setInterval(syncArena, 2000);
</script>
</body>
</html>