@extends('layouts.app')

@section('title', 'Detail Tiket - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Detail Tiket')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <h5 class="mb-0 fw-semibold">{{ $ticket->ticket_number }}</h5>
                                <span
                                    class="badge @if ($ticket->status == 'open') bg-primary-subtle text-primary @elseif($ticket->status == 'in_progress') bg-warning-subtle text-warning @elseif($ticket->status == 'resolved') bg-success-subtle text-success @else bg-secondary-subtle text-secondary @endif rounded-pill px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span
                                    class="badge @if ($ticket->priority == 'high')-subtle text-danger @elseif($ticket->priority == 'medium') bg-warning-subtle text-warning @else bg-info-subtle text-info @endif rounded-pill px-3 py-2">
                                    <iconify-icon
                                        icon="@if ($ticket->priority == 'high') solar:fire-linear @elseif($ticket->priority == 'medium') solar:danger-circle-linear @else solar:shield-check-linear @endif"
                                        class="me-1"></iconify-icon>
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </div>
                            <p class="text-muted mb-0 small">Dibuat pada: {{ $ticket->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">{{ $ticket->subject }}</h6>
                        <p class="text-muted">{{ $ticket->description }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 bg-light">
                                <small class="text-muted d-block mb-1">Kategori</small>
                                <span class="fw-semibold">{{ $ticket->category->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                class="border rounded-3 p-3 bg-light @if ($ticket->sla_due_date && $ticket->sla_due_date < now() && in_array($ticket->status, ['open', 'in_progress'])) border-danger @elseif($ticket->sla_due_date && $ticket->sla_due_date->diffInHours(now()) < 12) border-warning @else border-success @endif">
                                <small class="text-muted d-block mb-1">SLA Due</small>
                                <span
                                    class="fw-semibold @if ($ticket->sla_due_date && $ticket->sla_due_date < now() && in_array($ticket->status, ['open', 'in_progress'])) text-danger @elseif($ticket->sla_due_date && $ticket->sla_due_date->diffInHours(now()) < 12) text-warning @else text-success @endif">
                                    {{ $ticket->sla_due_date ? $ticket->sla_due_date->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($ticket->attachments->count() > 0)
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Lampiran</h6>
                            <div class="row g-2">
                                @foreach ($ticket->attachments as $attachment)
                                    <div class="col-md-4">
                                        <a href="{{ route('tickets.attachments.download', [$ticket->id, $attachment->id]) }}"
                                            class="text-decoration-none text-dark">
                                            <div class="border rounded-3 p-3 h-100">
                                                <div class="d-flex align-items-start gap-2">
                                                    <iconify-icon
                                                        icon="@if ($attachment->extension === 'pdf') solar:file-text-linear @elseif(in_array($attachment->extension, ['jpg', 'jpeg', 'png'])) solar:gallery-linear @else solar:file-linear @endif"
                                                        class="fs-3 text-primary"></iconify-icon>
                                                    <div class="overflow-hidden">
                                                        <p class="mb-0 small fw-medium text-truncate">
                                                            {{ $attachment->original_name }}
                                                        </p>
                                                        <small class="text-muted">{{ $attachment->size_kb }} KB</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div id="chat">
                        <h6 class="fw-semibold mb-3">Diskusi Tiket</h6>

                        <div id="chatMessages" class="chat-messages mb-3" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($ticket->messages as $message)
                                @if ($message->user_id == auth()->id())
                                    <div class="d-flex mb-3 justify-content-end" style="width: 100%;" data-message-id="{{ $message->id }}">
                                    <div class="d-flex flex-row mb-3 justify-content-end gap-2">
                                        <div class="flex-grow-1 me-2" style="width: 100%;">
                                            <div class="bg-primary text-white rounded p-3">
                                                <p class="mb-0 small">{{ $message->content }}</p>
                                            </div>
                                            <small
                                                class="text-muted d-block text-end mt-1">{{ $message->created_at->format('H:i') }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('assets/images/profile/user1.jpg') }}"
                                                class="rounded-circle" width="35" height="35">
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="d-flex mb-3 justify-content-start" style="width: 100%;" data-message-id="{{ $message->id }}">
                                    <div class="d-flex flex-row-reverse mb-3 justify-content-start gap-2">
                                        <div class="flex-grow-1 me-2" style="width: 100%;">
                                            <div class="bg-warning text-white rounded p-3">
                                                <span class="d-block fw-semibold small mb-1">{{ $message->user?->name ?? 'User' }}</span>
                                                <p class="mb-0 small">{{ $message->content }}</p>
                                            </div>
                                            <small
                                                class="text-muted d-block text-end mt-1">{{ $message->created_at->format('H:i') }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('assets/images/profile/user1.jpg') }}"
                                                class="rounded-circle" width="35" height="35">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('tickets.messages.store', $ticket->id) }}" id="messageForm">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control" name="content" id="messageInput"
                                    placeholder="Ketik pesan Anda..." required>
                                <button type="submit" class="btn btn-primary">
                                    <iconify-icon icon="solar:send-linear" class="me-2"></iconify-icon>Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Informasi Pemohon</h6>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="50"
                            height="50">
                        <div>
                            <h6 class="mb-0 fw-medium">{{ $ticket->user->name }}</h6>
                            <p class="text-muted mb-0 small">{{ $ticket->user->department }}</p>
                        </div>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted">NIP:</span>
                            <span class="fw-medium">{{ $ticket->user->nip }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Email:</span>
                            <span class="fw-medium">{{ $ticket->user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($ticket->assigned_to)
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">Teknisi Penanggung Jawab</h6>
                        <div class="d-flex align-items-center gap-3">
                            
                            <div>
                                <h6 class="mb-0 fw-medium">{{ $ticket->assignedTechnician?->name ?? $ticket->assigned_to }}</h6>
                                <p class="text-muted mb-0 small">{{ $ticket->assignedTechnician?->department }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0 mt-4 border-warning">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <iconify-icon icon="solar:user-minus-linear" class="fs-3 text-warning mb-2"></iconify-icon>
                            <p class="text-muted mb-0">Belum ada teknisi yang ditugaskan</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($ticket->resolved_at)
                <div class="card shadow-sm border-0 mt-4 border-success bg-success-subtle">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:check-circle-linear" class="text-success fs-2"></iconify-icon>
                            <div>
                                <h6 class="mb-0 fw-semibold text-success">Tiket Selesai</h6>
                                <p class="text-muted mb-0 small">Diselesaikan pada
                                    {{ $ticket->resolved_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Timeline</h6>
                    <div class="timeline">
                        <div class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-success rounded-circle p-1">
                                    <iconify-icon icon="solar:check-circle-linear" class="text-white fs-5"></iconify-icon>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0 fw-medium">Tiket Dibuat</p>
                                <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @if ($ticket->assigned_to)
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle p-1">
                                        <iconify-icon icon="solar:user-check-linear"
                                            class="text-white fs-5"></iconify-icon>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Teknisi Ditugaskan</p>
                                    <small class="text-muted">{{ $ticket->updated_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                        @endif
                        @if ($ticket->status == 'in_progress')
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning rounded-circle p-1">
                                        <iconify-icon icon="solar:clock-circle-outline"
                                            class="text-white fs-5"></iconify-icon>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">Sedang Diproses</p>
                                    <small class="text-muted">Sedang dikerjakan oleh teknisi</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status Tiket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('tickets.update', $ticket->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="action" value="change_status">
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select class="form-select" name="status" required>
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved
                                </option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignTechnicianModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tugaskan Teknisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('tickets.update', $ticket->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="action" value="assign_technician">
                        <div class="mb-3">
                            <label class="form-label">Pilih Teknisi</label>
                            <select class="form-select" name="assigned_to" required>
                                <option value="">Pilih Teknisi</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}"
                                        {{ $ticket->assigned_to == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }} - {{ $technician->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function changeStatus() {
            new bootstrap.Modal(document.getElementById('changeStatusModal')).show();
        }

        function assignTechnician() {
            new bootstrap.Modal(document.getElementById('assignTechnicianModal')).show();
        }

        const chatMessages = document.getElementById('chatMessages');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const submitButton = messageForm.querySelector('button[type="submit"]');
        const authUserId = @json(auth()->id());
        const fetchMessagesUrl = @json(route('tickets.messages.index', $ticket->id));

        let lastMessageId = getLastMessageId();
        let isFetchingMessages = false;

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }

        function getLastMessageId() {
            const messageItems = chatMessages.querySelectorAll('[data-message-id]');
            let latestId = 0;

            messageItems.forEach((item) => {
                latestId = Math.max(latestId, Number(item.dataset.messageId) || 0);
            });

            return latestId;
        }

        function scrollChatToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function renderMessage(message) {
            const isOwnMessage = Number(message.user_id) === Number(authUserId);
            const outerClass = isOwnMessage ? 'justify-content-end' : 'justify-content-start';
            const rowClass = isOwnMessage ? 'flex-row justify-content-end' : 'flex-row-reverse justify-content-start';
            const bubbleClass = isOwnMessage ? 'bg-primary' : 'bg-warning';
            const senderName = escapeHtml(message.user_name || 'User');
            const content = escapeHtml(message.content);
            const time = escapeHtml(message.created_at_time || '');
            const senderNameHtml = isOwnMessage ? '' : `<span class="d-block fw-semibold small mb-1">${senderName}</span>`;

            return `
                <div class="d-flex mb-3 ${outerClass}" style="width: 100%;" data-message-id="${message.id}">
                    <div class="d-flex ${rowClass} mb-3 gap-2">
                        <div class="flex-grow-1 me-2" style="width: 100%;">
                            <div class="${bubbleClass} text-white rounded p-3">
                                ${senderNameHtml}
                                <p class="mb-0 small">${content}</p>
                            </div>
                            <small class="text-muted d-block text-end mt-1">${time}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="35" height="35">
                        </div>
                    </div>
                </div>
            `;
        }

        function appendMessage(message) {
            if (!message || !message.id || chatMessages.querySelector(`[data-message-id="${message.id}"]`)) {
                return;
            }

            chatMessages.insertAdjacentHTML('beforeend', renderMessage(message));
            lastMessageId = Math.max(lastMessageId, Number(message.id) || 0);
            scrollChatToBottom();
        }

        async function fetchNewMessages() {
            if (isFetchingMessages || document.hidden) {
                return;
            }

            isFetchingMessages = true;

            try {
                const response = await fetch(`${fetchMessagesUrl}?after_id=${lastMessageId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();

                if (Array.isArray(payload.data)) {
                    payload.data.forEach(appendMessage);
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            } finally {
                isFetchingMessages = false;
            }
        }

        scrollChatToBottom();

        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const content = messageInput.value.trim();

            if (!content) {
                return;
            }

            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    console.error('Error sending message:', data);
                    return;
                }

                appendMessage(data.data);
                form.reset();
            } catch (error) {
                console.error('Error sending message:', error);
            } finally {
                submitButton.disabled = false;
                messageInput.focus();
            }
        });

        fetchNewMessages();
        setInterval(fetchNewMessages, 500);
    </script>
@endpush
