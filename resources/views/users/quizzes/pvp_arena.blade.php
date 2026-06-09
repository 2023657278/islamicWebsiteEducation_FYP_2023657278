<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elite PVP Arena | {{ $room->room_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body { 
            background: #020617; 
            color: white; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            height: 100vh; 
            overflow: hidden; 
            padding-top: 10px; 
        }
        
        /* 🏆 MAIN BATTLE STATION */
        .battle-card { 
            background: #0f172a; 
            border-radius: 30px; 
            border: 4px solid #334155; 
            padding: 20px 25px; 
            position: relative; 
            transition: background-color 0.15s ease-in-out, border-color 0.4s; 
            height: 620px; 
            display: flex; 
            flex-direction: column;
            box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        }

        /* 🔴 DAMAGE FLASH INDICATOR */
        .battle-card.damage-flash {
            background-color: #7f1d1d !important;
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
        .theme-heal { background: #14532d !important; border-color: #22c55e !important; box-shadow: 0 0 40px rgba(34, 197, 94, 0.4); }

        .power-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 15px; }
        
        /* 🎮 CLASSROOM INTERACTIVE ABILITY BUTTONS */
        .pwr-btn { 
            border: 2px solid rgba(255,255,255,0.15); 
            border-radius: 14px; 
            padding: 12px 2px; 
            color: white; 
            font-weight: 800; 
            font-size: 0.75rem; 
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); 
            opacity: 0.25; 
            pointer-events: none; 
            background: rgba(255,255,255,0.05); 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pwr-btn.active { 
            opacity: 1 !important; 
            pointer-events: auto !important; 
            transform: translateY(-3px); 
            border-color: #ffffff; 
            cursor: pointer; 
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
        .pwr-btn:active { transform: translateY(-1px); }
        .pwr-btn.cooldown { opacity: 0.4 !important; pointer-events: none !important; transform: none !important; background: #1e293b !important; border-color: #475569 !important; color: #94a3b8 !important; }
        
        .bg-heal { background: #22c55e !important; color: #ffffff !important; }
        .bg-shield { background: #3b82f6 !important; color: #ffffff !important; }
        .bg-freeze { background: #06b6d4 !important; color: #ffffff !important; }
        .bg-boost { background: #f43f5e !important; color: #ffffff !important; }

        #optionsGrid { overflow-y: auto; flex-grow: 1; max-height: 260px; padding-right: 8px; margin-bottom: 10px; }
        #optionsGrid::-webkit-scrollbar { width: 5px; }
        #optionsGrid::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }

        .option-card { 
            border: 2px solid #334155; border-radius: 15px; padding: 14px 20px; cursor: pointer; 
            transition: 0.2s; background: #1e293b; color: white; width: 100%; text-align: left; 
            margin-bottom: 8px; font-weight: 700; display: flex; align-items: center; font-size: 1rem;
        }
        .option-card.selected { border-color: #3b82f6; background: rgba(59, 130, 246, 0.15); }
        .option-card.correct { border-color: #10b981 !important; background: rgba(16, 185, 129, 0.3) !important; }
        .option-card.incorrect { border-color: #ef4444 !important; background: rgba(239, 68, 68, 0.3) !important; }

        .hp-fill { height: 100%; background: linear-gradient(90deg, #ef4444, #dc2626); transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 50px; }
        .mp-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 50px; }
        .timer-line { height: 8px; background: #3b82f6; width: 100%; transition: width 1s linear; border-radius: 10px; }

        /* 👑 HIGHLY VISIBLE WARRIOR STANDINGS SIDEBAR DESIGN */
        .designed-sidebar {
            background-color: #0b1329 !important;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.06) 1.5px, transparent 1.5px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.06) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            border: 3px solid #1e293b !important;
            box-shadow: inset 0 0 25px rgba(59, 130, 246, 0.1), 0 15px 50px rgba(0,0,0,0.7) !important;
            border-radius: 24px !important;
            height: 100%;
            overflow-y: auto;
        }

        .rank-title {
            color: #60a5fa !important;
            font-weight: 800;
            letter-spacing: 0.8px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 12px;
        }

        .rank-item { 
            background: #1e293b !important; 
            border: 2px solid #334155 !important;
            border-radius: 16px; 
            padding: 14px; 
            margin-bottom: 12px; 
            border-left: 6px solid #3b82f6 !important; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        .rank-item.is-me { 
            border-left-color: #3b82f6 !important; 
            background: rgba(30, 41, 59, 0.9) !important;
            border: 2px solid #60a5fa !important;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.15);
        }
        .rank-item.is-dead { border-left-color: #ef4444 !important; opacity: 0.35; filter: grayscale(1); background: rgba(0,0,0,0.3) !important; }
        
        .game-overlay { display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; border-radius: 30px; align-items: center; justify-content: center; flex-direction: column; text-align: center; backdrop-filter: blur(5px); }
        .is-frozen-state #frozenOverlay { display: flex; }
        .fib-ans { background: rgba(0,0,0,0.5); color: #ffffff; text-align: center; border: 3px solid #334155; padding: 12px; border-radius: 20px; font-size: 2.2rem; width: 100%; font-weight: 900; outline: none; }
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
                    <h3 class="text-white fw-bold mb-0" style="letter-spacing: -0.5px;">{{ strtoupper(Auth::user()->name) }}</h3>
                    <div class="text-end">
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
                    <h4 id="qText" class="fw-bold text-center mb-3 px-2" style="min-height: 45px; font-size: 1.1rem; color: #f1f5f9;"></h4>
                    <div id="optionsGrid" class="row row-cols-1 g-2"></div>
                    <div id="fibArea" class="mb-3"></div>

                    <button id="submitBtn" onclick="submitAns()" class="btn btn-warning w-100 py-3 mt-auto fw-black rounded-pill shadow-lg border-0 fs-5 text-dark font-weight-bold">
                        EXECUTE STRIKE ⚡
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-3 designed-sidebar">
                <h6 class="text-primary mb-3 fw-black rank-title">
                    <i class="fas fa-trophy me-2 text-warning"></i>WARRIOR STANDINGS
                </h6>
                <div id="warriorList"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            
            if (data.status === 'finished') { 
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

            // 🟢 FIXED LOBBY SCALING: 100 HP per participant (e.g., 3 players = 300 Max HP Pool)
            const totalPlayersCount = data.participants.length;
            const maxHp = totalPlayersCount * 100; 
            let currentHp = me.hp;
            if (currentHp > maxHp) currentHp = maxHp;

            // DAMAGE TINT FLASH CHECK
            if (lastKnownHp !== null && currentHp < lastKnownHp && !isDead) {
                arena.classList.add('damage-flash');
                setTimeout(() => { arena.classList.remove('damage-flash'); }, 350);
            }
            lastKnownHp = currentHp;

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

            // RENDER LOGICAL DISPLAY METRICS AT 100% RATIOS
            document.getElementById('myHp').style.width = (currentHp / maxHp * 100) + "%";
            document.getElementById('myHpText').innerText = `${currentHp > 0 ? currentHp : 0} / ${maxHp} HP`;
            
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
                    let canHealCheck = (p === 'heal') ? (currentHp < maxHp) : true;
                    btn.classList.toggle('active', me.mp >= cost && !me.abilities_locked && !feedbackActive && !isDead && !isFrozen && canHealCheck);
                }
            });

            // 🏆 WARRIOR STANDINGS SIDEBAR
            document.getElementById('warriorList').innerHTML = data.participants.map(p => {
                let pCurrentHp = p.hp;
                if (pCurrentHp > maxHp) pCurrentHp = maxHp;
                
                return `
                    <div class="rank-item ${p.user_id == {{ Auth::id() }} ? 'is-me' : ''} ${p.hp <= 0 ? 'is-dead' : ''}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small" style="color: #ffffff; font-size: 0.9rem;">
                                ${p.rank ? `<span class="badge bg-primary me-1" style="font-size:0.7rem; padding: 4px 7px;">#${p.rank}</span>` : ''}
                                ${p.name} ${p.hp <= 0 ? '💀' : ''}
                            </span>
                            <span class="badge ${p.hp > 0 ? 'bg-success' : 'bg-danger'}" style="font-size: 0.75rem; padding: 5px 9px; border-radius: 6px; font-weight:700;">${p.hp > 0 ? pCurrentHp : '0'} / ${maxHp} HP</span>
                        </div>
                        <div class="progress" style="height:6px; background: rgba(0,0,0,0.4); border-radius: 50px; margin-top: 6px;">
                            <div class="progress-bar bg-danger" style="width:${(pCurrentHp / maxHp * 100)}%; border-radius: 50px;"></div>
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
        
        if (!Array.isArray(selectedIds)) {
            selectedIds = [];
        }

        const cleanType = String(type).toLowerCase().trim();
        const intId = parseInt(id);

        if (cleanType === 'single' || cleanType === 'single_choice') { 
            document.querySelectorAll('.option-card').forEach(b => b.classList.remove('selected')); 
            btn.classList.add('selected'); 
            selectedIds = [intId]; 
        } else { 
            btn.classList.toggle('selected'); 
            if (selectedIds.includes(intId)) {
                selectedIds = selectedIds.filter(i => i !== intId); 
            } else {
                selectedIds.push(intId); 
            }
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
        let ans;
        
        if (isTimeout) {
            ans = null;
        } else if (q.question_type === 'text') {
            ans = document.getElementById('ansInput').value;
        } else {
            ans = Array.isArray(selectedIds) ? selectedIds : [selectedIds];
        }
        
        try {
            const res = await fetch(`${baseURL}/strike`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ answer: ans, question_id: q.id, question_type: q.question_type, time_left: timeLeft }) 
            });
            const result = await res.json();

            if (q.question_type === 'text') {
                const input = document.getElementById('ansInput');
                if (input) input.classList.add(result.is_correct ? 'correct' : 'incorrect');
            } else {
                document.querySelectorAll('.option-card').forEach(btn => {
                    const id = parseInt(btn.dataset.id);
                    if (ans.includes(id)) {
                        btn.classList.add(result.is_correct ? 'correct' : 'incorrect');
                    }
                });
            }

            setTimeout(() => { currentIdx++; renderQ(); }, 1200);
        } catch (e) { renderQ(); }
    }

    async function castPower(type) {
        if (isDead || isFrozen || cooldowns[type] > 0 || feedbackActive) return;
        try {
            const res = await fetch(`${baseURL}/power`, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, 
                body: JSON.stringify({ power_type: type }) 
            });
            const data = await res.json();
            if (data.success) {
                // 🟢 5-SECOND COOLDOWN MODIFIER TRIGGER
                ['heal', 'shield', 'freeze', 'boost'].forEach(p => {
                    cooldowns[p] = 5;
                });
                
                if (type === 'heal') {
                    document.getElementById('arenaCard').classList.add('theme-heal');
                    setTimeout(() => { document.getElementById('arenaCard').classList.remove('theme-heal'); }, 800);
                }
                sync();
            }
        } catch (e) {}
    }
    
    setInterval(sync, 1500); 
    renderQ();
</script>
</body>
</html>