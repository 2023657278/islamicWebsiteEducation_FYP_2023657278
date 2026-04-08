@extends('admin.adminhome')

@section('content')
<style>
    :root { --maroon-dark: #5b1a1a; --maroon-light: #7f1d1d; }
    .resource-header { background: linear-gradient(135deg, var(--maroon-light) 0%, var(--maroon-dark) 100%); color: white; border-radius: 16px; padding: 35px; margin-bottom: 30px; }
    #custom-tabs .nav-link { border-radius: 50px; padding: 10px 25px; font-weight: 600; border:none; }
    #custom-tabs .nav-link.active { background-color: var(--maroon-dark); color: white; }
    .resource-item { display: flex; flex-direction: column; height: 100%; position: relative; }
    .class-footer-tag { background: #f8fafc; border: 1px solid #edf2f7; border-left: 4px solid var(--maroon-dark); padding: 10px 15px; margin-top: -1px; }
</style>

<div class="container-fluid">
    <div class="resource-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="font-weight-bold mb-1">مكتبة الموارد • Library</h2>
                <p class="mb-0 small opacity-75">Manage your class materials and YouTube sync.</p>
            </div>
            <div class="d-flex">
                <button class="btn btn-outline-light btn-sm mr-2 px-3" data-toggle="modal" data-target="#syncModal">Sync YouTube</button>
                <button class="btn btn-light text-maroon btn-sm font-weight-bold px-3 shadow-sm" data-toggle="modal" data-target="#uploadModal">Add Resource</button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="nav nav-pills" id="custom-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#content-my-materials">My Materials</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#content-textbooks">Public Textbooks</a></li>
        </ul>
        <select class="form-control form-control-sm" id="classFilter" style="width: 250px;" onchange="filterByClass()">
            <option value="all">All Classes & Years</option>
            @foreach($groups as $g) 
                <option value="group-{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> 
            @endforeach
        </select>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="content-my-materials">
            <ul class="nav nav-pills sub-nav mb-4">
                <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#sub-notes">Notes</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#sub-videos">Videos</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="sub-notes">
                    <div class="row">
                        @forelse($myResources->where('type', 'note') as $res)
                        <div class="col-xl-4 col-md-6 mb-4 resource-item group-{{ $res->group_id }}">
                            <div class="card-wrapper shadow-sm">
                                @include('resources.partials.resource-card', ['res' => $res])
                                <div class="class-footer-tag"><small class="font-weight-bold text-muted"><i class="fas fa-users mr-1"></i> {{ $res->group->group_name ?? 'Unassigned' }} ({{ $res->group->year->year ?? 'N/A' }})</small></div>
                            </div>
                        </div>
                        @empty <div class="col-12 text-center py-5">No notes found.</div> @endforelse
                    </div>
                </div>
                <div class="tab-pane fade" id="sub-videos">
                    <div class="row">
                        @forelse($myResources->where('type', 'video') as $res)
                        <div class="col-xl-4 col-md-6 mb-4 resource-item group-{{ $res->group_id }}">
                            <div class="card-wrapper shadow-sm">
                                @include('resources.partials.resource-card', ['res' => $res])
                                <div class="class-footer-tag"><small class="font-weight-bold text-muted"><i class="fas fa-users mr-1"></i> {{ $res->group->group_name ?? 'Unassigned' }} ({{ $res->group->year->year ?? 'N/A' }})</small></div>
                            </div>
                        </div>
                        @empty <div class="col-12 text-center py-5">No videos found.</div> @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="content-textbooks">
            <div class="row">
                @forelse($textbooks as $res)
                <div class="col-xl-4 col-md-6 mb-4">@include('resources.partials.resource-card', ['res' => $res])</div>
                @empty <div class="col-12 text-center py-5">No textbooks.</div> @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: var(--maroon-dark);"><h5>Upload Material</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4 bg-light">
                <form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center p-4 border rounded bg-white mb-4 shadow-sm" onclick="document.getElementById('fileInput').click()" style="cursor:pointer; border: 2px dashed #ccc;">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i><h6>Click to select file</h6>
                        <input type="file" name="file" id="fileInput" class="d-none" onchange="document.getElementById('fileName').innerText = this.files[0].name" required>
                        <div id="fileName" class="text-success small mt-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Type</label><select name="type" class="form-control" id="modalTypeSelect" onchange="toggleFields()"><option value="note">Private Note</option><option value="textbook">Public Textbook</option></select></div>
                        <div class="col-md-6 mb-3"><label>Subject</label><select name="subject_id" class="form-control" required>@foreach($subjects as $s) <option value="{{ $s->id }}">{{ $s->subject_name }}</option> @endforeach</select></div>
                    </div>
                    <div class="form-group" id="groupSelectDiv"><label>Assign to Class (Year)</label>
                        <select name="group_id" class="form-control">
                            @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-block text-white font-weight-bold" style="background: var(--maroon-dark);">Save Resource</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="syncModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white"><h5>Sync YouTube Channel</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body p-4">
                <form action="{{ route('resources.sync.auth') }}" method="GET">
                    <div class="form-group"><label>Target Class (Year)</label>
                        <select name="group_id" class="form-control" required>
                            @foreach($groups as $g) <option value="{{ $g->id }}">{{ $g->group_name }} ({{ $g->year->year ?? 'N/A' }})</option> @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Subject</label><select name="subject_id" class="form-control" required>@foreach($subjects as $s) <option value="{{ $s->id }}">{{ $s->subject_name }}</option> @endforeach</select></div>
                    <button type="submit" class="btn btn-danger btn-block font-weight-bold">Login & Sync</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ PREVENT SCREEN FREEZE: Manually cleanup and handle modals
    document.addEventListener("DOMContentLoaded", function() {
        const uploadModal = document.getElementById('uploadModal');
        const syncModal = document.getElementById('syncModal');
        document.body.appendChild(uploadModal);
        document.body.appendChild(syncModal);
    });

    function toggleFields() { document.getElementById('groupSelectDiv').style.display = (document.getElementById('modalTypeSelect').value === 'textbook') ? 'none' : 'block'; }
    function filterByClass() {
        const val = document.getElementById('classFilter').value;
        document.querySelectorAll('.resource-item').forEach(item => { item.style.display = (val === 'all' || item.classList.contains(val)) ? 'block' : 'none'; });
    }
</script>
@endsection