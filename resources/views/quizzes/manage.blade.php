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
        
        {{-- LEFT SIDE: ADD QUESTION FORM --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-left-primary">
                <div class="card-header bg-white">
                    <h5 class="m-0 font-weight-bold text-primary">Add Question</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('questions.store', $quiz->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-uppercase">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="2" required placeholder="Type question here..."></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-uppercase">Points</label>
                                <input type="number" name="points" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-8">
                                <label class="small font-weight-bold text-uppercase">Question Type</label>
                                <select name="question_type" id="typeSelector" class="form-control">
                                    <option value="single">Single Choice (Radio)</option>
                                    <option value="multiple">Multiple Correct (Checkbox)</option>
                                    <option value="text">Fill in the Blanks</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div id="choicesSection">
                            <label class="small font-weight-bold text-uppercase mb-2">Options</label>
                            <small class="d-block text-muted mb-2">Check the box/radio for the correct answer(s).</small>
                            
                            <div id="optionsContainer">
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="correct_single" value="0" class="type-radio" checked>
                                        <input type="checkbox" name="correct_multiple[]" value="0" class="type-check" style="display:none;">
                                    </div>
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="input-group mb-2 option-row">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="correct_single" value="1" class="type-radio">
                                        <input type="checkbox" name="correct_multiple[]" value="1" class="type-check" style="display:none;">
                                    </div>
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-info mt-2" id="addOptionBtn">
                                <i class="fas fa-plus"></i> Add Another Option
                            </button>
                        </div>

                        <div id="textSection" style="display: none;">
                            <label class="small font-weight-bold text-uppercase mb-2">Correct Answer</label>
                            <input type="text" name="text_answer" class="form-control" placeholder="Enter the exact correct answer here...">
                            <small class="text-muted">Exact match required for auto-grading.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-4 shadow-sm">
                            <i class="fas fa-save mr-1"></i> Save Question
                        </button>
                    </form>
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
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong style="max-width: 70%;">Q{{ $index + 1 }}. {{ $q->question_text }}</strong>
                            <div>
                                <span class="badge badge-secondary">{{ ucfirst($q->question_type) }}</span>
                                <span class="badge badge-warning text-dark">{{ $q->points }} pts</span>
                                
                                {{-- 🟢 ADDED: EDIT TRIGGER BUTTON (Triggers JS hydration matrix) --}}
                                <button type="button" 
                                        class="btn btn-sm text-primary border-0 bg-transparent p-0 ml-2 edit-question-btn"
                                        data-id="{{ $q->id }}"
                                        data-text="{{ $q->question_text }}"
                                        data-points="{{ $q->points }}"
                                        data-type="{{ $q->question_type }}"
                                        data-options='@json($q->options)'>
                                    <i class="fas fa-pen"></i>
                                </button>

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

{{-- 🟢 ADDED: BOOTSTRAP EDIT QUESTION MODAL BOX OVERLAY --}}
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-pen mr-2"></i>Edit Question</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editQuestionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-uppercase">Question Text</label>
                        <textarea name="question_text" id="edit_question_text" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-uppercase">Points</label>
                            <input type="number" name="points" id="edit_points" class="form-control" min="1">
                        </div>
                        <div class="col-md-8">
                            <label class="small font-weight-bold text-uppercase">Question Type</label>
                            <select name="question_type" id="editTypeSelector" class="form-control">
                                <option value="single">Single Choice (Radio)</option>
                                <option value="multiple">Multiple Correct (Checkbox)</option>
                                <option value="text">Fill in the Blanks</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div id="editChoicesSection">
                        <label class="small font-weight-bold text-uppercase mb-2">Options</label>
                        <div id="editOptionsContainer"></div>
                        <button type="button" class="btn btn-sm btn-info mt-2" id="editAddOptionBtn">
                            <i class="fas fa-plus"></i> Add Another Option
                        </button>
                    </div>

                    <div id="editTextSection" style="display: none;">
                        <label class="small font-weight-bold text-uppercase mb-2">Correct Answer</label>
                        <input type="text" name="text_answer" id="edit_text_answer" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- ADD SCREEN LOGIC ENGRAVINGS ---
        const typeSelector = document.getElementById('typeSelector');
        const choicesSection = document.getElementById('choicesSection');
        const textSection = document.getElementById('textSection');
        const optionsContainer = document.getElementById('optionsContainer');
        const addOptionBtn = document.getElementById('addOptionBtn');

        typeSelector.addEventListener('change', function() {
            handleTypeShift(this.value, choicesSection, textSection);
        });

        function handleTypeShift(type, choiceBlock, textBlock) {
            if (type === 'text') {
                choiceBlock.style.display = 'none';
                textBlock.style.display = 'block';
                toggleInputs(choiceBlock, true); 
                toggleInputs(textBlock, false);
            } else {
                choiceBlock.style.display = 'block';
                textBlock.style.display = 'none';
                toggleInputs(choiceBlock, false);
                toggleInputs(textBlock, true);

                let radios = choiceBlock.querySelectorAll('.type-radio');
                let checks = choiceBlock.querySelectorAll('.type-check');
                
                if (type === 'multiple') {
                    radios.forEach(el => el.style.display = 'none');
                    checks.forEach(el => el.style.display = 'inline-block');
                } else {
                    radios.forEach(el => el.style.display = 'inline-block');
                    checks.forEach(el => el.style.display = 'none');
                }
            }
        }

        function toggleInputs(section, disable) {
            section.querySelectorAll('input, textarea, select').forEach(el => el.disabled = disable);
        }

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

        optionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                if (optionsContainer.children.length > 1) {
                    e.target.closest('.option-row').remove();
                    reindexOptions(optionsContainer);
                }
            }
        });

        function reindexOptions(container) {
            let rows = container.querySelectorAll('.option-row');
            rows.forEach((row, index) => {
                let r = row.querySelector('.type-radio');
                let c = row.querySelector('.type-check');
                if(r) r.value = index;
                if(c) c.value = index;
                row.querySelector('input[type="text"]').placeholder = `Option ${index + 1}`;
            });
        }

        // --- 🟢 ADDED: EDIT MODAL DESERIALIZATION MACHINE ENGINE ---
        const editModal = $('#editQuestionModal');
        const editForm = document.getElementById('editQuestionForm');
        const editTypeSelector = document.getElementById('editTypeSelector');
        const editChoicesSection = document.getElementById('editChoicesSection');
        const editTextSection = document.getElementById('editTextSection');
        const editOptionsContainer = document.getElementById('editOptionsContainer');
        const editAddOptionBtn = document.getElementById('editAddOptionBtn');

        editTypeSelector.addEventListener('change', function() {
            handleTypeShift(this.value, editChoicesSection, editTextSection);
        });

        $('.edit-question-btn').on('click', function() {
            let id = $(this).data('id');
            let text = $(this).data('text');
            let points = $(this).data('points');
            let type = $(this).data('type');
            let options = $(this).data('options');

            // Set Form action mapping targets dynamically
            editForm.action = `/admin/questions/${id}/update`;

            document.getElementById('edit_question_text').value = text;
            document.getElementById('edit_points').value = points;
            editTypeSelector.value = type;

            editOptionsContainer.innerHTML = '';
            document.getElementById('edit_text_answer').value = '';

            if (type === 'text') {
                if(options.length > 0) {
                    document.getElementById('edit_text_answer').value = options[0].option_text;
                }
            } else {
                options.forEach((opt, idx) => {
                    let displayRadio = (type === 'single') ? 'inline-block' : 'none';
                    let displayCheck = (type === 'multiple') ? 'inline-block' : 'none';
                    let isCheckedRadio = (type === 'single' && opt.is_correct) ? 'checked' : '';
                    let isCheckedCheck = (type === 'multiple' && opt.is_correct) ? 'checked' : '';

                    let html = `
                        <div class="input-group mb-2 option-row">
                            <div class="input-group-text bg-white">
                                <input type="radio" name="correct_single" value="${idx}" class="type-radio" style="display:${displayRadio}" ${isCheckedRadio}>
                                <input type="checkbox" name="correct_multiple[]" value="${idx}" class="type-check" style="display:${displayCheck}" ${isCheckedCheck}>
                            </div>
                            <input type="text" name="options[]" class="form-control" value="${opt.option_text}" required>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                        </div>`;
                    editOptionsContainer.insertAdjacentHTML('beforeend', html);
                });
            }

            handleTypeShift(type, editChoicesSection, editTextSection);
            editModal.modal('show');
        });

        editAddOptionBtn.addEventListener('click', function() {
            let index = editOptionsContainer.children.length;
            let type = editTypeSelector.value;
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
            editOptionsContainer.insertAdjacentHTML('beforeend', html);
        });

        editOptionsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-btn')) {
                if (editOptionsContainer.children.length > 1) {
                    e.target.closest('.option-row').remove();
                    reindexOptions(editOptionsContainer);
                }
            }
        });
    });
</script>
@endsection