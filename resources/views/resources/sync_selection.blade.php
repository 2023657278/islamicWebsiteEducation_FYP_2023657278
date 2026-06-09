@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    {{-- 1. Header Section --}}
    <div class="p-4 bg-white shadow-sm rounded-lg border-left border-danger mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="fab fa-youtube text-danger mr-2"></i> YouTube Master Finder</h4>
                <p class="text-muted mb-0">Class: <strong>{{ \App\Models\Group::find($group_id)?->group_name ?? 'General Resource' }}</strong></p>
            </div>
            <a href="{{ route('resources.index') }}" class="btn btn-light btn-sm rounded-pill px-4 border font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Back to Library
            </a>
        </div>
    </div>

    {{-- 2. Search & Source Toggle Section --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                        <input type="text" id="searchTerm" class="form-control border-0 px-4" style="height: 50px;" 
                               placeholder="Search for lessons (e.g. 'Tarawih', 'Puasa')..." value="{{ $query ?? '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-danger px-5 font-weight-bold" type="button" onclick="newSearch('public')">
                                 SEARCH
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-right mt-3 mt-md-0">
                    <div class="btn-group btn-group-toggle shadow-sm" data-toggle="buttons">
                        <label class="btn btn-outline-dark active btn-sm px-4 py-2" style="border-radius: 25px 0 0 25px;">
                            <input type="radio" name="searchType" value="public" checked onchange="handleToggle('public')"> 
                            <i class="fas fa-globe mr-1"></i> Public YouTube
                        </label>
                        <label class="btn btn-outline-dark btn-sm px-4 py-2" style="border-radius: 0 25px 25px 0;">
                            <input type="radio" name="searchType" value="mine" onchange="handleToggle('mine')"> 
                            <i class="fas fa-user-circle mr-1"></i> My Channel
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Results Section --}}
    <form action="{{ route('resources.sync.store_selected') }}" method="POST">
        @csrf
        <input type="hidden" name="group_id" value="{{ $group_id }}">
        <input type="hidden" name="subject_id" value="{{ $subject_id }}">

        <div class="text-center py-5 text-muted" id="initialState">
            <i class="fab fa-youtube fa-4x mb-3 opacity-25"></i>
            <h5>Type keywords and click search to find videos...</h5>
        </div>

        <div id="videoContainer" class="row">
            {{-- Search results will appear here --}}
        </div>

        {{-- Loading Spinner --}}
        <div id="loadingState" class="text-center py-5 d-none">
            <div class="spinner-border text-danger" role="status"></div>
            <p class="mt-2 text-muted font-weight-bold">Searching YouTube...</p>
        </div>

        {{-- Load More --}}
        <div id="loadMoreContainer" class="text-center py-4 d-none">
            <button type="button" class="btn btn-light border shadow-sm rounded-pill px-5 font-weight-bold" onclick="fetchNextPage()">
                <i class="fas fa-plus mr-2"></i> LOAD MORE
            </button>
        </div>

        {{-- 4. Sticky Import Footer --}}
        <div class="import-footer shadow-lg border-top">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="text-muted small d-none d-md-block">
                    <i class="fas fa-check-square mr-1"></i> Select videos to save them to your library.
                </div>
                <button type="submit" class="btn btn-danger btn-lg px-5 font-weight-bold shadow rounded-pill">
                    IMPORT SELECTED <i class="fas fa-download ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .card-video { transition: 0.3s; border-radius: 12px; overflow: hidden; border: none; }
    .card-video:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .import-footer { position: sticky; bottom: 0; background: white; padding: 20px; margin: 40px -15px 0 -15px; z-index: 1050; }
    .video-thumb { width: 100%; aspect-ratio: 16/9; object-fit: cover; cursor: pointer; }
    .custom-control-input:checked ~ .custom-control-label::before { background-color: #7f1d1d !important; border-color: #7f1d1d !important; }
</style>

<script>
    let nextPageToken = '';
    let currentType = "{{ $type ?? 'public' }}"; 
    let currentQuery = '';

    function handleToggle(type) {
        currentType = type;
        if (type === 'mine' || document.getElementById('searchTerm').value !== '') {
            newSearch(type);
        }
    }

    async function newSearch(type = null) {
        if(type) currentType = type;
        currentQuery = document.getElementById('searchTerm').value;
        
        if (currentType === 'public' && !currentQuery) {
            alert("Please enter keywords to search!");
            return;
        }

        const state = document.getElementById('initialState');
        if (state) state.classList.add('d-none');

        document.getElementById('videoContainer').innerHTML = '';
        nextPageToken = ''; 
        
        await fetchNextPage();
    }

    async function fetchNextPage() {
        const loader = document.getElementById('loadingState');
        const loadMore = document.getElementById('loadMoreContainer');
        
        loader.classList.remove('d-none');
        loadMore.classList.add('d-none');

        try {
            const url = `/youtube/fetch-data?q=${encodeURIComponent(currentQuery)}&pageToken=${nextPageToken}&type=${currentType}`;
            const res = await fetch(url);
            
            // 🔒 FIXED HANDLER CALLBACK LOGIC LINK
            if (res.status === 401) {
                if(confirm("To see your channel's videos, you need to sync your account. Sync now?")) {
                    // 🟢 THE FIX: Explicitly append context variables into query string parameters to trigger the selector
                    window.location.href = "{{ route('resources.sync.auth') }}?group_id={{ $group_id }}&subject_id={{ $subject_id }}";
                }
                loader.classList.add('d-none');
                return;
            }

            const data = await res.json();
            nextPageToken = data.nextPageToken || '';
            
            if (!data.items || data.items.length === 0) {
                if(document.getElementById('videoContainer').innerHTML === '') {
                    document.getElementById('videoContainer').innerHTML = '<div class="col-12 text-center py-5"><h5>No videos found.</h5></div>';
                }
            } else {
                renderVideos(data.items);
            }

            loader.classList.add('d-none');
            if (nextPageToken) loadMore.classList.remove('d-none');
            
        } catch (e) {
            console.error("Fetch Error:", e);
            loader.classList.add('d-none');
        }
    }

    function renderVideos(items) {
        const container = document.getElementById('videoContainer');
        items.forEach(item => {
            const vId = item.id.videoId || (item.snippet.resourceId ? item.snippet.resourceId.videoId : null);
            if (!vId) return;

            const title = item.snippet.title;
            const thumb = item.snippet.thumbnails.medium.url;

            const card = `
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-video shadow-sm bg-white">
                        <img src="${thumb}" class="video-thumb" onclick="window.open('https://youtube.com/watch?v=${vId}', '_blank')">
                        <div class="card-body p-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="video_ids[${vId}]" value="${title.replace(/"/g, '&quot;')}" 
                                       class="custom-control-input" id="vid-${vId}">
                                <label class="custom-control-label font-weight-bold small text-dark" for="vid-${vId}" style="cursor:pointer">
                                    ${title}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', card);
        });
    }

    // Auto-load if returning from OAuth
    window.onload = function() {
        if (currentType === 'mine') {
            document.querySelector('input[value="mine"]').parentElement.click();
        }
    };

    document.getElementById('searchTerm').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); newSearch(currentType); }
    });
</script>
@endsection