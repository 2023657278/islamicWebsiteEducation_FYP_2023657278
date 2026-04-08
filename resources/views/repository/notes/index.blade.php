@extends('admin.adminhome')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📂 Manage Public Notes</h3>
        <a href="{{ route('repo.notes.create') }}" class="btn btn-primary">
            <i class="fas fa-upload me-2"></i> Upload New Note
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="p-3">Title</th>
                        <th class="p-3">Subject Tag</th>
                        <th class="p-3">Uploaded On</th>
                        <th class="p-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                    <tr>
                        <td class="p-3 fw-bold">{{ $note->title }}</td>
                        <td class="p-3"><span class="badge bg-info text-dark">{{ $note->subject_tag }}</span></td>
                        <td class="p-3">{{ $note->created_at->format('d M Y') }}</td>
                        <td class="p-3 text-end">
                            <form action="{{ route('repo.notes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('Delete this note?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection