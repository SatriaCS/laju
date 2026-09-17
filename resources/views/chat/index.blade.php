<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('messages.chat_ai') }}
                </h2>
            </div>

            <button
                id="clear-chat"
                type="button"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition"
            >
                {{ __('messages.clear_chat') }}
            </button>

        </div>

    </x-slot>


    <div class="py-6 sm:py-8">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow overflow-hidden">

                <div class="border-b px-5 py-4">

                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ __('messages.ai_assistant') }}
                    </h3>

                    <p class="text-sm text-gray-500">
                        {{ __('messages.ai_assistant_desc') }}
                    </p>

                </div>

                <div
                    id="chat-messages"
                    class="h-[500px] overflow-y-auto p-5 space-y-4"
                >

                    @if(count($history) === 0)

                        <div class="flex items-start gap-3">

                            <div
                                class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-semibold"
                            >
                                AI
                            </div>

                            <div class="bg-gray-100 rounded-lg px-4 py-3 max-w-[80%]">

                                <p class="text-sm text-gray-800">
                                    {{ __('messages.ai_greeting') }}
                                </p>

                            </div>

                        </div>

                    @else

                        @foreach($history as $message)

                            @if($message['role'] === 'user')

                                <div class="flex items-start justify-end gap-3">

                                    <div class="bg-blue-600 text-white rounded-lg px-4 py-3 max-w-[80%]">

                                        <p class="text-sm">
                                            {{ $message['text'] }}
                                        </p>

                                    </div>

                                    <div
                                        class="w-9 h-9 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center flex-shrink-0 text-sm font-semibold"
                                    >

                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                                    </div>

                                </div>

                            @else

                                <div class="flex items-start gap-3">

                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-semibold"
                                    >
                                        AI
                                    </div>

                                    <div class="bg-gray-100 rounded-lg px-4 py-3 max-w-[80%]">

                                        <p class="text-sm text-gray-800 whitespace-pre-line">
                                            {{ $message['text'] }}
                                        </p>

                                    </div>

                                </div>

                            @endif

                        @endforeach

                    @endif

                </div>

                <div class="border-t p-4">

                    <form
                        id="chat-form"
                        class="flex flex-col sm:flex-row gap-3"
                    >

                        <input
                            id="message-input"
                            type="text"
                            placeholder="{{ __('messages.chat_input_placeholder') }}"
                            autocomplete="off"
                            class="flex-1 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500"
                        >

                        <button
                            type="submit"
                            id="send-button"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                        >
                            {{ __('messages.send') }}
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <script>

        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const sendButton = document.getElementById('send-button');
        const clearChatButton = document.getElementById('clear-chat');

        chatForm.addEventListener('submit', function(event) {

            event.preventDefault();

            const message = messageInput.value.trim();

            if (message === '') {
                return;
            }

            addUserMessage(message);
            messageInput.value = '';
            sendButton.disabled = true;
            sendButton.innerText = 'Thinking...';

            fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: message
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Request gagal');
                }

                return data;
            })
            .then(data => {
                addAIMessage(data.message);
            })
            .catch(error => {
                console.error(error);
                addAIMessage('Maaf, terjadi kesalahan saat menghubungi AI.');
            })
            .finally(() => {
                sendButton.disabled = false;
                sendButton.innerText = 'Send';
                messageInput.focus();
            });

        });

        function addUserMessage(message) {

            const messageElement = document.createElement('div');
            messageElement.className = 'flex items-start justify-end gap-3';

            messageElement.innerHTML = `

                <div class="bg-blue-600 text-white rounded-lg px-4 py-3 max-w-[80%]">
                    <p class="text-sm">${escapeHtml(message)}</p>
                </div>

                <div class="w-9 h-9 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center flex-shrink-0 text-sm font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            `;

            chatMessages.appendChild(messageElement);
            scrollToBottom();

        }

        function addAIMessage(message) {

            const messageElement = document.createElement('div');
            messageElement.className = 'flex items-start gap-3';

            messageElement.innerHTML = `

                <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-semibold">
                    AI
                </div>

                <div class="bg-gray-100 rounded-lg px-4 py-3 max-w-[80%]">
                    <p class="text-sm text-gray-800 whitespace-pre-line">${escapeHtml(message)}</p>
                </div>

            `;

            chatMessages.appendChild(messageElement);
            scrollToBottom();

        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        clearChatButton.addEventListener('click', function() {

            if (!confirm('Hapus seluruh percakapan?')) {
                return;
            }

            fetch('{{ route('chat.clear') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    chatMessages.innerHTML = `

                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 text-sm font-semibold">
                                AI
                            </div>
                            <div class="bg-gray-100 rounded-lg px-4 py-3 max-w-[80%]">
                                <p class="text-sm text-gray-800">
                                    {{ __('messages.ai_greeting') }}
                                </p>
                            </div>
                        </div>

                    `;
                }
            });

        });

        scrollToBottom();

    </script>

</x-app-layout>
