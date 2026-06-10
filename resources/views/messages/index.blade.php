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

    .chat-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        height: calc(100vh - 80px);
        padding-bottom: 20px;
    }

    .chat-container {
        display: flex;
        width: 95%;
        max-width: 1400px;
        height: 85vh;
        background-color: var(--dark-chat);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #333;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }

    /* --- SIDEBAR --- */
    .chat-sidebar {
        width: 320px;
        background-color: var(--dark-sidebar);
        border-right: 1px solid #333;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    
    .sidebar-search {
        padding: 15px;
        background-color: var(--dark-header);
        border-bottom: 1px solid #333;
    }

    .search-input {
        background-color: var(--dark-input);
        border: none;
        color: var(--text-primary);
        border-radius: 10px;
        padding: 10px 15px;
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }

    .contact-list {
        overflow-y: auto;
        flex: 1;
        padding-top: 10px;
    }

    .section-title {
        color: var(--maroon-accent);
        font-weight: bold;
        font-size: 0.7rem;
        padding: 15px 20px 5px 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        text-decoration: none;
        color: var(--text-primary);
        transition: background 0.2s;
    }
    .contact-item:hover { background-color: #2a3942; }
    .contact-item.active { background-color: #2a3942; border-left: 4px solid var(--maroon-accent); }
    
    .avatar {
        width: 45px; height: 45px;
        border-radius: 50%;
        background: #6c757d;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; color: white; margin-right: 15px;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    /* --- CHAT AREA --- */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--dark-chat);
        background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); 
        background-repeat: repeat;
        position: relative;
    }

    .chat-header {
        padding: 0 25px;
        background-color: var(--dark-header);
        border-bottom: 1px solid #333;
        display: flex;
        align-items: center;
        color: var(--text-primary);
        height: 70px;
        flex-shrink: 0;
        z-index: 10;
    }

    .messages-box {
        flex: 1;
        padding: 20px 30px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        scroll-behavior: smooth;
    }

    .message-bubble {
        max-width: 60%;
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.95rem;
        line-height: 1.5;
        color: var(--text-primary);
        position: relative;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }

    .sent { align-self: flex-end; background-color: var(--green-sent); border-top-right-radius: 0; }
    .received { align-self: flex-start; background-color: var(--dark-sidebar); border-top-left-radius: 0; }
    .msg-info { font-size: 0.65rem; color: rgba(255,255,255,0.6); text-align: right; margin-top: 4px; display: block; }

    .input-area { padding: 15px 25px; background-color: var(--dark-header); display: flex; align-items: center; flex-shrink: 0; z-index: 10; }
    .chat-input { flex: 1; padding: 12px 20px; border-radius: 25px; border: none; outline: none; background-color: var(--dark-input); color: white; font-size: 1rem; }
    .btn-send { background: none; border: none; margin-left: 15px; color: var(--text-secondary); font-size: 1.4rem; cursor: pointer; transition: color 0.2s; }
    .btn-send:hover { color: var(--maroon-accent); }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #374045; border-radius: 3px; }
    ::-webkit-scrollbar-track { background: transparent; }

    /* --- SEARCH RESULTS OVERLAY --- */
    .search-results-overlay {
        overflow-y: auto;
        height: 100%;
        padding: 24px;
        background-color: rgba(18, 18, 18, 0.95);
        backdrop-filter: blur(4px);
        color: var(--text-primary);
    }
    .search-item-card {
        background: #1f2c34 !important;
        border: 1px solid #2a3942 !important;
        transition: background 0.2s ease;
    }
    .search-item-card:hover {
        background: #2a3942 !important;
    }
</style>

<div class="chat-wrapper text-start">
    <div class="chat-container">
        
        {{-- SIDEBAR --}}
        <div class="chat-sidebar">
            <div class="sidebar-search">
                <form action="{{ route('messages.index') }}" method="GET">
                    <input type="text" name="search" id="contactSearch" class="search-input" placeholder="Search name, phone or email..." value="{{ $search }}" autocomplete="off">
                    <button type="submit" style="display: none;"></button>
                </form>
            </div>
            
            <div class="contact-list" id="contactList">
                {{-- Global Room --}}
                <a href="{{ route('messages.index', ['type' => 'global', 'id' => 0]) }}" class="contact-item {{ ($type == 'global') ? 'active' : '' }}" onclick="loadChat(event, this.href)">
                    <div class="avatar" style="background: #E53935;"><i class="fas fa-bullhorn"></i></div>
                    <div><div class="fw-bold">Global Announcement</div><small style="color: var(--text-secondary)">Message All</small></div>
                </a>

                {{-- Groups Section --}}
                @if($groups->count() > 0)
                    <div class="section-title">Class Groups</div>
                    @foreach($groups as $group)
                        <a href="{{ route('messages.index', ['type' => 'group', 'id' => $group->id]) }}" class="contact-item {{ ($type == 'group' && $id == $group->id) ? 'active' : '' }}" onclick="loadChat(event, this.href)">
                            <div class="avatar" style="background: #008f78;"><i class="fas fa-users"></i></div>
                            <div><div class="fw-bold">{{ $group->group_with_year }}</div><small style="color: var(--text-secondary)">Classroom</small></div>
                        </a>
                    @endforeach
                @endif

                {{-- Contacts Section --}}
                @if($contacts->count() > 0)
                    <div class="section-title">Contacts</div>
                    @foreach($contacts as $contact)
                        <a href="{{ route('messages.index', ['type' => 'private', 'id' => $contact->id]) }}" class="contact-item {{ ($type == 'private' && $id == $contact->id) ? 'active' : '' }}" onclick="loadChat(event, this.href)">
                            <div class="avatar">{{ substr($contact->name, 0, 1) }}</div>
                            <div>
                                <div class="fw-bold">{{ $contact->name }}</div>
                                <small style="color: var(--text-secondary)">{{ ucfirst($contact->role) }}</small>
                            </div>
                        </a>
                    @endforeach
                @endif
                
                <div id="noResults" class="p-3 text-center text-muted small" style="display:none;">No matches found.</div>
            </div>
        </div>

        {{-- CHAT INTERFACE AREA --}}
        <div class="chat-area" id="chatArea">
            @if($search && !$activeChat)
                <div class="search-results-overlay">
                    <h4 class="fw-bold mb-4" style="color: #38a169;">
                        <i class="fas fa-search me-2"></i> Unified Query Search Matches for: "{{ $search }}"
                    </h4>
                    
                    {{-- Verified Accounts Profiles Section --}}
                    <div class="mb-4">
                        <h6 class="section-title text-start ps-0 mb-3" style="color: #8696a0;">Profile Matches / Verified Email Addresses ({{ $contacts->count() }})</h6>
                        @forelse($contacts as $contact)
                            <a href="{{ route('messages.index', ['type' => 'private', 'id' => $contact->id]) }}" class="search-item-card d-flex align-items-center p-3 mb-2 rounded-3 text-decoration-none text-light">
                                <div class="avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">{{ strtoupper(substr($contact->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-bold text-white mb-0" style="font-size: 0.95rem;">{{ $contact->name }}</div>
                                    <small style="color: #38a169; font-weight: 600;">{{ $contact->email }} • {{ ucfirst($contact->role) }}</small>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted small ps-2">No user accounts found matching this string query.</p>
                        @endforelse
                    </div>

                    {{-- Text Content Snippets Section --}}
                    <div class="mb-4">
                        <h6 class="section-title text-start ps-0 mb-3" style="color: #8696a0;">Matching Historical Message Content ({{ $searchedMessages->count() }})</h6>
                        @forelse($searchedMessages as $msg)
                            <div class="p-3 mb-2 rounded-3 search-item-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-warning" style="font-size: 0.85rem;">{{ $msg->sender->name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $msg->created_at->format('d M Y H:i') }}</small>
                                </div>
                                <p class="mb-0 text-white-50" style="font-size: 0.9rem;">{{ $msg->message }}</p>
                            </div>
                        @empty
                            <p class="text-muted small ps-2">No conversations containing this message text phrase match.</p>
                        @endforelse
                    </div>
                </div>

            @elseif($activeChat)
                <div class="chat-header">
                    <div class="avatar me-3" style="background: {{ $type == 'global' ? '#E53935' : ($type == 'group' ? '#008f78' : '#6c757d') }}">
                        @if($type == 'group') <i class="fas fa-users"></i> @elseif($type == 'global') <i class="fas fa-bullhorn"></i> @else {{ substr($activeChat->name, 0, 1) }} @endif
                    </div>
                    <div>
                        <div class="fw-bold">
                            @if($type == 'group') 
                                {{ $activeChat->group_with_year }} 
                            @elseif($type == 'global') 
                                Global Announcement 
                            @else 
                                {{ $activeChat->name }} 
                            @endif
                        </div>
                        <small style="color: var(--text-secondary)">
                            @if($type == 'private') {{ $activeChat->email }} @else Chat Room @endif
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
                        <div class="text-center mt-5"><span class="badge bg-dark p-2" style="color: var(--text-secondary)">No messages here yet.</span></div>
                    @endforelse
                </div>

                <div class="input-area">
                    <form id="sendMessageForm" action="{{ route('messages.store') }}" method="POST" class="w-100 d-flex" onsubmit="submitMessage(event)">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="target_id" value="{{ $id }}">
                        <input type="text" name="message" id="msgInput" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                        <button type="submit" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            @else
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                    <i class="fas fa-comments fa-4x mb-3" style="color: #2a3942;"></i>
                    <h4 class="fw-bold" style="color: var(--text-secondary)">Select a Chat</h4>
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
    
    // Run initial on standard page ready load profiles
    document.addEventListener("DOMContentLoaded", scrollToBottom);

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
            if(res.ok) { 
                if (container.lastElementChild) {
                    container.lastElementChild.style.opacity = '1'; 
                }
            }
        });
    }
</script>
@endsection