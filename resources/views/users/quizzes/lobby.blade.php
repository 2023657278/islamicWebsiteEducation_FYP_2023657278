<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Lobby | {{ $room->room_code }}</title>
    <!-- 🟢 NEW: Website Browser Tab Icon (Favicon) -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo-badge.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; margin: 0; }
        .lobby-container { width: 100%; max-width: 850px; background: white; border-radius: 40px; padding: 50px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .room-badge { background: #1e293b; color: #fbbf24; padding: 15px 40px; border-radius: 20px; font-size: 3rem; font-weight: 900; letter-spacing: 8px; }
        .arena-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 20px; margin-top: 30px; }
        .warrior-card { background: #f8fafc; border-radius: 20px; padding: 15px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="lobby-container shadow-lg">
    <div class="mb-4"><span class="badge bg-primary rounded-pill px-4 py-2 fw-bold text-uppercase">Mission Deployment</span></div>
    <div class="room-badge mb-4 shadow">{{ $room->room_code }}</div>
    <h2 class="fw-bold text-dark">Warriors Assembling: <span id="count" class="text-primary">{{ $participants->count() }}</span>/20</h2>
    
    <div class="arena-grid" id="warriorArena">
        {{-- JS will clear and redraw this list every 2 seconds --}}
    </div>

    <div class="mt-5 pt-4 border-top">
        @if(Auth::id() === $room->host_id)
            <button onclick="startMission()" class="btn btn-warning btn-lg px-5 rounded-pill fw-bold py-3 shadow">START MISSION</button>
            <button onclick="confirmDismiss()" class="btn btn-link text-danger text-decoration-none d-block mt-3 fw-bold">ABORT MISSION</button>
        @else
            <div class="spinner-grow text-primary mb-2"></div>
            <p class="text-muted">Awaiting Host Command...</p>
            <a href="{{ route('student.quizzes.lobby.leave', $room->room_code) }}" class="btn btn-outline-secondary rounded-pill px-4">LEAVE ARENA</a>
        @endif
    </div>
</div>

<script>
    const roomCode = "{{ $room->room_code }}";

    async function startMission() {
        if(confirm("Ready to deploy warriors?")) {
            window.onbeforeunload = null; 
            
            // 📡 Attempt to start the mission
            const res = await fetch(`/student/quizzes/lobby/${roomCode}/start`, { 
                method: 'POST', 
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
            });

            // ✅ If successful, go to the arena
            if(res.ok) {
                window.location.href = `/student/quizzes/pvp/${roomCode}`;
            } 
            // ❌ If failed (e.g., only 1 player), show the error from the controller
            else {
                const data = await res.json();
                alert(data.error || "Mission deployment failed.");
            }
        }
    }

    async function confirmDismiss() {
        if(confirm("Abort mission? This will close the lobby for everyone.")) {
            window.onbeforeunload = null; 
            const res = await fetch(`/student/quizzes/lobby/${roomCode}/dismiss`, { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
            if(res.ok) window.location.href = "{{ route('student.quizzes.index') }}";
        }
    }

    function updateLobby() {
        fetch(`/student/quizzes/lobby/${roomCode}/participants`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'dismissed' || !data.participants) { window.location.href = "{{ route('student.quizzes.index') }}"; return; }
                if (data.status === 'active') { window.location.href = `/student/quizzes/pvp/${roomCode}`; return; }

                const arena = document.getElementById('warriorArena');
                document.getElementById('count').innerText = data.participants.length;
                
                // 🟢 SYNC UI: Rebuild list so players who left are removed
                arena.innerHTML = '';
                data.participants.forEach(p => {
                    const name = p.user ? p.user.name : 'Warrior';
                    arena.insertAdjacentHTML('beforeend', `
                        <div class="warrior-card">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random&color=fff" class="rounded-circle mb-2" width="60">
                            <div class="small fw-bold text-truncate">${name}</div>
                            <div class="badge bg-success-soft text-success small">READY</div>
                        </div>`);
                });
            });
    }
    setInterval(updateLobby, 2000);
</script>
</body>
</html>