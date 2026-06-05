@extends('admin.adminhome')

@section('content')
<style>
    .glass-card { background: rgba(255, 255, 255, 0.95); border-radius: 20px; padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
    .btn-maroon { background-color: #800000; color: white; border-radius: 50px; padding: 10px 30px; border:none; font-weight: bold; }
</style>

<div class="container py-4 text-start">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="fw-bold mb-4"><i class="fas fa-envelope-open-text me-2"></i>Compose Message</h3>

            <div class="glass-card">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    
                    {{-- 1. TYPE SELECTION --}}
                    <div class="mb-4">
                        <label class="fw-bold text-muted small mb-2">Message Type</label>
                        <select name="type" id="messageType" class="form-select" onchange="toggleRecipientField()">
                            <option value="">-- Select Type --</option>
                            
                            {{-- ADMIN OPTIONS --}}
                            @if($role === 'admin')
                                <option value="global">📢 Global Announcement (Everyone)</option>
                                <option value="group">🏫 Group Announcement</option>
                                <option value="private">👤 Private Message</option>
                            @endif

                            {{-- TEACHER OPTIONS --}}
                            @if($role === 'teacher')
                                <option value="group">🏫 Announcement to My Class</option>
                                <option value="private">👤 Private Message (Student/Admin)</option>
                            @endif

                            {{-- STUDENT OPTIONS --}}
                            @if($role === 'student')
                                <option value="private">👤 Private Message (Teacher/Admin)</option>
                            @endif
                        </select>
                    </div>

                    {{-- 2. RECIPIENT FIELDS --}}
                    
                    {{-- GROUP SELECTOR --}}
                    @if($role !== 'student') 
                    <div class="mb-4" id="groupField" style="display: none;">
                        <label class="fw-bold text-muted small mb-2">Select Class Group</label>
                        <select name="target_id" id="groupInput" class="form-select" disabled>
                            <option value="">-- Choose Class --</option>
                            @foreach($groups as $group)
                                {{-- 🟢 FIXED: Uses accessor to output "4Amanah (2025)" inside option dropdown values --}}
                                <option value="{{ $group->id }}">{{ $group->group_with_year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- PRIVATE RECIPIENT SELECTOR --}}
                    <div class="mb-4" id="userField" style="display: none;">
                        <label class="fw-bold text-muted small mb-2">Select Recipient</label>
                        <select name="target_id" id="userInput" class="form-select" disabled>
                            <option value="">-- Choose Person --</option>
                            @foreach($recipients as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} 
                                    @if($user->role == 'admin') (Admin) 
                                    @elseif($user->role == 'teacher') (Teacher)
                                    @else (Student) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. CONTENT --}}
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold text-muted small">Message</label>
                        <textarea name="message" rows="5" class="form-control" required></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-maroon">Send Message <i class="fas fa-paper-plane ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRecipientField() {
        var type = document.getElementById('messageType').value;
        
        var groupField = document.getElementById('groupField');
        var userField  = document.getElementById('userField');
        var groupInput = document.getElementById('groupInput');
        var userInput  = document.getElementById('userInput');

        if(groupField) groupField.style.display = 'none';
        if(userField) userField.style.display = 'none';
        
        if(groupInput) groupInput.disabled = true;
        if(userInput) userInput.disabled = true;

        if (type === 'group') {
            if(groupField) {
                groupField.style.display = 'block';
                groupInput.disabled = false;
            }
        } else if (type === 'private') {
            if(userField) {
                userField.style.display = 'block';
                userInput.disabled = false;
            }
        }
    }
</script>
@endsection