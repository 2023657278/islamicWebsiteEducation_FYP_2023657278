<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elite PVP Arena | {{ $room->room_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        
        body { background: #020617; color: white; font-family: 'Inter', sans-serif; height: 100vh; overflow: hidden; padding-top: 10px; }
        
        /* 🏆 MAIN BATTLE STATION */
        .battle-card { 
            background: #0f172a; border-radius: 30px; border: 4px solid #1e293b; padding: 20px 25px; 
            position: relative; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
            height: 620px; display: flex; flex-direction: column;
            box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        }

        /* 💀 ELIMINATION OVERLAY */
        #eliminatedOverlay {
            display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.95); z-index: 2000; border-radius: 26px;
            align-items: center; justify-content: center; flex-direction: column; text-align: center;
            backdrop-filter: blur(8px);
        }

        /* ✨ ABILITY THEMES */
        .theme-shield { background: #064e3b !important; border-color: #10b981 !important; box-shadow: 0 0 40px rgba(16, 185, 129, 0.4); }
        .theme-boost { background: #450a0a !important; border-color: #ef4444 !important; box-shadow: 0 0 40px rgba(239, 68, 68, 0.4); }
        .theme-freeze { background: #0c4a6e !important; border-color: #3b82f6 !important; }
        .theme-heal { background: #422006 !important; border-color: #fbbf24 !important; }

        .power-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 15px; }
        .pwr-btn { 
            border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px 2px; 
            color: white; font-weight: 900; font-size: 0.65rem; transition: 0.2s; opacity: 0.15; 
            pointer-events: none; background: rgba(255,255,255,0.05); text-transform: uppercase;
        }
        .pwr-btn.active { opacity: 1 !important; pointer-events: auto !important; transform: translateY(-2px); border-color: white; cursor: pointer; }
        .bg-heal { background: #fbbf24 !important; color: #000 !important; }
        .bg-shield { background: #10b981 !important; }
        .bg-boost { background: #ef4444 !important; }
        .bg-freeze { background: #3b82f6 !important; }

        #optionsGrid { overflow-y: auto; flex-grow: 1; max-height: 260px; padding-right: 8px; margin-bottom: 10px; }
        #optionsGrid::-webkit-scrollbar { width: 5px; }
        #optionsGrid::-webkit-scrollbar-thumb { background: #fbbf24; border-radius: 10px; }

        .option-card { 
            border: 2px solid #334155; border-radius: 15px; padding: 12px 20px; cursor: pointer; 
            transition: 0.2s; background: #1e293b; color: white; width: 100%; text-align: left; 
            margin-bottom: 8px; font-weight: 700; display: flex; align-items: center; font-size: 1rem;
        }
        .option-card.selected { border-color: #fbbf24; background: rgba(251, 191, 36, 0.1); }
        .option-card.correct { border-color: #10b981 !important; background: rgba(16, 185, 129, 0.3) !important; }
        .option-card.incorrect { border-color: #ef4444 !important; background: rgba(239, 68, 68, 0.3) !important; }

        .hp-fill { height: 100%; background: linear-gradient(90deg, #ef4444, #b91c1c); transition: width 0.4s ease; border-radius: 50px; }
        .mp-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); transition: width 0.4s ease; border-radius: 50px; }
        .timer-line { height: 8px; background: #fbbf24; width: 100%; transition: width 1s linear; border-radius: 10px; }

        .rank-item { background: rgba(255,255,255,0.03); border-radius: 12px; padding: 10px; margin-bottom: 8px; border-left: 4px solid #3b82f6; transition: 0.3s; }
        .rank-item.is-me { border-left-color: #fbbf24; background: rgba(251, 191, 36, 0.05); }
        .rank-item.is-dead { border-left-color: #ef4444; opacity: 0.5; filter: grayscale(1); }
        
        .game-overlay { display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; border-radius: 30px; align-items: center; justify-content: center; flex-direction: column; text-align: center; backdrop-filter: blur(5px); }
        .is-frozen-state #frozenOverlay { display: flex; }
        .fib-ans { background: rgba(0,0,0,0.5); color: #fbbf24; text-align: center; border: 3px solid #334155; padding: 12px; border-radius: 20px; font-size: 2.2rem; width: 100%; font-weight: 900; outline: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="battle-card" id="arenaCard">
                
                <div id="eliminatedOverlay">
                    <i class="fas fa-skull-crossbones fa-5x text-danger mb-3"></i>
                    <h1 class="fw-black text-white">YOU HAVE FALLEN!</h1>
                    <h3 class="text-muted">FINAL RANK: <span id="myFinalRank" class="text-danger">#?</span></h3>
                    <p class="mt-2 text-white-50">Spectating remaining warriors...</p>
                </div>

                <div id="frozenOverlay" class="game-overlay">
                    <h1 class="display-2 fw-black text-info">FROZEN</h1>
                    <div id="freezeCountdown" class="fs-2 fw-bold text-white"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-warning fw-black mb-0">{{ strtoupper(Auth::user()->name) }}</h3>
                    <div class="text-end">
                        <a href="{{ route('student.quizzes.pvp.surrender', $room->room_code) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-0 mb-1 fw-black" style="font-size: 0.7rem;">SURRENDER</a>
                        <div class="progress mb-1" style="width: 180px; height: 16px; border-radius: 50px; background: rgba(0,0,0,0.5);">
                            <div id="myHp" class="hp-fill"></div>
                        </div>
                        <div class="progress" style="width: 180px; height: 8px; border-radius: 50px; background: rgba(0,0,0,0.5);">
                            <div id="myMp" class="mp-fill"></div>
                        </div>
                    </div>
                </div>

                <div class="power-grid">
                    <button id="p-heal" onclick="castPower('heal')" class="pwr-btn bg-heal">HEAL (80)</button>
                    <button id="p-shield" onclick="castPower('shield')" class="pwr-btn bg-shield">SHIELD (60)</button>
                    <button id="p-freeze" onclick="castPower('freeze')" class="pwr-btn bg-freeze">FREEZE (40)</button>
                    <button id="p-boost" onclick="castPower('boost')" class="pwr-btn bg-boost">BOOST (20)</button>
                </div>

                <div class="timer-line" id="timerFill"></div>
                <div class="text-center my-1 fw-black small">TIME: <span id="timeSec" class="text-warning">60</span>S | <span id="qTypeBadge" class="badge bg-primary"></span></div>

                <div id="qArea">
                    <h4 id="qText" class="fw-bold text-center mb-3 px-2" style="min-height: 45px; font-size: 1.1rem;"></h4>
                    <div id="optionsGrid" class="row row-cols-1 g-2"></div>
                    <div id="fibArea" class="mb-3"></div>

                    <button id="submitBtn" onclick="submitAns()" class="btn btn-warning w-100 py-3 mt-auto fw-black rounded-pill shadow-lg border-0 fs-5">
                        EXECUTE STRIKE ⚡
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-3 bg-dark rounded-4 border border-secondary h-100 shadow-lg" style="overflow-y: auto;">
                <h6 class="text-primary mb-3 fw-black"><i class="fas fa-bolt me-2"></i>WARRIOR STANDINGS</h6>
                <div id="warriorList"></div>
            </div>
        </div>
    </div>
</div>

<script>
    const roomCode = "{{ $room->room_code }}";
    const baseURL = "/student/quizzes/pvp/{{ $room->room_code }}";
    const resultsURL = "{{ route('student.quizzes.pvp.results', $room->room_code) }}";
    
    let quizBank = @json($room->quiz->questions); 
    let currentIdx = 0, timer, timeLeft = 60, selectedIds = [];
    let isFrozen = false, feedbackActive = false, spellUsedThisTurn = false, isDead = false;

    function getQ() { 
        if (currentIdx >= quizBank.length) { 
            currentIdx = 0; 
            quizBank.sort(() => Math.random() - 0.5); 
        } 
        return quizBank[currentIdx]; 
    }

    async function sync() {
        try {
            const r = await fetch(`${baseURL}/status`);
            const data = await r.json();
            
            // 1. VICTORY REDIRECT
            if (data.status === 'finished' && !feedbackActive) { 
                window.location.href = resultsURL; 
                return; 
            }
            
            const me = data.participants.find(p => p.user_id == {{ Auth::id() }});
            if (!me) return;

            const arena = document.getElementById('arenaCard');

            // 💀 ELIMINATION CHECK
            if (me.status === 'defeated' || me.hp <= 0) {
                isDead = true;
                document.getElementById('eliminatedOverlay').style.display = 'flex';
                document.getElementById('myFinalRank').innerText = `#${me.rank || '?'}`;
                document.getElementById('submitBtn').disabled = true;
                clearInterval(timer); // Stop timer logic locally
            }

            arena.classList.toggle('theme-shield', me.is_shielded);
            arena.classList.toggle('theme-boost', me.active_boost);

            // 🔒 STRIKE LOCK UI
            const sBtn = document.getElementById('submitBtn');
            if (me.strike_locked && !isDead) {
                sBtn.disabled = true;
                sBtn.innerText = "RECOVERING...";
                sBtn.classList.add('strike-btn-locked');
            } else if (!feedbackActive && !isDead) {
                sBtn.disabled = false;
                sBtn.innerText = "EXECUTE STRIKE ⚡";
                sBtn.classList.remove('strike-btn-locked');
            }

            // ❄️ FREEZE COUNTDOWN UI
            if (me.is_frozen) {
                isFrozen = true; 
                arena.classList.add('is-frozen-state', 'theme-freeze');
                const diff = Math.max(0, Math.ceil((new Date(me.frozen_until) - new Date()) / 1000));
                document.getElementById('freezeCountdown').innerText = diff + "s";
            } else { 
                isFrozen = false; 
                arena.classList.remove('is-frozen-state', 'theme-freeze'); 
            }

            // HUD BARS (DYNAMIC SCALING)
            const maxHp = data.participants.length * 50; 
            document.getElementById('myHp').style.width = (me.hp / maxHp * 100) + "%";
            document.getElementById('myMp').style.width = me.mp + "%";
            
            ['heal', 'shield', 'freeze', 'boost'].forEach(p => {
                const btn = document.getElementById(`p-${p}`);
                const cost = parseInt(btn.innerText.match(/\d+/)[0]);
                // Abilities locked if dead, frozen, cooldown active, or out of mana
                btn.classList.toggle('active', me.mp >= cost && !me.abilities_locked && !spellUsedThisTurn && !isDead && !isFrozen);
            });

            // 🏆 RANKINGS SIDEBAR
            document.getElementById('warriorList').innerHTML = data.participants.map(p => `
                <div class="rank-item ${p.user_id == {{ Auth::id() }} ? 'is-me' : ''} ${p.hp <= 0 ? 'is-dead' : ''}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold small">
                            ${p.rank ? `<span class="badge bg-primary me-1">#${p.rank}</span>` : ''}
                            ${p.name} ${p.hp <= 0 ? '💀' : ''}
                        </span>
                        <span class="badge ${p.hp > 20 ? 'bg-success' : 'bg-danger'}">${p.hp > 0 ? p.hp : '0'} HP</span>
                    </div>
                    <div class="progress" style="height:4px; background: rgba(255,255,255,0.1);">
                        <div class="progress-bar bg-danger" style="width:${(p.hp / maxHp * 100)}%"></div>
                    </div>
                </div>
            `).join('');
        } catch (e) { console.error("Sync Error:", e); }
    }

    function renderQ() {
        if (isDead) return; // Stop rendering for spectators
        feedbackActive = false; 
        spellUsedThisTurn = false; 
        
        const arena = document.getElementById('arenaCard');
        arena.classList.remove('theme-heal'); 
        
        const q = getQ();
        document.getElementById('qText').innerText = q.question_text;
        document.getElementById('qTypeBadge').innerText = q.question_type.toUpperCase();
        
        const grid = document.getElementById('optionsGrid'), fib = document.getElementById('fibArea');
        grid.innerHTML = ''; fib.innerHTML = ''; selectedIds = [];
        
        if (q.question_type === 'text') {
            fib.innerHTML = `<input type="text" id="ansInput" class="fib-ans" placeholder="TYPE ANSWER..." autofocus>`;
        } else {
            q.options.forEach(opt => grid.insertAdjacentHTML('beforeend', `
                <button class="option-card" id="opt-${opt.id}" data-id="${opt.id}" onclick="handleSel(${opt.id}, '${q.question_type}', this)">
                    ${opt.option_text}
                </button>`));
        }
        startT();
    }

    function handleSel(id, type, btn) {
        if (isFrozen || feedbackActive || isDead) return;
        if (type === 'single') { 
            document.querySelectorAll('.option-card').forEach(b => b.classList.remove('selected')); 
            btn.classList.add('selected'); 
            selectedIds = id; 
        } else { 
            btn.classList.toggle('selected'); 
            if(!Array.isArray(selectedIds)) selectedIds = [];
            if(selectedIds.includes(id)) selectedIds = selectedIds.filter(i => i !== id); 
            else selectedIds.push(id); 
        }
    }

    function startT() {
        clearInterval(timer); timeLeft = 60;
        document.getElementById('timerFill').style.transition = 'none'; 
        document.getElementById('timerFill').style.width = '100%';
        setTimeout(() => { 
            document.getElementById('timerFill').style.transition = 'width 60s linear'; 
            document.getElementById('timerFill').style.width = '0%'; 
        }, 50);
        timer = setInterval(() => { 
            if(!isFrozen && !feedbackActive && !isDead) { 
                timeLeft--; 
                document.getElementById('timeSec').innerText = timeLeft; 
                if(timeLeft <= 0) submitAns(true); 
            } 
        }, 1000);
    }

    async function submitAns(isTimeout = false) {
        if (feedbackActive || isDead) return; 
        feedbackActive = true; 
        clearInterval(timer);

        const q = getQ();
        let ans = isTimeout ? null : (q.question_type === 'text' ? document.getElementById('ansInput').value : selectedIds);
        
        try {
            const res = await fetch(`${baseURL}/strike`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ answer: ans, question_id: q.id, question_type: q.question_type, time_left: timeLeft }) 
            });
            const result = await res.json();

            // Correction Visuals
            if (q.question_type === 'text') {
                document.getElementById('ansInput').classList.add(result.is_correct ? 'correct' : 'incorrect');
            } else {
                document.querySelectorAll('.option-card').forEach(btn => {
                    const id = parseInt(btn.dataset.id);
                    if (selectedIds == id || (Array.isArray(selectedIds) && selectedIds.includes(id))) {
                        btn.classList.add(result.is_correct ? 'correct' : 'incorrect');
                    }
                });
            }

            setTimeout(() => { currentIdx++; renderQ(); }, 1200);
        } catch (e) { renderQ(); }
    }

    async function castPower(type) {
        if (spellUsedThisTurn || isDead || isFrozen) return;
        try {
            const res = await fetch(`${baseURL}/power`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ power_type: type }) 
            });
            const data = await res.json();
            if (data.success) {
                spellUsedThisTurn = true; 
                if (type === 'heal') document.getElementById('arenaCard').classList.add('theme-heal');
                sync();
            }
        } catch (e) {}
    }
    

    setInterval(sync, 1500); 
    renderQ();
</script>
</body>
</html>