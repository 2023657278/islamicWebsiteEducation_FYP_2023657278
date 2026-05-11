@extends('users.students')

@section('content')
<style>
    .rank-card { background: white; border-radius: 20px; border: 1px solid #eee; overflow: hidden; }
    .table thead { background: #f8fafc; }
    .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; padding: 20px; border: none; }
    .table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    
    .tier-badge { font-size: 0.7rem; padding: 5px 12px; border-radius: 50px; font-weight: 900; text-transform: uppercase; }
    .tier-bronze { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .tier-silver { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .tier-gold { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }

    .rank-number { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; }
    .top-1 { background: #FFD700; color: #856404; box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3); }
    .top-2 { background: #C0C0C0; color: #333; }
    .top-3 { background: #CD7F32; color: white; }
</style>

<div class="container-fluid p-0">
    <div class="mb-4">
        <h2 class="fw-bold">Warrior Leaderboard</h2>
        <p class="text-muted">Compete in PVP missions to climb the ranks from Bronze to Gold.</p>
    </div>

    <div class="rank-card shadow-sm">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Warrior Name</th>
                    <th>Current Tier</th>
                    <th class="text-end">Total Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankings as $index => $rUser)
                    @php
                        $rankName = 'Bronze'; $class = 'tier-bronze';
                        if($rUser->pvp_points >= 300) { $rankName = 'Gold'; $class = 'tier-gold'; }
                        elseif($rUser->pvp_points >= 100) { $rankName = 'Silver'; $class = 'tier-silver'; }
                        
                        $posClass = '';
                        if($index == 0) $posClass = 'top-1';
                        elseif($index == 1) $posClass = 'top-2';
                        elseif($index == 2) $posClass = 'top-3';
                    @endphp
                    <tr>
                        <td>
                            <div class="rank-number {{ $posClass }}">{{ $index + 1 }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="fw-bold text-dark">{{ $rUser->name }}</div>
                                @if($rUser->id === Auth::id())
                                    <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">YOU</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="tier-badge {{ $class }}">
                                <i class="fas fa-shield-alt me-1"></i> {{ $rankName }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-black fs-5 text-primary">{{ number_format($rUser->pvp_points) }}</span>
                            <small class="text-muted fw-bold ms-1">PTS</small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection