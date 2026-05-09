<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arena Lobby | {{ $room->room_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background: #0f172a; /* Deep cinematic dark blue */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .lobby-container { 
            width: 100%;
            max-width: 900px;
            background: #ffffff; 
            border-radius: 40px; 
            padding: 60px; 
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        @keyframes dropIn {
            0% { transform: translateY(-100px) scale(0.5); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }

        .warrior-card {
            animation: dropIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: #f8fafc; 
            border-radius: 24px; 
            padding: 20px;
            width: 150px; 
            border: 2px solid #f1f5f9;
        }

        .arena-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px; 
            margin-top: 40px; 
            justify-items: center;
        }

        .room-badge { 
            background: #1e293b; 
            color: #fbbf24; 
            padding: 15px 45px; 
            border-radius: 20px; 
            font-size: 3rem; 
            font-weight: 900; 
            letter-spacing: 8px; 
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="lobby-container">
        <div class="mb-4">
            <span class="badge bg-primary rounded-pill px-4 py-2 text-uppercase fw-bold" style="letter-spacing: 2px;">
                <i class="fas fa-shield-alt me-2"></i> Mission Deployment
            </span>
        </div>

        <small class="text-muted text-uppercase fw-black d-block mb-2" style="letter-spacing: 1px;">Access Code</small>
        <div class="d-flex justify-content-center mb-4">
            <div class="room-badge shadow-lg">{{ $room->room_code }}</div>
        </div>
        
        <h2 class="fw-bold text-dark">Warriors Assembling: <span id="count" class="text-primary">{{ $participants->count() }}</span>/20</h2>
        <p class="text-muted">The jump sequence will begin once the Host confirms deployment.</p>

        <div class="arena-grid" id="warriorArena">
            @foreach($participants as $p)
            <div class="warrior-card" data-user-id="{{ $p->user_id }}">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($p->user->name) }}&background=random&color=fff&size=128" 
                     class="rounded-circle mb-3 shadow-sm" width="80">
                <div class="small fw-bold text-truncate">{{ $p->user->name }}</div>
                <div class="badge bg-success-soft text-success mt-2">READY</div>
            </div>
            @endforeach
        </div>

        <div class="mt-5 pt-4 border-top">
            @if(Auth::id() === $room->host_id)
                <div class="d-flex flex-column align-items-center gap-3">
                    <button onclick="startMission()" class="btn btn-warning btn-lg px-5 rounded-pill fw-black py-3 shadow-lg" style="min-width: 300px;">
                        <i class="fas fa-bolt me-2"></i> START MISSION
                    </button>
                    <button onclick="confirmDismiss()" class="btn btn-link text-danger text-decoration-none fw-bold">
                        <i class="fas fa-times-circle me-1"></i> ABORT MISSION
                    </button>
                </div>
            @else
                <div class="py-3">
                    <div class="spinner-grow text-primary mb-3" role="status"></div>
                    <p class="text-muted italic">Awaiting Host Command...</p>
                    <a href="{{ route('student.quizzes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-door-open me-2"></i> LEAVE ARENA
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const roomCode = "{{ $room->room_code }}";
    const isHost = {{ Auth::id() === $room->host_id ? 'true' : 'false' }};

    if (isHost) {
        window.onbeforeunload = () => "Warning: Leaving will terminate the mission.";
    }

    async function startMission() {
        if (confirm("START MISSION: Ready to deploy all warriors?")) {
            window.onbeforeunload = null; 
            const res = await fetch(`/student/quizzes/lobby/${roomCode}/start`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (res.ok) window.location.href = `/student/quizzes/pvp/${roomCode}`;
        }
    }

    async function confirmDismiss() {
        if (confirm("ABORT MISSION: This will delete the lobby. Proceed?")) {
            window.onbeforeunload = null; 
            const res = await fetch(`/student/quizzes/lobby/${roomCode}/dismiss`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            if (res.ok) window.location.href = "{{ route('student.quizzes.index') }}";
        }
    }

    function updateLobby() {
        fetch(`/student/quizzes/lobby/${roomCode}/participants`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'dismissed' || data.error) {
                    window.onbeforeunload = null;
                    window.location.href = "{{ route('student.quizzes.index') }}";
                    return;
                }
                if (data.status === 'active') {
                    window.onbeforeunload = null;
                    window.location.href = `/student/quizzes/pvp/${roomCode}`;
                }
                document.getElementById('count').innerText = data.participants.length;
            });
    }
    setInterval(updateLobby, 3000);
</script>
</body>
</html>