@extends('admin.adminhome')

@section('content')
<style>
    /* --- DARK MODE & WHATSAPP FLAT LAYOUT --- */
    :root {
        --dark-bg: #121212; 
        --dark-sidebar: #1f2c34; 
        --dark-header: #202c33;
        --dark-chat: #0b141a; 
        --dark-input: #2a3942; 
        --green-sent: #005c4b;
        --text-primary: #e9edef; 
        --text-secondary: #8696a0; 
        --maroon-accent: #800000;
    }

    .chat-wrapper { display: flex; align-items: center; justify-content: center; height: calc(100vh - 100px); padding-bottom: 10px; }
    .chat-container { display: flex; width: 100%; height: 100%; background-color: var(--dark-chat); border-radius: 20px; overflow: hidden; border: 1px solid #2f3b43; box-shadow: 0 12px 48px rgba(0,0,0,0.4); }

    /* --- SIDEBAR --- */
    .chat-sidebar { width: 340px; background-color: var(--dark-sidebar); border-right: 1px solid #2f3b43; display: flex; flex-direction: column; flex-shrink: 0; }
    .sidebar-search { padding: 16px; background-color: var(--dark-header); }
    .search-input { background-color: var(--dark-input); border: none; color: var(--text-primary); border-radius: 10px; padding: 10px 16px; width: 100%; outline: none; font-size: 0.9rem; }

    .contact-list { overflow-y: auto; flex: 1; }
    .section-title { color: #38a169; font-weight: 800; font-size: 0.72rem; padding: 20px 20px 8px 20px; text-transform: uppercase; letter-spacing: 1.2px; border-bottom: 1px solid rgba(255,255,255,0.02); }

    .contact-item { display: flex; align-items: center; padding: 14px 20px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.03); text-decoration: none; color: var(--text-primary); transition: all 0.2s ease; }
    .contact-item:hover { background-color: rgba(255,255,255,0.04); }
    .contact-item.active { background-color: var(--dark-input); border-left: 4px solid var(--maroon-accent); }
    
    .avatar { width: 44px; height: 44px; border-radius: 50%; background: #4a5568; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; margin-right: 14px; flex-shrink: 0; font-size: 1rem; }

    /* --- CHAT AREA --- */
    .chat-area { flex: 1; display: flex; flex-direction: column; background-color: var(--dark-chat); background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat; position: relative; }
    .chat-header { padding: 0 25px; background-color: var(--dark-header); border-bottom: 1px solid #2f3b43; display: flex; align-items: center; color: var(--text-primary); height: 75px; flex-shrink: 0; z-index: 10; }

    .messages-box { flex: 1; padding: 25px 30px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
    .message-bubble { max-width: 65%; padding: 10px 16px; border-radius: 14px; font-size: 0.92rem; line-height: 1.5; color: var(--text-primary); position: relative; word-wrap: break-word; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    
    .sent { align-self: flex-end; background-color: var(--green-sent); border-top-right-radius: 0; }
    .received { align-self: flex-start; background-color: #202c33; border-top-left-radius: 0; border: 1px solid #2f3b43; }
    .msg-info { font-size: 0.65rem; color: rgba(255,255,255,0.5); text-align: right; margin-top: 4px; display: block; }

    .input-area { padding: 16px 24px; background-color: var(--dark-header); display: flex; align-items: center; flex-shrink: 0; z-index: 10; border-top: 1px solid #2f3b43; }
    .chat-input { flex: 1; padding: 12px 20px; border-radius: 12px; border: none; outline: none; background-color: var(--dark-input); color: white; font-size: 0.95rem; }
    .btn-send { background: none; border: none; margin-left: 16px; color: #8696a0; font-size: 1.3rem; cursor: pointer; transition: color 0.2s; }
    .btn-send:hover { color: white; }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #374045; border-radius: 3px; }
</style>

<div class="chat-wrapper text-start">
    <div class="chat-container">
        
        {{-- SIDEBAR --}}
        <div class="chat-sidebar">
            <div class="sidebar-search">
                <input type="text" id="contactSearch" class="search-input" placeholder="Search classrooms or profiles..." autocomplete="off">
            </div>
            
            <div class="contact-list" id="contactList">
                {{-- Global Broadcast Channel --}}
                <a href="{{ route('messages.index', ['type' => 'global', 'id' => 0]) }}" class="contact-item {{ ($type == 'global') ? 'active' : '' }}" onclick="loadChat(event, this.href)" data-name="Global Announcement">
                    <div class="avatar" style="background: #E53935;"><i class="fas fa-bullhorn"></i></div>
                    <div>
                        <div class="fw-bold">Global Announcement</div>
                        <small style="color: var(--text-secondary)"><i class="fas fa-globe me-1"></i> Broadcast to Everyone</small>
                    </div>
                </a>

                {{-- Groups Section Loop --}}
                @if($groups->count() > 0)
                    <div class="section-title">Classroom Groups</div>
                    @foreach($groups as $group)
                        <a href="{{ route('messages.index', ['type' => 'group', 'id' => $group->id]) }}" class="contact-item {{ ($type == 'group' && $id == $group->id) ? 'active' : '' }}" data-name="{{ $group->group_name }}" onclick="loadChat(event, this.href)">
                            <div class="avatar" style="background: #008f78;"><i class="fas fa-users"></i></div>
                            <div>
                                <div class="fw-bold">{{ $group->group_name }}</div>
                                {{-- 🟢 FIXED: Appends form/year markers dynamically to group loops --}}
                                <small style="color: #38a169; font-weight: 600;">
                                    <i class="fas fa-graduation-cap me-1"></i> Classroom @if(isset($group->year)) • {{ $group->year->year_name ?? 'Form '.$group->year_id }} @endif
                                </small>
                            </div>
                        </a>
                    @endforeach
                @endif

                {{-- Personal Contacts Section Loop --}}
                @if($contacts->count() > 0)
                    <div class="section-title">Institutional Contacts</div>
                    @foreach($contacts as $contact)
                        <a href="{{ route('messages.index', ['type' => 'private', 'id' => $contact->id]) }}" class="contact-item {{ ($type == 'private' && $id == $contact->id) ? 'active' : '' }}" data-name="{{ $contact->name }}" onclick="loadChat(event, this.href)">
                            <div class="avatar" style="background: #4a5568;">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
                            <div>
                                <div class="fw-bold">{{ $contact->name }}</div>
                                {{-- 🟢 FIXED: If classmate contact is student role, attach their linked class name metadata details safely --}}
                                <small style="color: var(--text-secondary)">
                                    <i class="far fa-user me-1"></i> {{ ucfirst($contact->role) }} @if($contact->role == 'student' && isset($contact->group)) • {{ $contact->group->group_name }} @endif
                                </small>
                            </div>
                        </a>
                    @endforeach
                @endif
                
                <div id="noResults" class="p-3 text-center text-muted small" style="display:none;">No parameters match your query.</div>
            </div>
        </div>

        {{-- CHAT AREA --}}
        <div class="chat-area" id="chatArea">
            @if($activeChat)
                <div class="chat-header">
                    <div class="avatar me-3" style="background: {{ $type == 'global' ? '#E53935' : ($type == 'group' ? '#008f78' : '#4a5568') }}">
                        @if($type == 'group') <i class="fas fa-users"></i> @elseif($type == 'global') <i class="fas fa-bullhorn"></i> @else {{ strtoupper(substr($activeChat->name, 0, 1)) }} @endif
                    </div>
                    <div>
                        <div class="fw-bold">
                            @if($type == 'group') 
                                {{ $activeChat->group_name }}
                            @elseif($type == 'global') 
                                Global Announcement 
                            @else 
                                {{ $activeChat->name }} 
                            @endif
                        </div>
                        <small style="color: var(--text-secondary)">
                            @if($type == 'private') 
                                <i class="far fa-envelope me-1"></i> {{ $activeChat->email }} 
                            @elseif($type == 'group' && isset($activeChat->year))
                                {{-- 🟢 FIXED: Header subtext reflects the target Year metrics as well --}}
                                <i class="fas fa-graduation-cap me-1"></i> Assigned Academic Level: {{ $activeChat->year->year_name ?? 'Form '.$activeChat->year_id }}
                            @else 
                                <i class="fas fa-comments me-1"></i> Open Communication Room 
                            @endif
                        </small>
                    </div>
                </div>

                <div class="messages-box" id="messageContainer">
                    @forelse($messages as $msg)
                        <div class="message-bubble {{ $msg->sender_id == Auth::id() ? 'sent' : 'received' }}">
                            @if(($type == 'group' || $type == 'global') && $msg->sender_id != Auth::id())
                                <span style="font-size:0.75rem; color:#f5c518; font-weight:bold; display:block; margin-bottom:2px;">{{ $msg->sender->name }}</span>
                            @endif
                            {{ $msg->message }}
                            <span class="msg-info">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <div class="text-center mt-5"><span class="badge bg-dark p-2 text-muted border border-secondary border-opacity-25" style="font-size: 0.8rem; font-weight: 500;">No communication log footprints recorded here.</span></div>
                    @endforelse
                </div>

                <div class="input-area">
                    <form id="sendMessageForm" action="{{ route('messages.store') }}" method="POST" class="w-100 d-flex" onsubmit="submitMessage(event)">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="target_id" value="{{ $id }}">
                        <input type="text" name="message" id="msgInput" class="chat-input" placeholder="Type data message parameter payload..." required autocomplete="off">
                        <button type="submit" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            @else
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5" style="background: rgba(0,0,0,0.03);">
                    <div class="p-4 bg-white bg-opacity-5 rounded-circle mb-3 border border-secondary border-opacity-10">
                        <i class="fas fa-comments fa-3x" style="color: var(--text-secondary);"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary)">Select a Chat Stream</h5>
                    <p class="text-muted small mb-0" style="max-width: 280px;">Pick an option from the sidebar directory timeline matrix to review records.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function scrollToBottom() {
        var container = document.getElementById("messageContainer");
        if(container) container.scrollTop = container.scrollHeight;
    }
    scrollToBottom();

    document.getElementById('contactSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.contact-item');
        let hasVisible = false;

        items.forEach(item => {
            let name = item.getAttribute('data-name');
            if(name) {
                name = name.toLowerCase();
                if(name.includes(filter)) {
                    item.style.display = 'flex';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            }
        });
        document.getElementById('noResults').style.display = hasVisible ? 'none' : 'block';
    });

    function loadChat(e, url) {
        e.preventDefault();
        document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active'));
        e.currentTarget.classList.add('active');

        fetch(url).then(response => response.text()).then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            document.getElementById('chatArea').innerHTML = doc.getElementById('chatArea').innerHTML;
            scrollToBottom();
            window.history.pushState({}, '', url);
        });
    }

    function submitMessage(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const input = document.getElementById('msgInput');
        const msgText = input.value;
        const container = document.getElementById('messageContainer');

        if(msgText.trim() === "") return;

        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        container.insertAdjacentHTML('beforeend', `
            <div class="message-bubble sent" style="opacity:0.7">
                ${msgText} <span class="msg-info">${time}</span>
            </div>
        `);
        scrollToBottom();
        input.value = '';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(res => {
            if(res.ok) { container.lastElementChild.style.opacity = '1'; }
        });
    }
</script>
@endsection