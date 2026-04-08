

<?php $__env->startSection('content'); ?>
<style>
    /* --- DARK MODE & LAYOUT --- */
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

    /* 1. CENTER WRAPPER */
    .chat-wrapper {
        display: flex;
        align-items: center;       /* Vertical Center */
        justify-content: center;   /* Horizontal Center */
        height: calc(100vh - 80px); /* Full height minus Navbar */
        padding-bottom: 20px;
    }

    /* 2. CHAT CARD (Floating & Centered) */
    .chat-container {
        display: flex;
        width: 95%;                /* Responsive Width */
        max-width: 1400px;         /* Max Width limit */
        height: 85vh;              /* Fixed Height so input is visible */
        background-color: var(--dark-chat);
        border-radius: 16px;       /* Nice rounded corners */
        overflow: hidden;
        border: 1px solid #333;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5); /* Deep Shadow for depth */
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
        border-radius: 20px; /* Pill shape */
        padding: 10px 15px;
        width: 100%;
        outline: none;
        font-size: 0.9rem;
        text-align: center; /* Centered placeholder */
    }
    .search-input:focus { text-align: left; background-color: #374045; }

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
        height: 70px; /* Taller header */
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
        
        /* Smooth Fade Mask */
        mask-image: linear-gradient(to bottom, transparent 0%, black 20px, black 100%);
        -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 20px, black 100%);
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

    .sent { 
        align-self: flex-end; 
        background-color: var(--green-sent); 
        border-top-right-radius: 0; 
    }
    .received { 
        align-self: flex-start; 
        background-color: var(--dark-sidebar); 
        border-top-left-radius: 0; 
    }

    .msg-info {
        font-size: 0.65rem;
        color: rgba(255,255,255,0.6);
        text-align: right;
        margin-top: 4px;
        display: block;
    }

    .input-area {
        padding: 15px 25px;
        background-color: var(--dark-header);
        display: flex;
        align-items: center;
        flex-shrink: 0;
        z-index: 10;
    }

    .chat-input {
        flex: 1;
        padding: 12px 20px;
        border-radius: 25px; /* Pill shape */
        border: none;
        outline: none;
        background-color: var(--dark-input);
        color: white;
        font-size: 1rem;
    }

    .btn-send {
        background: none; border: none; margin-left: 15px;
        color: var(--text-secondary); font-size: 1.4rem; cursor: pointer;
        transition: color 0.2s;
    }
    .btn-send:hover { color: var(--maroon-accent); }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #374045; border-radius: 3px; }
    ::-webkit-scrollbar-track { background: transparent; }
</style>

<div class="chat-wrapper">
    <div class="chat-container">
        
        
        <div class="chat-sidebar">
            <div class="sidebar-search">
                <input type="text" id="contactSearch" class="search-input" placeholder="Search contact..." autocomplete="off">
            </div>
            
            <div class="contact-list" id="contactList">
                
                <a href="<?php echo e(route('messages.index', ['type' => 'global', 'id' => 0])); ?>" class="contact-item <?php echo e(($type == 'global') ? 'active' : ''); ?>" onclick="loadChat(event, this.href)" data-name="Global Announcement">
                    <div class="avatar" style="background: #E53935;"><i class="fas fa-bullhorn"></i></div>
                    <div><div class="fw-bold">Global Announcement</div><small style="color: var(--text-secondary)">Message All</small></div>
                </a>

                
                <?php if($groups->count() > 0): ?>
                    <div class="section-title">Class Groups</div>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('messages.index', ['type' => 'group', 'id' => $group->id])); ?>" class="contact-item <?php echo e(($type == 'group' && $id == $group->id) ? 'active' : ''); ?>" data-name="<?php echo e($group->group_name); ?>" onclick="loadChat(event, this.href)">
                            <div class="avatar"><i class="fas fa-users"></i></div>
                            <div><div class="fw-bold"><?php echo e($group->group_name); ?></div><small style="color: var(--text-secondary)">Classroom</small></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                
                <?php if($contacts->count() > 0): ?>
                    <div class="section-title">Contacts</div>
                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('messages.index', ['type' => 'private', 'id' => $contact->id])); ?>" class="contact-item <?php echo e(($type == 'private' && $id == $contact->id) ? 'active' : ''); ?>" data-name="<?php echo e($contact->name); ?>" onclick="loadChat(event, this.href)">
                            <div class="avatar"><?php echo e(substr($contact->name, 0, 1)); ?></div>
                            <div>
                                <div class="fw-bold"><?php echo e($contact->name); ?></div>
                                <small style="color: var(--text-secondary)"><?php echo e(ucfirst($contact->role)); ?></small>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                
                <div id="noResults" class="p-3 text-center text-muted small" style="display:none;">No matches found.</div>
            </div>
        </div>

        
        <div class="chat-area" id="chatArea">
            <?php if($activeChat): ?>
                <div class="chat-header">
                    <div class="avatar me-3" style="background: <?php echo e($type == 'global' ? '#E53935' : '#6c757d'); ?>">
                        <?php if($type == 'group'): ?> <i class="fas fa-users"></i> <?php elseif($type == 'global'): ?> <i class="fas fa-bullhorn"></i> <?php else: ?> <?php echo e(substr($activeChat->name, 0, 1)); ?> <?php endif; ?>
                    </div>
                    <div>
                        <div class="fw-bold">
                            <?php if($type == 'group'): ?> <?php echo e($activeChat->group_name); ?> 
                            <?php elseif($type == 'global'): ?> Global Announcement 
                            <?php else: ?> <?php echo e($activeChat->name); ?> <?php endif; ?>
                        </div>
                        <small style="color: var(--text-secondary)">
                            <?php if($type == 'private'): ?> <?php echo e($activeChat->email); ?> <?php else: ?> Chat Room <?php endif; ?>
                        </small>
                    </div>
                </div>

                <div class="messages-box" id="messageContainer">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="message-bubble <?php echo e($msg->sender_id == Auth::id() ? 'sent' : 'received'); ?>">
                            <?php if(($type == 'group' || $type == 'global') && $msg->sender_id != Auth::id()): ?>
                                <span style="font-size:0.75rem; color:#f5c518; font-weight:bold; display:block; margin-bottom:2px;"><?php echo e($msg->sender->name); ?></span>
                            <?php endif; ?>
                            <?php echo e($msg->message); ?>

                            <span class="msg-info"><?php echo e($msg->created_at->format('H:i')); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center mt-5"><span class="badge bg-dark p-2" style="color: var(--text-secondary)">No messages here yet.</span></div>
                    <?php endif; ?>
                </div>

                <div class="input-area">
                    <form id="sendMessageForm" action="<?php echo e(route('messages.store')); ?>" method="POST" class="w-100 d-flex" onsubmit="submitMessage(event)">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="type" value="<?php echo e($type); ?>">
                        <input type="hidden" name="target_id" value="<?php echo e($id); ?>">
                        <input type="text" name="message" id="msgInput" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                        <button type="submit" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            <?php else: ?>
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5">
                    <i class="fas fa-comments fa-4x mb-3" style="color: #2a3942;"></i>
                    <h4 class="fw-bold" style="color: var(--text-secondary)">Select a Chat</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // 1. SCROLL TO BOTTOM
    function scrollToBottom() {
        var container = document.getElementById("messageContainer");
        if(container) container.scrollTop = container.scrollHeight;
    }
    scrollToBottom();

    // 2. SEARCH (Instant)
    document.getElementById('contactSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.contact-item');
        let hasVisible = false;

        items.forEach(item => {
            let name = item.getAttribute('data-name').toLowerCase();
            if(name.includes(filter)) {
                item.style.display = 'flex';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });
        document.getElementById('noResults').style.display = hasVisible ? 'none' : 'block';
    });

    // 3. LOAD CHAT (No Reload)
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

    // 4. SEND MESSAGE (AJAX)
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.adminhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\islamicWebsiteEducation_FYP_2023657278\resources\views/messages/index.blade.php ENDPATH**/ ?>