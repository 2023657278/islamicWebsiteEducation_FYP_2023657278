@extends('users.students')

@section('content')
<style>
    .wa-chat-container { display: flex; flex-direction: column; height: 85vh; background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); border-radius: 16px; overflow: hidden; border: 1px solid #d1d7db; }
    .wa-chat-header { background-color: #f0f2f5; padding: 12px 20px; display: flex; align-items: center; border-bottom: 1px solid #d1d7db; }
    .wa-back-btn { color: #54656f; font-size: 1.2rem; margin-right: 15px; cursor: pointer; text-decoration: none; }
    .wa-chat-avatar { width: 45px; height: 45px; background: #E6FFFA; color: #008f78; border-radius: 12px; overflow: hidden; margin-right: 15px; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 1px solid rgba(0,0,0,0.05); flex-shrink: 0; }
    .wa-chat-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .wa-messages-area { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 8px; }
    .wa-bubble { max-width: 65%; padding: 8px 12px; border-radius: 10px; position: relative; font-size: 14.5px; line-height: 20px; color: #111b21; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
    .wa-received { align-self: flex-start; background-color: #ffffff; border-top-left-radius: 0; }
    .wa-sent { align-self: flex-end; background-color: #d9fdd3; border-top-right-radius: 0; }
    .wa-meta { float: right; margin-left: 10px; margin-top: 4px; font-size: 11px; color: #667781; }
    .wa-input-area { background-color: #f0f2f5; padding: 12px 16px; display: flex; align-items: center; gap: 12px; }
    .wa-input-field { flex: 1; background-color: #ffffff; border: none; border-radius: 10px; padding: 10px 15px; font-size: 15px; outline: none; }
    .wa-icon-btn { color: #54656f; font-size: 1.3rem; background: none; border: none; cursor: pointer; transition: 0.2s; }
</style>

<div class="container-fluid p-0 text-start">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="wa-chat-container shadow-sm">
                
                <div class="wa-chat-header">
                    <a href="{{ route('student.messages.index') }}" class="wa-back-btn"><i class="fas fa-arrow-left"></i></a>
                    
                    <div class="wa-chat-avatar">
                        @if($teacher->name == 'School Announcements')
                            <i class="fas fa-university" style="color: #c0392b;"></i>
                        @elseif($teacher->name == 'Class Announcements')
                            <i class="fas fa-bullhorn" style="color: #f39c12;"></i>
                        @elseif(isset($teacher->profile_image) && $teacher->profile_image)
                            {{-- 🟢 FIXED: Unified path selector for conversation frame titles --}}
                            <img src="{{ str_starts_with($teacher->profile_image, 'profile_images/') ? asset('storage/' . $teacher->profile_image) : asset('storage/profile_images/' . $teacher->profile_image) }}">
                        @else
                            {{ strtoupper(substr($teacher->name, 0, 2)) }}
                        @endif
                    </div>

                    <div style="flex: 1;">
                        <div class="fw-bold text-dark" style="font-size: 16px;">{{ $teacher->name }}</div>
                        <div class="small text-muted" style="font-size: 12px;">
                            @if($isBroadcast)
                                <i class="fas fa-lock me-1"></i> Read Only Announcement
                            @else
                                <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> Online
                            @endif
                        </div>
                    </div>
                </div>

                <div class="wa-messages-area" id="message-box">
                    @foreach($messages as $message)
                        <div class="wa-bubble {{ ($message->sender_id == Auth::id()) ? 'wa-sent' : 'wa-received' }}">
                            @if($isBroadcast && $message->sender_id != Auth::id())
                                <small class="d-block fw-bold text-primary mb-1">{{ $message->sender->name ?? 'Staff' }}</small>
                            @endif
                            {{ $message->message }}
                            <div class="wa-meta"><span>{{ $message->created_at->format('H:i') }}</span></div>
                        </div>
                    @endforeach
                </div>

                @if($isBroadcast)
                    <div class="p-3 bg-light text-center border-top">
                        <small class="text-muted fw-bold"><i class="fas fa-info-circle me-1"></i> This is a one-way communication channel.</small>
                    </div>
                @else
                    <div class="wa-input-area">
                        <form id="chatForm" action="{{ route('student.messages.store', $teacher->id) }}" method="POST" style="flex: 1; display: flex;">
                            @csrf
                            <input type="text" name="message" class="wa-input-field" placeholder="Type a message..." required autocomplete="off">
                            <button type="submit" class="wa-icon-btn ms-2"><i class="fas fa-paper-plane text-primary"></i></button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    const box = document.getElementById('message-box');
    box.scrollTop = box.scrollHeight;
</script>
@endsection