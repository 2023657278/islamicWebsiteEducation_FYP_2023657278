@extends('admin.adminhome')

@section('content')
<div class="container-fluid p-4">
    <h2 class="mb-4 fw-bold">🗂️ Flashcard Manager</h2>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        {{-- MANUAL UPLOAD --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">➕ Manual Upload</div>
                <div class="card-body">
                    <form action="{{ route('flashcards.store') }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Subject</label>
                            <select name="subject_id" class="form-control" required>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Topic</label>
                            <input type="text" name="topic" class="form-control" placeholder="e.g. Tajweed" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Question</label>
                            <textarea name="question" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Answer</label>
                            <textarea name="answer" class="form-control" rows="2" required></textarea>
                        </div>
                        <button class="btn btn-primary w-100 mt-2">Save Card</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- AUTO GENERATE --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-info">
                <div class="card-header bg-info text-white fw-bold">⚡ Auto-Generate from Quiz</div>
                <div class="card-body">
                    <p class="text-muted small">First select a subject, then choose a quiz to import.</p>
                    
                    <form action="{{ route('flashcards.import') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Filter by Subject</label>
                            <select id="importSubjectFilter" name="subject_id" class="form-control" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Select Quiz</label>
                            <select id="quizSelect" name="quiz_id" class="form-control" disabled required>
                                <option value="">-- First Select a Subject --</option>
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}" data-subject="{{ $quiz->subject_id }}">
                                        {{ $quiz->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info text-white w-100">
                            Generate Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Flashcard List</h4>
        <div class="d-flex gap-2">
            {{-- ✅ TOPIC SEARCH --}}
            <div class="input-group shadow-sm" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="topicSearch" class="form-control border-start-0" placeholder="Search topic...">
            </div>

            {{-- ✅ SUBJECT FILTER --}}
            <div class="d-flex gap-2 align-items-center bg-white px-2 rounded shadow-sm border">
                <i class="fas fa-filter text-muted ms-1"></i>
                <select id="tableSubjectFilter" class="form-select form-select-sm border-0" style="width: 180px; cursor: pointer;">
                    <option value="all">All Subjects</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->subject_name }}">{{ $sub->subject_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="flashcardsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Topic</th>
                        <th>Subject</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flashcards as $card)
                    <tr class="flashcard-row" 
                        data-subject="{{ $card->subject->subject_name ?? 'General' }}" 
                        data-topic="{{ strtolower($card->topic) }}">
                        <td><span class="badge bg-secondary">{{ $card->topic }}</span></td>
                        <td><small class="fw-bold text-primary">{{ $card->subject->subject_name ?? 'General' }}</small></td>
                        <td>{{ Str::limit($card->question, 40) }}</td>
                        <td class="text-success fw-bold">{{ Str::limit($card->answer, 40) }}</td>
                        <td>
                            <form action="{{ route('flashcards.destroy', $card->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-4 text-muted">No flashcards created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Logic for Auto-Generate Filtering
        const importSubjectSelect = document.getElementById('importSubjectFilter');
        const quizSelect = document.getElementById('quizSelect');
        const allQuizzes = Array.from(quizSelect.options);

        importSubjectSelect.addEventListener('change', function() {
            const selectedId = this.value;
            quizSelect.innerHTML = '<option value="">-- Select Quiz --</option>';
            quizSelect.disabled = true;

            if (selectedId) {
                const filtered = allQuizzes.filter(opt => opt.getAttribute('data-subject') == selectedId);
                if (filtered.length > 0) {
                    filtered.forEach(opt => quizSelect.appendChild(opt));
                    quizSelect.disabled = false;
                } else {
                    quizSelect.innerHTML = '<option value="">No quizzes found</option>';
                }
            }
        });

        // ✅ COMBINED FILTERING LOGIC (Topic Search + Subject Filter)
        const tableFilter = document.getElementById('tableSubjectFilter');
        const topicSearch = document.getElementById('topicSearch');
        const rows = document.querySelectorAll('.flashcard-row');

        function applyFilters() {
            const selectedSubject = tableFilter.value;
            const searchKeyword = topicSearch.value.toLowerCase();

            rows.forEach(row => {
                const rowSubject = row.getAttribute('data-subject');
                const rowTopic = row.getAttribute('data-topic');

                const matchesSubject = (selectedSubject === 'all' || rowSubject === selectedSubject);
                const matchesTopic = rowTopic.includes(searchKeyword);

                if (matchesSubject && matchesTopic) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        tableFilter.addEventListener('change', applyFilters);
        topicSearch.addEventListener('input', applyFilters);
    });
</script>
@endsection