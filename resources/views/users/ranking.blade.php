@extends('users.students')

@section('content')
<style>
    /* Premium Gamified Leaderboard Theme Layouts */
    .rank-card { background: white; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); margin-bottom: 120px; }
    .table thead { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; padding: 22px 20px; border: none; font-weight: 800; }
    .table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
    .table tbody tr:hover td { background-color: #f8fafc; }
    
    /* Tier Badge Vectors */
    .tier-badge { font-size: 0.7rem; padding: 6px 14px; border-radius: 50px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 6px; }
    .tier-unranked { background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; }
    .tier-bronze { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .tier-silver { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .tier-gold { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; }

    /* Number Badge Podium Formatting */
    .rank-number { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.95rem; background: #f1f5f9; color: #475569; }
    .top-1 { background: linear-gradient(135deg, #ffdf00 0%, #d4af37 100%); color: #5c4300; box-shadow: 0 4px 12px rgba(212, 175, 55, 0.35); }
    .top-2 { background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%); color: #334155; box-shadow: 0 4px 12px rgba(148, 163, 184, 0.25); }
    .top-3 { background: linear-gradient(135deg, #ffedd5 0%, #f97316 100%); color: #7c2d12; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25); }
    
    /* Highlight Active Row Element if within Top 100 */
    .row-highlight-me td { background-color: #f0fdf4 !important; border-bottom-color: #bbf7d0; }

    /* 📌 FIXED BOTTOM USER HUD PIN */
    .user-sticky-hud {
        position: fixed;
        bottom: 24px;
        left: var(--sidebar-width, 270px); /* Adjusts dynamically across your layout padding widths */
        right: 24px;
        background: rgba(15, 23, 42, 0.95);
        color: white;
        border-radius: 20px;
        padding: 18px 30px;
        box-shadow: 0 -10px 30px -5px rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        z-index: 999;
        border: 1px solid rgba(255,255,255,0.1);
        transition: left 0.3s ease;
    }
    
    /* Fallback override rule if sidebar state alters width boundaries */
    @media (max-width: 991px) {
        .user-sticky-hud { left: 24px !important; }
    }
</style>

<div class="container-fluid p-0 text-start position-relative">
    
    {{-- LEADERBOARD HEADER BANNERS --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black text-dark mb-1"><i class="fas fa-trophy text-warning me-2"></i>Warrior Leaderboard</h2>
            <p class="text-muted mb-0">Compete in Arena PVP updates to secure your station among the Top 100 legends.</p>
        </div>
        <div class="badge bg-dark rounded-pill px-3 py-2 fs-6 shadow-sm">
            <i class="fas fa-users me-2 text-info"></i>Pool: {{ $rankings->count() }} Users
        </div>
    </div>

    {{-- MAIN TABLE MODULE CONTAINER --}}
    <div class="rank-card shadow-sm bg-white">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 120px;" class="ps-4">Position</th>
                    <th>Warrior Profile</th>
                    <th>Class Tier Status</th>
                    <th class="text-end pe-4">Total Skill Points</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $myRankPosition = null; 
                    $currentUserData = null;
                @endphp

                @foreach($rankings as $index => $rUser)
                    @php
                        // Determine chronological absolute tracking index values
                        $displayRank = $index + 1;
                        $isMe = ($rUser->id === Auth::id());
                        
                        if ($isMe) {
                            $myRankPosition = $displayRank;
                            $currentUserData = $rUser;
                        }

                        // Gating constraints past position threshold limit boundaries
                        $isRanked = ($displayRank <= 100);

                        // Tier distribution engines
                        if ($rUser->pvp_points >= 300) { 
                            $rankName = 'Gold'; $tierClass = 'tier-gold'; 
                        } elseif ($rUser->pvp_points >= 100) { 
                            $rankName = 'Silver'; $tierClass = 'tier-silver'; 
                        } else { 
                            $rankName = 'Bronze'; $tierClass = 'tier-bronze'; 
                        }

                        // Medal position assignment
                        $posClass = '';
                        if ($displayRank == 1) $posClass = 'top-1';
                        elseif ($displayRank == 2) $posClass = 'top-2';
                        elseif ($displayRank == 3) $posClass = 'top-3';
                    @endphp

                    {{-- Hide record rows from main viewing panel array if past rank 100 constraint boundaries --}}
                    @if($isRanked)
                        <tr class="{{ $isMe ? 'row-highlight-me' : '' }}">
                            <td class="ps-4">
                                <div class="rank-number {{ $posClass }}">{{ $displayRank }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($rUser->name) }}&background={{ $isMe ? '10b981' : '64748b' }}&color=fff&bold=true" class="rounded-circle shadow-sm" width="38" height="38">
                                    <div>
                                        <span class="fw-bold {{ $isMe ? 'text-success fs-5' : 'text-dark' }}">{{ $rUser->name }}</span>
                                        @if($isMe)
                                            <span class="badge bg-success ms-2 px-2 small">YOU</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="tier-badge {{ $tierClass }}">
                                    <i class="fas fa-shield-alt"></i> {{ $rankName }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="fw-black fs-5 {{ $isMe ? 'text-success' : 'text-primary' }}">{{ number_format($rUser->pvp_points) }}</span>
                                <small class="text-muted fw-bold ms-1">PTS</small>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ==========================================================================
         📌 STICKY FLOATING HORIZONTAL PROFILE DASHBOARD OVERLAY (ALWAYS VISIBLE)
         ========================================================================== --}}
    @if($currentUserData)
        @php
            $isUnrankedPastMax = ($myRankPosition > 100);
            
            if ($currentUserData->pvp_points >= 300) { 
                $myTierName = 'Gold'; $myTierClass = 'tier-gold'; 
            } elseif ($currentUserData->pvp_points >= 100) { 
                $myTierName = 'Silver'; $myTierClass = 'tier-silver'; 
            } else { 
                $myTierName = 'Bronze'; $myTierClass = 'tier-bronze'; 
            }
        @endphp

        <div class="user-sticky-hud d-flex justify-content-between align-items-center animate__animated animate__fadeInUp">
            <div class="d-flex align-items-center gap-3">
                @if($isUnrankedPastMax)
                    <div class="rank-number tier-unranked px-3 w-auto h-auto py-2 small" style="font-size:0.75rem;">UNRANKED</div>
                @else
                    <div class="rank-number {{ ($myRankPosition <= 3) ? 'top-'.$myRankPosition : '' }} shadow-sm" style="width: 42px; height: 42px; font-size:1.1rem;">
                        #{{ $myRankPosition }}
                    </div>
                @endif
                <div>
                    <small class="text-white-50 d-block tracking-wider text-uppercase" style="font-size:0.65rem;">Your Standings Status</small>
                    <h5 class="fw-bold text-white mb-0">{{ $currentUserData->name }} <span class="text-success small ms-1">(Online)</span></h5>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <div class="text-center">
                    <small class="text-white-50 d-block tracking-wider text-uppercase mb-1" style="font-size:0.65rem;">Current Tier</small>
                    <span class="tier-badge {{ $myTierClass }} px-3 py-1 shadow-sm">
                        <i class="fas fa-shield-alt"></i> {{ $myTierName }}
                    </span>
                </div>
                <div class="text-end border-start border-secondary ps-4">
                    <small class="text-white-50 d-block tracking-wider text-uppercase" style="font-size:0.65rem;">Accumulated Value</small>
                    <span class="fw-black text-warning fs-3">{{ number_format($currentUserData->pvp_points) }}</span>
                    <small class="text-warning small fw-bold ms-1">PTS</small>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- Ensure Javascript captures layout tracking transitions safely --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Adjust fixed floating banner alignment boundaries if standard wrapper sidebar transitions toggle
        const sidebar = document.querySelector('.sidebar');
        if(!sidebar) {
            const hud = document.querySelector('.user-sticky-hud');
            if(hud) hud.style.left = "24px";
        }
    });
</script>
@endsection