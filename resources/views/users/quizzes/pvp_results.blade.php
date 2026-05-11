<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Battle Over | Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #020617; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; }
        .results-card { background: #0f172a; border-radius: 30px; padding: 50px; border: 4px solid #1e293b; width: 100%; max-width: 600px; text-align: center; }
        .rank-item { background: rgba(255,255,255,0.05); border-radius: 15px; padding: 15px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .rank-1 { border: 2px solid #fbbf24; background: rgba(251, 191, 36, 0.1); }
    </style>
</head>
<body>

<div class="results-card">
    <h1 class="fw-black text-warning mb-4">BATTLE RESULTS</h1>
    <div class="mb-5">
        @foreach($participants as $index => $p)
            <div class="rank-item {{ $index === 0 ? 'rank-1' : '' }}">
                <span>
                    <span class="badge bg-primary me-2">#{{ $index + 1 }}</span>
                    <strong>{{ $p->user->name }}</strong>
                </span>
                <span class="text-{{ $p->hp > 0 ? 'success' : 'danger' }} fw-bold">
                    {{ $p->hp > 0 ? 'WINNER' : 'DEFEATED' }}
                </span>
            </div>
        @endforeach
    </div>
    <a href="{{ route('student.quizzes.index') }}" class="btn btn-warning px-5 py-3 rounded-pill fw-bold">RETURN TO DASHBOARD</a>
</div>

</body>
</html>