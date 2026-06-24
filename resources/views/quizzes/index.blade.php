@extends('admin.adminhome')

@section('content')
<style>
    :root { --maroon: #5b1a1a; }
    .quiz-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
    
    /* Card Design */
    .quiz-card { background: #fff; border-radius: 16px; border: 1px solid #edf2f7; padding: 25px; height: 100%; display: flex; flex-direction: column; transition: 0.3s; position: relative; }
    .quiz-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
    
    /* Difficulty Badges */
    .difficulty-badge { padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; position: absolute; top: 25px; right: 25px; }
    .diff-easy { background: #f0fff4; color: #38a169; border: 1px solid #c6f6d5; }
    .diff-medium { background: #fffaf0; color: #dd6b20; border: 1px solid #feebc8; }
    .diff-hard { background: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }

    /* Stats Row - Simplified to only Questions and Duration */
    .stats-row { display: flex; justify-content: space-around; text-align: center; border-bottom: 1px solid #edf2f7; padding: 20px 0; margin-bottom: 20px; }
    .stat-item h4 { font-size: 1.5rem; font-weight: 800; color: #2d3748; margin-bottom: 2px; }
    .stat-item span { font-size: 0.7rem; color: #718096; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
    
    .subject-pill { background: #fffbe6; color: #d4a017; border: 1px solid #ffe58f; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold" style="color: #1a202c;">Quiz Management</h2>
            <p class="text-muted">Create and manage your student assessments</p>
        </div>
        <a href="{{ route('quizzes.create') }}" class="btn px-4 py-2" style="background:var(--maroon); color:white; border-radius:10px; font-weight: 600;">
            <i class="fas fa-plus mr-2"></i> Create Quiz
        </a>
    </div>

    <div class="bg-white p-3 rounded-lg shadow-sm mb-4 d-flex gap-3 align-items-center">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-right-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="quizSearch" class="form-control border-left-0" placeholder="Search quizzes...">
        </div>
        
        <select id="subjectFilter" class="form-control w-auto">
            <option value="">All Subjects</option>
            @foreach($subjects as $s)
                <option value="{{ $s->subject_name }}">{{ $s->subject_name }}</option>
            @endforeach
        </select>

        <select id="difficultyFilter" class="form-control w-auto">
            <option value="">All Difficulties</option>
            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>
        </select>
    </div>

    <div class="quiz-grid" id="quizContainer">
        @foreach ($quizzes as $quiz)
        <div class="quiz-item" 
             data-title="{{ strtolower($quiz->title) }}" 
             data-topic="{{ strtolower($quiz->topic) }}" 
             data-subject="{{ $quiz->subject_id == 0 ? 'global reservoir' : ($quiz->subject ? $quiz->subject->subject_name : 'unassigned') }}"
             data-difficulty="{{ $quiz->difficulty }}">
            <div class="quiz-card">
                <span class="difficulty-badge diff-{{ strtolower($quiz->difficulty) }}">
                    {{ $quiz->difficulty }}
                </span>

                <div class="mb-2">
                    <h4 class="font-weight-bold mb-1" style="color: #2d3748; padding-right: 80px;">{{ $quiz->title }}</h4>
                    <p class="text-muted small mb-3">{{ Str::limit($quiz->description, 60) }}</p>
                </div>

                <div class="mb-4">
                    @if($quiz->subject_id == 0 || !$quiz->subject)
                        <span class="subject-pill bg-dark text-white border-0"><i class="fas fa-globe mr-1"></i>Global Reservoir</span>
                    @else
                        <span class="subject-pill">{{ $quiz->subject->subject_name }}</span>
                    @endif
                    <span class="text-muted small ml-2">{{ $quiz->topic }}</span>
                </div>

                <div class="stats-row">
                    <div class="stat-item">
                        <h4>{{ $quiz->questions->count() }}</h4>
                        <span>Questions</span>
                    </div>
                    <div class="stat-item">
                        <h4>{{ $quiz->duration_minutes }}m</h4>
                        <span>Duration</span>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <div class="small font-weight-bold mb-1 text-success">
                        <i class="fas fa-globe mr-2"></i>Global Assessment
                    </div>
                </div>

                <div class="mt-auto d-flex gap-2">
                    <a href="{{ route('quizzes.manage', $quiz->id) }}" class="btn btn-sm btn-light flex-grow-1 border" style="background: #ebf8ff; color: #3182ce; border: none !important;"><i class="fas fa-chart-bar mr-1"></i> Manage</a>
                    <a href="{{ route('quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-light flex-grow-1 border" style="background: #fffbe6; color: #d4a017; border: none !important;"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" style="background: #fff5f5; color: #e53e3e;" onclick="return confirm('Delete Quiz?')"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('quizSearch');
        const subjectFilter = document.getElementById('subjectFilter');
        const diffFilter = document.getElementById('difficultyFilter');
        const items = document.querySelectorAll('.quiz-item');

        function filter() {
            const search = searchInput.value.toLowerCase();
            const subject = subjectFilter.value;
            const diff = diffFilter.value;

            items.forEach(item => {
                const matchesSearch = item.dataset.title.includes(search) || item.dataset.topic.includes(search);
                const matchesSubject = subject === "" || item.dataset.subject === subject;
                const matchesDiff = diff === "" || item.dataset.difficulty === diff;

                if (matchesSearch && matchesSubject && matchesDiff) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        }

        searchInput.addEventListener('input', filter);
        subjectFilter.addEventListener('change', filter);
        diffFilter.addEventListener('change', filter);
    });
</script>
@endsection