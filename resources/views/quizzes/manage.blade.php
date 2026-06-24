@extends('admin.adminhome')

@section('content')
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-0">{{ $quiz->title }}</h2>
            <p class="text-muted mb-0">
                <span class="badge badge-info">{{ $quiz->subject->subject_name }}</span>
                <span class="badge badge-secondary">{{ $quiz->topic ?? 'General' }}</span>
                <span class="badge badge-warning text-dark">{{ $quiz->difficulty }}</span>
            </p>
        </div>
        <a href="{{ url(route('quizzes.index')) }}" class="btn btn-secondary">Done</a>
    </div>

    <div class="row">
        
        {{-- LEFT SIDE: DYNAMIC WORKSPACE FORM (MANUAL ENTRY & AL-FALAH QUESTION BANK) --}}
        <div class="col-lg-5 mb-4">
            
            {{-- Navigation Tabs --}}
            <ul class="nav nav-tabs mb-3 shadow-sm p-1 bg-white rounded" id="creationTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="manual-tab" data-toggle="tab" href="#manualWorkspace" role="tab">Manual Creation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-success" id="bank-tab" data-toggle="tab" href="#bankWorkspace" role="tab">
                        <i class="fas fa-university mr-1"></i> Al-Falah Bank
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="creationTabContent">
                
                {{-- TAB A: ORIGINAL MANUAL ENTRY FORM BLOCK --}}
                <div class="tab-pane fade show active" id="manualWorkspace" role="tabpanel">
                    <div class="card shadow-sm {{ $editingQuestion ? 'border-left-warning shadow' : 'border-left-primary' }}">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold {{ $editingQuestion ? 'text-warning' : 'text-primary' }}">
                                {{ $editingQuestion ? 'Edit Question Mode' : 'Add Question' }}
                            </h5>
                            @if($editingQuestion)
                                <a href="{{ route('quizzes.manage', $quiz->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill">Cancel Edit</a>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ $editingQuestion ? route('questions.update', $editingQuestion->id) : route('questions.store', $quiz->id) }}" method="POST">
                                @csrf
                                @if($editingQuestion)
                                    @method('PUT')
                                @endif
                                
                                <div class="form-group mb-3">
                                    <label class="small font-weight-bold text-uppercase">Question Text</label>
                                    <textarea name="question_text" class="form-control" rows="2" required placeholder="Type question here...">{{ $editingQuestion ? $editingQuestion->question_text : '' }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="small font-weight-bold text-uppercase">Points</label>
                                        <input type="number" name="points" class="form-control" value="{{ $editingQuestion ? $editingQuestion->points : '1' }}" min="1">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="small font-weight-bold text-uppercase">Question Type</label>
                                        @php $qType = $editingQuestion ? $editingQuestion->question_type : 'single'; @endphp
                                        <select name="question_type" id="typeSelector" class="form-control">
                                            <option value="single" {{ $qType == 'single' ? 'selected' : '' }}>Single Choice (Radio)</option>
                                            <option value="multiple" {{ $qType == 'multiple' ? 'selected' : '' }}>Multiple Correct (Checkbox)</option>
                                            <option value="text" {{ $qType == 'text' ? 'selected' : '' }}>Fill in the Blanks</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                {{-- CHOICES INTERFACE SECTION --}}
                                <div id="choicesSection" style="{{ $qType == 'text' ? 'display: none;' : '' }}">
                                    <label class="small font-weight-bold text-uppercase mb-2">Options</label>
                                    <small class="d-block text-muted mb-2">Check the box/radio for the correct answer(s).</small>
                                    
                                    <div id="optionsContainer">
                                        @if($editingQuestion && $editingQuestion->question_type != 'text')
                                            @foreach($editingQuestion->options as $index => $opt)
                                            <div class="input-group mb-2 option-row">
                                                <div class="input-group-text bg-white">
                                                    <input type="radio" name="correct_single" value="{{ $index }}" class="type-radio" {{ $opt->is_correct && $qType == 'single' ? 'checked' : '' }} style="{{ $qType == 'single' ? '' : 'display:none;' }}">
                                                    <input type="checkbox" name="correct_multiple[]" value="{{ $index }}" class="type-check" {{ $opt->is_correct && $qType == 'multiple' ? 'checked' : '' }} style="{{ $qType == 'multiple' ? '' : 'display:none;' }}">
                                                </div>
                                                <input type="text" name="options[]" class="form-control" value="{{ $opt->option_text }}" required>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 option-row">
                                                <div class="input-group-text bg-white">
                                                    <input type="radio" name="correct_single" value="0" class="type-radio" checked>
                                                    <input type="checkbox" name="correct_multiple[]" value="0" class="type-check" style="display:none;">
                                                </div>
                                                <input type="text" name="options[]" class="form-control" placeholder="Option 1" {{ $editingQuestion ? 'disabled' : 'required' }}>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                            </div>
                                            <div class="input-group mb-2 option-row">
                                                <div class="input-group-text bg-white">
                                                    <input type="radio" name="correct_single" value="1" class="type-radio">
                                                    <input type="checkbox" name="correct_multiple[]" value="1" class="type-check" style="display:none;">
                                                </div>
                                                <input type="text" name="options[]" class="form-control" placeholder="Option 2" {{ $editingQuestion ? 'disabled' : 'required' }}>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-sm btn-info mt-2" id="addOptionBtn">
                                        <i class="fas fa-plus"></i> Add Another Option
                                    </button>
                                </div>

                                {{-- FILL IN THE BLANK TEXT SECTION --}}
                                <div id="textSection" style="{{ $qType == 'text' ? '' : 'display: none;' }}">
                                    <label class="small font-weight-bold text-uppercase mb-2">Correct Answer</label>
                                    @php $txtAns = ($editingQuestion && $editingQuestion->question_type == 'text' && $editingQuestion->options->first()) ? $editingQuestion->options->first()->option_text : ''; @endphp
                                    <input type="text" name="text_answer" class="form-control" value="{{ $txtAns }}" placeholder="Enter the exact correct answer here..." {{ $qType == 'text' ? 'required' : 'disabled' }}>
                                    <small class="text-muted">Exact match required for auto-grading.</small>
                                </div>

                                <button type="submit" class="btn {{ $editingQuestion ? 'btn-warning' : 'btn-primary' }} btn-block mt-4 shadow-sm font-weight-bold">
                                    <i class="fas fa-save mr-1"></i> {{ $editingQuestion ? 'Update Question Parameters' : 'Save Question' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- TAB B: NEW KEYWORD SEARCH & DISTRACTOR AUTO-FILL GENERATOR --}}
                <div class="tab-pane fade" id="bankWorkspace" role="tabpanel">
                    <div class="card shadow-sm border-left-success">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 font-weight-bold text-success"><i class="fas fa-search-plus mr-1"></i> Query Live Repository</h5>
                        </div>
                        <div class="card-body">
                            
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-success text-uppercase">Enter Search Keyword</label>
                                <div class="input-group">
                                    <input type="text" id="bankKeywordField" class="form-control" placeholder="e.g., 'Mad Silah', 'Rasuah', 'Haji'...">
                                    <div class="input-group-append">
                                        <button type="button" id="triggerSearchBtn" class="btn btn-success font-weight-bold"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Result Dynamic Target Block --}}
                            <div id="bankQueryListWrapper" class="list-group mb-3 shadow-inner" style="max-height: 200px; overflow-y: auto; border: 1px solid #e3e6f0; border-radius: 5px;">
                                <p class="text-muted text-center small py-3 my-0">Type keywords above to pull corresponding textbook lines.</p>
                            </div>

                            {{-- MASTER AUTO-FILL INTERFACE FORM --}}
                            <div id="bankCollectorFormBlock" style="display: none;" class="bg-light p-3 rounded border">
                                <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-edit mr-1 text-info"></i> Auto-Filled Component Profile</h6>
                                
                                <form action="{{ route('questions.bank.attach', $quiz->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="bank_question_id" id="targetBankQuestionId">

                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-muted text-uppercase">Extracted Question Text</label>
                                        <textarea id="boxPreviewQuestion" class="form-control bg-white text-dark font-weight-bold" rows="2" readonly></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-success text-uppercase">Extracted Verified Answer</label>
                                        <input type="text" id="boxPreviewAnswer" class="form-control bg-white text-success font-weight-bold" readonly>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-dark text-uppercase">Points Multiplier</label>
                                        <input type="number" name="points" class="form-control" value="2" min="1">
                                    </div>

                                    {{-- DYNAMIC DISTRACTOR ROWS WRITTEN BY THE TEACHER --}}
                                    <div class="form-group mb-2 card p-2 border-danger">
                                        <label class="small font-weight-bold text-danger text-uppercase mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Formulate Wrong Choices</label>
                                        <small class="text-muted d-block mb-2">Please fill in three incorrect answer choices for students below:</small>
                                        
                                        <input type="text" name="wrong_options[]" class="form-control form-control-sm mb-2 border-left-danger" required placeholder="Type Wrong Answer Option 1">
                                        <input type="text" name="wrong_options[]" class="form-control form-control-sm mb-2 border-left-danger" required placeholder="Type Wrong Answer Option 2">
                                        <input type="text" name="wrong_options[]" class="form-control form-control-sm mb-2 border-left-danger" required placeholder="Type Wrong Answer Option 3">
                                    </div>

                                    <button type="submit" class="btn btn-success btn-block font-weight-bold mt-3 shadow-sm">
                                        <i class="fas fa-check-circle mr-1"></i> Inject Complete Profile to Quiz
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE: QUESTIONS PANEL LIST --}}
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="m-0 font-weight-bold text-dark">Questions ({{ $quiz->questions->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($quiz->questions as $index => $q)
                    <div class="border-bottom p-3 {{ ($editingQuestion && $editingQuestion->id == $q->id) ? 'bg-light border-left-warning' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong style="max-width: 70%;">Q{{ $index + 1 }}. {{ $q->question_text }}</strong>
                            <div>
                                <span class="badge badge-secondary">{{ ucfirst($q->question_type) }}</span>
                                <span class="badge badge-warning text-dark">{{ $q->points }} pts</span>
                                
                                <a href="{{ route('quizzes.manage', [$quiz->id, 'edit_question_id' => $q->id]) }}" class="btn btn-sm text-primary p-0 ml-2" title="Edit row data parameters">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="{{ route('questions.destroy', $q->id) }}" method="POST" class="d-inline ml-2">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm text-danger border-0 bg-transparent p-0" onclick="return confirm('Delete this question?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="mt-2 ml-3">
                            @if($q->question_type == 'text')
                                <span class="text-success font-weight-bold">Answer: {{ $q->options->first()->option_text ?? '-' }}</span>
                            @else
                                <ul class="list-unstyled mb-0">
                                    @foreach($q->options as $opt)
                                    <li class="{{ $opt->is_correct ? 'text-success font-weight-bold' : 'text-muted' }}">
                                        <i class="fas {{ $opt->is_correct ? 'fa-check-circle' : 'fa-circle' }}"></i> 
                                        {{ $opt->option_text }}
                                    </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">No questions yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- MANUAL WORKSPACE INTERACTION CODE ---
        const typeSelector = document.getElementById('typeSelector');
        const choicesSection = document.getElementById('choicesSection');
        const textSection = document.getElementById('textSection');
        const optionsContainer = document.getElementById('optionsContainer');
        const addOptionBtn = document.getElementById('addOptionBtn');

        if(typeSelector) {
            typeSelector.addEventListener('change', function() {
                let type = this.value;
                if (type === 'text') {
                    choicesSection.style.display = 'none';
                    textSection.style.display = 'block';
                    toggleInputs(choicesSection, true); 
                    toggleInputs(textSection, false);
                } else {
                    choicesSection.style.display = 'block';
                    textSection.style.display = 'none';
                    toggleInputs(choicesSection, false);
                    toggleInputs(textSection, true);

                    let radios = document.querySelectorAll('.type-radio');
                    let checks = document.querySelectorAll('.type-check');
                    
                    if (type === 'multiple') {
                        radios.forEach(el => el.style.display = 'none');
                        checks.forEach(el => el.style.display = 'inline-block');
                    } else {
                        radios.forEach(el => el.style.display = 'inline-block');
                        checks.forEach(el => el.style.display = 'none');
                    }
                }
            });
        }

        function toggleInputs(section, disable) {
            section.querySelectorAll('input, textarea, select').forEach(el => el.disabled = disable);
        }

        if(addOptionBtn) {
            addOptionBtn.addEventListener('click', function() {
                let index = optionsContainer.children.length;
                let type = typeSelector.value;
                let displayRadio = (type === 'single') ? 'inline-block' : 'none';
                let displayCheck = (type === 'multiple') ? 'inline-block' : 'none';

                let html = `
                    <div class="input-group mb-2 option-row">
                        <div class="input-group-text bg-white">
                            <input type="radio" name="correct_single" value="${index}" class="type-radio" style="display:${displayRadio}">
                            <input type="checkbox" name="correct_multiple[]" value="${index}" class="type-check" style="display:${displayCheck}">
                        </div>
                        <input type="text" name="options[]" class="form-control" placeholder="Option ${index + 1}" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                    </div>`;
                optionsContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        if(optionsContainer) {
            optionsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-btn')) {
                    if (optionsContainer.children.length > 1) {
                        e.target.closest('.option-row').remove();
                        reindexOptions();
                    } else {
                        alert("You need at least one option.");
                    }
                }
            });
        }

        function reindexOptions() {
            let rows = optionsContainer.querySelectorAll('.option-row');
            rows.forEach((row, index) => {
                row.querySelector('.type-radio').value = index;
                row.querySelector('.type-check').value = index;
                row.querySelector('input[type="text"]').placeholder = `Option ${index + 1}`;
            });
        }

        // --- AL-FALAH QUESTION BANK KEYWORD RETRIEVAL SYSTEM ---
        const keywordField = document.getElementById('bankKeywordField');
        const triggerBtn = document.getElementById('triggerSearchBtn');
        const resultsBox = document.getElementById('bankQueryListWrapper');

        const formBlock = document.getElementById('bankCollectorFormBlock');
        const hiddenId = document.getElementById('targetBankQuestionId');
        const previewQ = document.getElementById('boxPreviewQuestion');
        const previewA = document.getElementById('boxPreviewAnswer');

        function performLiveBankQuery() {
            let keyword = keywordField.value.trim();
            if(keyword.length < 2) {
                resultsBox.innerHTML = '<div class="alert alert-warning small m-2 p-2 text-center">Type at least 2 characters to search.</div>';
                return;
            }

            resultsBox.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin text-success mr-1"></i> Querying 702 repository lines...</div>';

            fetch(`{{ url('/question-bank/search') }}?keyword=${encodeURIComponent(keyword)}`)
                ->then(response => response.json())
                ->then(data => {
                    resultsBox.innerHTML = '';
                    if(data.length === 0) {
                        resultsBox.innerHTML = '<p class="text-muted text-center small py-3 my-0">No matching questions found.</p>';
                        return;
                    }

                    data.forEach(q => {
                        let correctAnswerRow = q.options.find(o => o.is_correct == 1);
                        let answerTextStr = correctAnswerRow ? correctAnswerRow.option_text : 'No explicit answer bound.';

                        let itemHtml = `
                            <div class="list-group-item p-2 mb-1 border-left-success d-flex justify-content-between align-items-center bg-white shadow-xs">
                                <div style="max-width: 75%;">
                                    <p class="mb-0 text-dark font-weight-bold small text-justify">${q.question_text}</p>
                                    <small class="text-success"><i class="fas fa-check mr-1"></i>${answerTextStr}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-success px-3 rounded-pill action-select-q" 
                                        data-id="${q.id}" 
                                        data-question="${encodeURIComponent(q.question_text)}" 
                                        data-answer="${encodeURIComponent(answerTextStr)}">
                                    Select
                                </button>
                            </div>`;
                        resultsBox.insertAdjacentHTML('beforeend', itemHtml);
                    });
                });
        }

        if(triggerBtn) {
            triggerBtn.addEventListener('click', performLiveBankQuery);
            keywordField.addEventListener('keyup', function(e) { if(e.key === 'Enter') performLiveBankQuery(); });
        }

        if(resultsBox) {
            resultsBox.addEventListener('click', function(e) {
                if(e.target.classList.contains('action-select-q')) {
                    const btnNode = e.target;
                    
                    hiddenId.value = btnNode.getAttribute('data-id');
                    previewQ.value = decodeURIComponent(btnNode.getAttribute('data-question'));
                    previewA.value = decodeURIComponent(btnNode.getAttribute('data-answer'));

                    // Smooth transition visibility scroll trigger
                    formBlock.style.display = 'block';
                    formBlock.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });
        }
    });
</script>
@endsection