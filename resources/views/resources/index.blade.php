@extends('admin.adminhome')

@section('content')
<style>
    :root { --maroon-dark: #5b1a1a; --maroon-light: #7f1d1d; }
    .resource-header { background: linear-gradient(135deg, var(--maroon-light) 0%, var(--maroon-dark) 100%); color: white; border-radius: 16px; padding: 35px; margin-bottom: 30px; }
    #custom-tabs .nav-link { border-radius: 50px; padding: 10px 25px; font-weight: 600; border:none; color: #64748b; }
    #custom-tabs .nav-link.active { background-color: var(--maroon-dark); color: white; }
    .resource-item { display: flex; flex-direction: column; height: 100%; position: relative; }
    .class-footer-tag { background: #f8fafc; border: 1px solid #edf2f7; border-left: 4px solid var(--maroon-dark); padding: 10px 15px; margin-top: -1px; border-radius: 0 0 12px 12px; }
    .card-wrapper { transition: 0.3s; border-radius: 12px; overflow: hidden; }
    .card-wrapper:hover { transform: translateY(-5px); }
</style>

<div class="container-fluid">
    {{-- Header --}}
    <div class="resource-header shadow">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="font-weight-bold mb-1">مكتبة الموارد • Resource Library</h2>
                <p class="mb-0 small opacity-75">Manage classroom notes, textbooks, and YouTube video lessons.</p>
            </div>
            <div class="d-flex">
                <button class="btn btn-outline-light btn-sm mr-2 px-4 rounded-pill font-weight-bold" data-toggle="modal" data-target="#syncModal">
                    <i class="fab fa-youtube mr-2"></i> YouTube Finder
                </button>
                <button class="btn btn-light text-maroon btn-sm font-weight-bold px-4 rounded-pill shadow-sm" data-toggle="modal" data-target="#uploadModal">
                    <i class="fas fa-plus mr-2"></i> Add Material
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-pills" id="custom-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#content-my-materials">My Materials</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#content-textbooks">Public Textbooks</a></li>
        </ul>
        <div class="d-flex align-items-center">
            <i class="fas fa-filter mr-2 text-muted"></i>
            <select class="form-control form-control-sm border-0 shadow-sm" id="classFilter" style="width: 250px; border-radius: 10px;" onchange="filterByClass()">
                <option value="all">All Classes & Years</option>
                @foreach($groups as $g) 
                    <option value="group-{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> 
                @endforeach
            </select>
        </div>
    </div>

    <div class="tab-content">
        {{-- TAB 1: MY MATERIALS --}}
        <div class="tab-pane fade show active" id="content-my-materials">
            <ul class="nav nav-pills sub-nav mb-4 bg-white p-1 d-inline-flex rounded-pill border">
                <li class="nav-item"><a class="nav-link active py-2 px-4" data-toggle="pill" href="#sub-notes">Notes</a></li>
                <li class="nav-item"><a class="nav-link py-2 px-4" data-toggle="pill" href="#sub-videos">Videos</a></li>
            </ul>
            
            <div class="tab-content">
                {{-- Sub-tab: Notes --}}
                <div class="tab-pane fade show active" id="sub-notes">
                    <div class="row">
                        @forelse($myResources->where('type', 'note') as $res)
                        <div class="col-xl-4 col-md-6 mb-4 resource-item group-{{ $res->group_id }}">
                            <div class="card-wrapper shadow-sm bg-white">
                                @include('resources.partials.resource-card', ['res' => $res])
                                <div class="class-footer-tag">
                                    <small class="font-weight-bold text-muted text-uppercase">
                                        <i class="fas fa-users mr-1"></i> {{ $res->group->group_name ?? 'Unassigned' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty 
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                            <h5 class="text-muted">No notes uploaded yet.</h5>
                        </div> 
                        @endforelse
                    </div>
                </div>

                {{-- Sub-tab: Videos --}}
                <div class="tab-pane fade" id="sub-videos">
                    <div class="row">
                        @forelse($myResources->where('type', 'video') as $res)
                        <div class="col-xl-4 col-md-6 mb-4 resource-item group-{{ $res->group_id }}">
                            <div class="card-wrapper shadow-sm bg-white">
                                @include('resources.partials.resource-card', ['res' => $res])
                                <div class="class-footer-tag">
                                    <small class="font-weight-bold text-muted text-uppercase">
                                        <i class="fas fa-users mr-1"></i> {{ $res->group->group_name ?? 'Unassigned' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @empty 
                        <div class="col-12 text-center py-5">
                            <i class="fab fa-youtube fa-3x text-light mb-3"></i>
                            <h5 class="text-muted">No YouTube videos shared yet.</h5>
                        </div> 
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: TEXTBOOKS --}}
        <div class="tab-pane fade" id="content-textbooks">
            <div class="row">
                @forelse($textbooks as $res)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card-wrapper shadow-sm">
                        @include('resources.partials.resource-card', ['res' => $res])
                    </div>
                </div>
                @empty 
                <div class="col-12 text-center py-5">
                    <i class="fas fa-book fa-3x text-light mb-3"></i>
                    <h5 class="text-muted">No public textbooks available.</h5>
                </div> 
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- 🟢 MODAL: UPLOAD MATERIAL --}}
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white border-0" style="background: var(--maroon-dark);">
                <h5 class="font-weight-bold mb-0">Upload Class Material</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center p-5 border rounded bg-white mb-4 shadow-sm" onclick="document.getElementById('fileInput').click()" style="cursor:pointer; border: 2px dashed #ddd !important;">
                        <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                        <h6>Click or Drag to upload file (PDF, DOC, JPG)</h6>
                        <input type="file" name="file" id="fileInput" class="d-none" onchange="document.getElementById('fileNameDisplay').innerText = this.files[0].name" required>
                        <div id="fileNameDisplay" class="text-danger font-weight-bold mt-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Material Title</label>
                            <input type="text" name="title" class="form-control rounded-pill border-0 shadow-sm" placeholder="e.g. Nota Bab 1: Iman" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Category</label>
                            <select name="type" class="form-control rounded-pill border-0 shadow-sm" id="modalTypeSelect" onchange="toggleFields()">
                                <option value="note">Private Note (Class Only)</option>
                                <option value="textbook">Public Textbook (All Students)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">Subject</label>
                            <select name="subject_id" class="form-control rounded-pill border-0 shadow-sm" required>
                                @foreach($subjects as $s) <option value="{{ $s->id }}">{{ $s->subject_name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="groupSelectDiv">
                        <label class="font-weight-bold">Assign to Class</label>
                        <select name="group_id" class="form-control rounded-pill border-0 shadow-sm">
                            @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-block text-white font-weight-bold rounded-pill py-3 shadow-lg mt-3" style="background: var(--maroon-dark);">
                        SAVE MATERIAL
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 🟢 MODAL: YOUTUBE FINDER (THE FIX) --}}
<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="font-weight-bold mb-0"><i class="fab fa-youtube text-danger mr-2"></i> YouTube Lesson Finder</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Find and share educational videos directly from YouTube with your classes.</p>
                <form action="{{ route('resources.youtube.search') }}" method="GET">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Target Class</label>
                        <select name="group_id" class="form-control border-0 bg-light rounded-pill" required>
                            @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Subject</label>
                        <select name="subject_id" class="form-control border-0 bg-light rounded-pill" required>
                            @foreach($subjects as $s) <option value="{{ $s->id }}">{{ $s->subject_name }}</option> @endforeach
                        </select>
                    </div>
                    {{-- 🛑 NOTE: The search term 'q' is left empty here so the teacher can type it on the next page --}}
                    <button type="submit" class="btn btn-danger btn-block font-weight-bold py-3 rounded-pill shadow">
                        OPEN SEARCH ENGINE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ MODAL MANAGEMENT: Append to body to prevent layering issues
    document.addEventListener("DOMContentLoaded", function() {
        const uploadModal = document.getElementById('uploadModal');
        const syncModal = document.getElementById('syncModal');
        document.body.appendChild(uploadModal);
        document.body.appendChild(syncModal);
    });

    // Toggle class selection based on material type
    function toggleFields() { 
        document.getElementById('groupSelectDiv').style.display = (document.getElementById('modalTypeSelect').value === 'textbook') ? 'none' : 'block'; 
    }

    // Client-side filtering for classes
    function filterByClass() {
        const val = document.getElementById('classFilter').value;
        document.querySelectorAll('.resource-item').forEach(item => { 
            item.style.display = (val === 'all' || item.classList.contains(val)) ? 'block' : 'none'; 
        });
    }
</script>
@endsection