<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Cross-browser scrollbar styling */
        .scroll-box {
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #cbd5e1 transparent; /* Firefox */
        }
        .scroll-box::-webkit-scrollbar {
            width: 6px; /* WebKit browsers (Chrome, Edge, Brave, Safari) */
        }
        .scroll-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .scroll-box::-webkit-scrollbar-track {
            background: transparent;
        }

        /* General responsive and cross-browser fixes */
        * {
            box-sizing: border-box; /* Consistent sizing across browsers */
        }
        .message-bubble {
            max-width: 75%; /* Responsive on small screens */
            word-wrap: break-word; /* Handle long text */
        }
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 90%; /* More space on mobile */
            }
        }
    </style>
</head>

<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden relative">

    <!-- LEFT SIDEBAR (Users list) - Full width on mobile, fixed on larger screens -->
    <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r overflow-y-auto scroll-box md:relative fixed inset-0 z-10 md:z-auto">

        <div class="p-4 text-xl font-bold border-b bg-gray-50">
            Chats
        </div>

        @foreach ($users as $user)
            <div class="p-4 flex items-center gap-3 border-b hover:bg-gray-100 cursor-pointer"
                onclick="openChat({{ $user->id }}, '{{ $user->name }}')">

                <div class="w-12 h-12 bg-blue-500 text-white flex justify-center items-center rounded-full text-lg font-semibold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <a href="{{ route('profile.show', $user->id) }}" class="font-semibold text-blue-600 hover:underline">{{ $user->name }}</a>
                    @if($user->status_image)
                        <img src="{{ $user->statusImageUrl }}" alt="Status" class="w-8 h-8 rounded-full mt-1">
                    @endif
                </div>
            </div>
        @endforeach

    </div>

    <!-- CHAT SECTION - Hidden on mobile, overlay on larger screens -->
    <div class="hidden md:flex flex-col w-full md:w-2/3 lg:w-3/4 fixed md:relative inset-0 md:inset-auto z-20 md:z-auto bg-white md:bg-transparent" id="chatSection">

        <!-- HEADER - Add back button for mobile -->
        <div class="p-4 border-b bg-white flex items-center gap-3">
            <button class="md:hidden text-blue-600 font-semibold" onclick="closeChat()">← Back</button>
            <div class="w-10 h-10 rounded-full bg-gray-300" id="chatUserPic"></div>
            <div class="text-xl font-semibold" id="chatUserName"></div>
        </div>

        <!-- MESSAGES LIST -->
        <div class="flex-1 overflow-y-auto p-4 scroll-box bg-gray-50" id="messagesBox">
        </div>

        <!-- INPUT BOX - Touch-friendly on mobile -->
        <div class="p-4 bg-white border-t flex items-center gap-3">

            <input type="file" id="fileInput" class="hidden">

            <button onclick="document.getElementById('fileInput').click()"
                    class="px-4 py-3 md:px-3 md:py-2 bg-gray-200 rounded-full hover:bg-gray-300 text-lg md:text-base">
                📎
            </button>

            <input type="text" id="messageInput"
                   class="flex-1 border rounded-full p-3 md:p-2 px-4 text-lg md:text-base"
                   placeholder="Type a message">

            <button onclick="sendMessage()"
                    class="bg-blue-600 text-white px-6 py-3 md:px-4 md:py-2 rounded-full hover:bg-blue-700 text-lg md:text-base">
                Send
            </button>
        </div>

    </div>

</div>

<script>
let activeReceiverId = null;
let currentChannel = null;  // Track the current Echo channel
let appendedMessageIds = new Set();  // Track appended message IDs to prevent duplicates

function openChat(id, name) {
    activeReceiverId = id;
    console.log('Opening chat with user ID:', id);  // Debug log (remove after testing)

    // Show chat section (overlay on mobile, side-by-side on desktop)
    document.getElementById("chatSection").classList.remove('hidden');
    document.getElementById("chatSection").classList.add('flex');

    document.getElementById("chatUserName").innerText = name;
    fetchMessages(id);

    // Unsubscribe from previous channel if switching chats
    if (currentChannel) {
        currentChannel.stopListening('.MessageSent');
        window.Echo.leave(currentChannel.name);
        console.log('Unsubscribed from previous channel');  // Debug log
    }

    // Subscribe to the private channel for this chat pair (sorted by ID)
    let u1 = Math.min({{ auth()->id() }}, activeReceiverId);
    let u2 = Math.max({{ auth()->id() }}, activeReceiverId);
    let channelName = `chat.${u1}.${u2}`;
    console.log('Subscribing to channel:', channelName);  // Debug log
    currentChannel = window.Echo.private(channelName)
        .listen('.MessageSent', (e) => {
            console.log('Received MessageSent event:', e);  // Debug log
            // Only append if this message is for the active chat
            if (e.sender_id == activeReceiverId || e.receiver_id == activeReceiverId) {
                appendMessage(e);
            }
        });
}

function closeChat() {
    // Hide chat section and return to sidebar on mobile
    document.getElementById("chatSection").classList.add('hidden');
    document.getElementById("chatSection").classList.remove('flex');
    activeReceiverId = null;
    if (currentChannel) {
        currentChannel.stopListening('.MessageSent');
        window.Echo.leave(currentChannel.name);
    }
}

function fetchMessages(userId) {
    console.log('Fetching messages for user ID:', userId);  // Debug log
    appendedMessageIds.clear();  // Clear set when reloading messages to avoid stale duplicates
    fetch(`/messages/${userId}`)
        .then(res => res.json())
        .then(messages => {
            let box = document.getElementById("messagesBox");
            box.innerHTML = "";
            messages.forEach(msg => {
                appendMessage(msg);  // Reuse appendMessage for consistency
            });
        });
}

function appendMessage(msg) {
    // Prevent duplicates by checking message ID
    if (appendedMessageIds.has(msg.id)) {
        console.log('Duplicate message ignored:', msg.id);  // Debug log
        return;
    }
    appendedMessageIds.add(msg.id);
    console.log('Appending message:', msg);  // Debug log

    let box = document.getElementById("messagesBox");
    let isMine = msg.sender_id === {{ auth()->id() }};
    
    let bubble = `
        <div class="mb-3 flex ${isMine ? 'justify-end' : 'justify-start'}">
            <div class="message-bubble p-3 rounded-2xl ${isMine ? 'bg-blue-600 text-white' : 'bg-white border'}">
                ${msg.file_type === 'image' ? 
                    `<img src="/storage/${msg.file_path}" class="rounded mb-2 max-h-48 w-auto">` 
                    : msg.file_type === 'file' ? 
                    `<a href="/storage/${msg.file_path}" class="underline" target="_blank">Download File</a>` 
                    : ''
                }
                ${msg.message ?? ''}
            </div>
        </div>
    `;
    box.innerHTML += bubble;
    box.scrollTop = box.scrollHeight;  // Auto-scroll to bottom
}

function sendMessage() {
    if (!activeReceiverId) return;

    let message = document.getElementById("messageInput").value;
    let fileInput = document.getElementById("fileInput");

    let form = new FormData();
    form.append('receiver_id', activeReceiverId);
    form.append('message', message);

    if (fileInput.files[0]) {
        form.append('file', fileInput.files[0]);
    }

    console.log('Sending message to user ID:', activeReceiverId);  // Debug log
    fetch('/send-message', {
        method: 'POST',
        body: form,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(msg => {
        console.log('Message sent, response:', msg);  // Debug log
        document.getElementById("messageInput").value = "";
        fileInput.value = "";
        appendMessage(msg);  // Append locally for the sender (broadcast is toOthers)
    });
}
</script>

</body>
</html>