@extends('admin.adminhome')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-dark">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Group</h5>
                    <span class="badge bg-white text-primary">MRSM Terendak</span>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('groups.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="small fw-bold text-dark">Group Name</label>
                            <input type="text" name="group_name" class="form-control @error('group_name') is-invalid @enderror" 
                                   placeholder="e.g., 4 Amanah" value="{{ old('group_name') }}" required>
                            @error('group_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-dark">Academic Year</label>
                            <input type="text" name="year" list="yearSuggestions" class="form-control @error('year') is-invalid @enderror" 
                                   placeholder="Type or select a year..." value="{{ old('year') }}" required autocomplete="off">
                            
                            <datalist id="yearSuggestions">
                                @foreach($years as $yearRecord)
                                    <option value="{{ $yearRecord->year }}">
                                @endforeach
                            </datalist>
                            <div class="form-text mt-1">Typing a new year will automatically register it in the system.</div>
                            @error('year') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('groups.index') }}" class="btn btn-secondary shadow-sm">Cancel</a>
                            <button type="submit" class="btn btn-maroon shadow-sm px-4">Register Group</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-maroon { background-color: #800000; color: white; border: none; }
    .btn-maroon:hover { background-color: #600000; color: white; }
    .card { border-radius: 12px; }
</style>
@endsection