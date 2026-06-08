@extends('admin.adminhome')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-0">Modify Question Parameters</h2>
            <p class="text-muted mb-0">Parent Pool Link: <strong class="text-primary">{{ $question->quiz->title }}</strong></p>
        </div>
        <a href="{{ route('quizzes.manage', $question->quiz_id) }}" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-left-primary">
                <div class="card-header bg-white">
                    <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-pen mr-2 text-primary"></i>Edit Layout Form</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('questions.update', $question->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-uppercase">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="3" required>{{ $question->question_text }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="small font-weight-bold text-uppercase">Points</label>
                                <input type="number" name="points" class="form-control" value="{{ $question->points }}" min="1">
                            </div>
                            <div class="col-md-8">
                                <label class="small font-weight-bold text-uppercase">Question Type</label>
                                <select name="question_type" id="editTypeSelector" class="form-control">
                                    <option value="single" {{ $question->question_type == 'single' ? 'selected' : '' }}>Single Choice (Radio)</option>
                                    <option value="multiple" {{ $question->question_type == 'multiple' ? 'selected' : '' }}>Multiple Correct (Checkbox)</option>
                                    <option value="text" {{ $question->question_type == 'text' ? 'selected' : '' }}>Fill in the Blanks</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div id="editChoicesSection" style="{{ $question->question_type == 'text' ? 'display:none;' : '' }}">
                            <label class="small font-weight-bold text-uppercase mb-2">Options Grid</label>
                            
                            <div id="editOptionsContainer">
                                @if($question->question_type != 'text')
                                    @foreach($question->options as $index => $opt)
                                    <div class="input-group mb-2 option-row">
                                        <div class="input-group-text bg-white">
                                            <input type="radio" name="correct_single" value="{{ $index }}" class="type-radio" {{ $opt->is_correct && $question->question_type == 'single' ? 'checked' : '' }} style="{{ $question->question_type == 'single' ? 'display:inline-block;' : 'display:none;' }}">
                                            <input type="checkbox" name="correct_multiple[]" value="{{ $index }}" class="type-check" {{ $opt->is_correct && $question->question_type == 'multiple' ? 'checked' : '' }} style="{{ $question->question_type == 'multiple' ? 'display:inline-block;' : 'display:none;' }}">
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
                                        <input type="text" name="options[]" class="form-control" placeholder="Option 1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-btn"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>

                            <button type="button" class="btn btn-sm btn-info mt-2" id="editAddOptionBtn">
                                <i class="fas fa-plus"></i> Add Another Option
                            </button>
                        </div>

                        <div id="editTextSection" style="{{ $question->question_type == 'text' ? 'display:block;' : 'display:none;' }}">
                            <label class="small font-weight-bold text-uppercase mb-2">Correct Answer</label>
                            <input type="text" name="text_answer" class="form-control" value="{{ $question->question_type == 'text' && $question->options->first() ? $question->options->first()->option_text : '' }}" placeholder="Enter exact answer string...">
                        </div>

                        <button type="submit" class="btn btn-success btn-block mt-4 shadow-sm font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Update Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editTypeSelector = document.getElementById('editTypeSelector');
        const editChoicesSection = document.getElementById('editChoicesSection');
        const editTextSection = document.getElementById('editTextSection');
        const editOptionsContainer = document.getElementById('editOptionsContainer');
        const editAddOptionBtn = document.getElementById('editAddOptionBtn');

        editTypeSelector.addEventListener('change', function() {
            let type = this.value;
            if (type === 'text') {
                editChoicesSection.style.display = 'none';
                editTextSection.style.display = 'block';
                toggleInputs(editChoicesSection, true);
                toggleInputs(editTextSection, false);
            } else {
                editChoicesSection.style.display = 'block';
                editTextSection.style.display = 'none';
                toggleInputs(editChoicesSection, false);
                toggleInputs(editTextSection, true);

                let radios = editChoicesSection.querySelectorAll('.type-radio');
                let checks = editChoicesSection.querySelectorAll('.type-check');
                
                if (type === 'multiple') {
                    radios.forEach(el => el.style.display = 'none');
                    checks.forEach(el => el.style.display = 'inline-block');
                } else {
                    radios.forEach(el => el.style.display = 'inline-block');
                    checks.forEach(el => el.style.display = 'none');
                }
            }
        });

        function toggleInputs(section, disable) {
            section.querySelectorAll('input, select').forEach(el => el.disabled = disable);
        }

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
                    reindexOptions();
                }
            }
        });

        function reindexOptions() {
            let rows = editOptionsContainer.querySelectorAll('.option-row');
            rows.forEach((row, index) => {
                row.querySelector('.type-radio').value = index;
                row.querySelector('.type-check').value = index;
                row.querySelector('input[type="text"]').placeholder = `Option ${index + 1}`;
            });
        }
    });
</script>
@endsection