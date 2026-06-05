@extends('admin.adminhome')

@section('content')
<style>
    /* Premium UI Card Upgrades */
    .premium-heading { color: #1e293b; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px; }
    .glass-card { background: #ffffff; border-radius: 24px; padding: 40px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05); }
    
    /* Modern Form Controls */
    .form-label-custom { font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
    .form-control-modern { background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; color: #0f172a; font-size: 0.95rem; transition: all 0.2s ease-in-out; }
    .form-control-modern:focus { background-color: #ffffff; border-color: #800000; box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.1); outline: none; }
    
    /* Button Enhancements */
    .btn-maroon { background-color: #800000; color: white; border-radius: 14px; padding: 14px 32px; border: none; font-weight: 700; font-size: 0.95rem; transition: all 0.2s ease; box-shadow: 0 4px 6px -1px rgba(128, 0, 0, 0.2); }
    .btn-maroon:hover { background-color: #660000; transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(128, 0, 0, 0.3); }
    .btn-maroon:active { transform: translateY(0); }
</style>

<div class="container py-4 text-start">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <div class="p-3 bg-white rounded-4 shadow-sm border me-3 text-dark">
                    <i class="fas fa-envelope-open-text fa-lg" style="color: #800000;"></i>
                </div>
                <div>
                    <h3 class="premium-heading mb-0">Compose Announcement</h3>
                    <p class="text-muted small mb-0">Broadcast school updates or directly contact institutional groups.</p>
                </div>
            </div>

            <div class="glass-card">
                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-3 shadow-sm p-3 mb-4 d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    
                    {{-- 1. TYPE SELECTION --}}
                    <div class="mb-4">
                        <label class="form-label-custom">Message Classification</label>
                        <select name="type" id="messageType" class="form-select form-control-modern" onchange="toggleRecipientField()" required>
                            <option value="">-- Select Broadcast Channels --</option>
                            
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
                        <label class="form-label-custom">Target Class Group</label>
                        <select name="target_id" id="groupInput" class="form-select form-control-modern" disabled required>
                            <option value="">-- Choose Class Segment --</option>
                            @foreach($groups as $group)
                                {{-- 🟢 FIXED: Shows Form/Year structural constraints side-by-side with Group names cleanly --}}
                                <option value="{{ $group->id }}">
                                    {{ $group->group_name }} 
                                    @if(isset($group->year)) ({{ $group->year->year_name ?? 'Form '.$group->year_id }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- PRIVATE RECIPIENT SELECTOR --}}
                    <div class="mb-4" id="userField" style="display: none;">
                        <label class="form-label-custom">Target Recipient</label>
                        <select name="target_id" id="userInput" class="form-select form-control-modern" disabled required>
                            <option value="">-- Choose Target Account Profile --</option>
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
                    <div class="mb-4">
                        <label class="form-label-custom">Subject / Headline Title</label>
                        <input type="text" name="subject" class="form-control form-control-modern" placeholder="Provide a summary header text..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom">Message Body Content</label>
                        <textarea name="message" rows="5" class="form-control form-control-modern" placeholder="Type your core notification or chat narrative parameters here..." required></textarea>
                    </div>

                    <div class="text-end pt-2">
                        <button type="submit" class="btn btn-maroon w-100 w-sm-auto">
                            Send Message <i class="fas fa-paper-plane ms-2"></i>
                        </button>
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