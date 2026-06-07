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
            position: relative; transition: background-color 0.15s ease-in-out, border-color 0.4s; 
            height: 620px; display: flex; flex-direction: column;
            box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        }

        /* 🔴 DAMAGE FLASH INDICATOR */
        .battle-card.damage-flash {
            background-color: #450a0a !important;
            border-color: #ef4444 !important;
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
            position: relative; overflow: hidden;
        }
        .pwr-btn.active { opacity: 1 !important; pointer-events: auto !important; transform: translateY(-2px); border-color: white; cursor: pointer; }
        .pwr-btn.cooldown { opacity: 0.4 !important; pointer-events: none !important; transform: none !important; background: #1e293b !important; border-color: #475569 !important; }
        
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

        /* 👑 WARRIOR STANDINGS UPGRADED DESIGNED BACKGROUND */
        .designed-sidebar {
            background-color: #0b0f19 !important;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 20px 20px;
            border: 2px solid #1e293b !important;
            box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.05), 0 15px 50px rgba(0,0,0,0.6) !important;
            border-radius: 24px !important;
            height: 100%;
            overflow-y: auto;
        }

        .rank-item { 
            background: rgba(30, 41, 59, 0.4); 
            backdrop-filter: blur(4px);
            border-radius: 14px; 
            padding: 12px; 
            margin-bottom: 10px; 
            border: 1px solid rgba(255,255,255,0.05);
            border-left: 5px solid #3b82f6; 
            transition: all 0.3s ease; 
        }
        .rank-item.is-me { border-left-color: #fbbf24; background: rgba(251, 191, 36, 0.06); border-right: 1px solid rgba(251, 191, 36, 0.1); }
        .rank-item.is-dead { border-left-color: #ef4444; opacity: 0.4; filter: grayscale(1); background: rgba(0,0,0,0.2); }
        
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
                        <!-- 🛑 REMOVED SURRENDER BUTTON ROW -->
                        <div class="progress mb-1" style="width: 180px; height: 16px; border-radius: 50px; background: rgba(0,0,0,0.5); position: relative;">
                            <div id="myHp" class="hp-fill"></div>
                            <small id="myHpText" class="position-absolute w-100 text-center text-white fw-bold text-xxs" style="left:0; top: -1px; font-size: 0.65rem;"></small>
                        </div>
                        <div class="progress" style="width: 180px; height: 14px; border-radius: 50px; background: rgba(0,0,0,0.5); position: relative;">
                            <div id="myMp" class="mp-fill"></div>
                            <small id="myMpText" class="position-absolute w-100 text-center text-white fw-bold" style="left:0; top: 0px; font-size: 0.6rem; z-index: 10; text-shadow: 0 1px 2px #000;"></small>
                        </div>
                    </div>
                </div>

                <div class="power-grid">
                    <button id="p-heal" onclick="castPower('heal')" class="pwr-btn bg-heal">HEAL (40)</button>
                    <button id="p-shield" onclick="castPower('shield')" class="pwr-btn bg-shield">SHIELD (40)</button>
                    <button id="p-freeze" onclick="castPower('freeze')" class="pwr-btn bg-freeze">FREEZE (40)</button>
                    <button id="p-boost" onclick="castPower('boost')" class="pwr-btn bg-boost">BOOST (40)</button>
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

        <!-- 🛡️ UPGRADED DESIGNED SIDEBAR ELEMENT -->
        <div class="col-lg-4">
            <div class="p-3 designed-sidebar">
                <h6 class="text-primary mb-3 fw-black" style="letter-spacing: 0.5px;">
                    <i class="fas fa-shield-halved me-2 text-warning"></i>WARRIOR STANDINGS
                </h6>
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
    
    let cooldowns = { heal: 0, shield: 0, freeze: 0, boost: 0 };
    let lastKnownHp = null;

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
            
            if (data.status === 'finished' && !feedbackActive) { 
                window.location.href = resultsURL; 
                return; 
            }
            
            const me = data.participants.find(p => p.user_id == {{ Auth::id() }});
            if (!me) return;

            const arena = document.getElementById('arenaCard');

            // ELIMINATION CHECK
            if (me.status === 'defeated' || me.hp <= 0) {
                isDead = true;
                document.getElementById('eliminatedOverlay').style.display = 'flex';
                document.getElementById('myFinalRank').innerText = `#${me.rank || '?'}`;
                document.getElementById('submitBtn').disabled = true;
                clearInterval(timer);
            }

            // RED BRIEF BACKGROUND FLASH IF HP DECREASED
            if (lastKnownHp !== null && me.hp < lastKnownHp && !isDead) {
                arena.classList.add('damage-flash');
                setTimeout(() => { arena.classList.remove('damage-flash'); }, 350);
            }
            lastKnownHp = me.hp;

            arena.classList.toggle('theme-shield', me.is_shielded);
            arena.classList.toggle('theme-boost', me.active_boost);

            // STRIKE LOCK UI
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

            // FREEZE COUNTDOWN UI
            if (me.is_frozen) {
                isFrozen = true; 
                arena.classList.add('is-frozen-state', 'theme-freeze');
                const diff = Math.max(0, Math.ceil((new Date(me.frozen_until) - new Date()) / 1000));
                document.getElementById('freezeCountdown').innerText = diff + "s";
            } else { 
                isFrozen = false; 
                arena.classList.remove('is-frozen-state', 'theme-freeze'); 
            }

            // 🟢 LINEAR HEALTH MAX-POOL CALCULATION: 1 User = 100, 2 Users = 200, 3 Users = 300...
            const maxHp = data.participants.length * 100; 
            
            // 🟢 FORCE UI INITIALIZATION SCALING MULTIPLIERS FOR 100% SPAWN RATIO FILLERS
            // This reads your true active proportion so players start with a visually Full Health bar
            let normalizedHp = me.hp;
            if (me.hp === 100 && maxHp > 100) {
                normalizedHp = maxHp; // Automatically scales up base starting HP to match full health index
            }

            document.getElementById('myHp').style.width = (normalizedHp / maxHp * 100) + "%";
            document.getElementById('myHpText').innerText = `${normalizedHp > 0 ? normalizedHp : 0} / ${maxHp} HP`;
            
            document.getElementById('myMp').style.width = me.mp + "%";
            document.getElementById('myMpText').innerText = `${me.mp} / 100 MP`;
            
            // SPELL COOLDOWNS TIMERS MANAGEMENT
            ['heal', 'shield', 'freeze', 'boost'].forEach(p => {
                const btn = document.getElementById(`p-${p}`);
                const cost = 40; 
                
                if (cooldowns[p] > 0) {
                    btn.classList.remove('active');
                    btn.classList.add('cooldown');
                    btn.innerText = `${p.toUpperCase()} (${cooldowns[p]}s)`;
                } else {
                    btn.classList.remove('cooldown');
                    btn.innerText = `${p.toUpperCase()} (40)`;
                    btn.classList.toggle('active', me.mp >= cost && !me.abilities_locked && !spellUsedThisTurn && !isDead && !isFrozen);
                }
            });

            // 🏆 RANKINGS SIDEBAR WITH CORRESPONDING INITIALIZATION SCALING
            document.getElementById('warriorList').innerHTML = data.participants.map(p => {
                let renderHp = p.hp;
                if (p.hp === 100 && maxHp > 100) { renderHp = maxHp; }
                return `
                    <div class="rank-item ${p.user_id == {{ Auth::id() }} ? 'is-me' : ''} ${p.hp <= 0 ? 'is-dead' : ''}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small" style="color: #f1f5f9;">
                                ${p.rank ? `<span class="badge bg-primary me-1" style="font-size:0.65rem; padding: 3px 6px;">#${p.rank}</span>` : ''}
                                ${p.name} ${p.hp <= 0 ? '💀' : ''}
                            </span>
                            <span class="badge ${p.hp > 0 ? 'bg-success' : 'bg-danger'}" style="font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">${p.hp > 0 ? renderHp : '0'} / ${maxHp} HP</span>
                        </div>
                        <div class="progress" style="height:5px; background: rgba(0,0,0,0.4); border-radius: 50px;">
                            <div class="progress-bar bg-danger" style="width:${(renderHp / maxHp * 100)}%; border-radius: 50px;"></div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (e) { console.error("Sync Error:", e); }
    }

    setInterval(() => {
        ['heal', 'shield', 'freeze', 'boost'].forEach(p => {
            if (cooldowns[p] > 0) { cooldowns[p]--; }
        });
    }, 1000);

    function renderQ() {
        if (isDead) return; 
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
        if (spellUsedThisTurn || isDead || isFrozen || cooldowns[type] > 0) return;
        try {
            const res = await fetch(`${baseURL}/power`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ power_type: type }) 
            });
            const data = await res.json();
            if (data.success) {
                spellUsedThisTurn = true; 
                cooldowns[type] = 10;
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