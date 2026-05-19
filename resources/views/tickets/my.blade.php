@extends('layouts.app')

@section('title', 'Tiket Saya - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Tiket Saya')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="mb-0 fw-semibold">Daftar Tiket Saya</h5>
            <p class="text-muted mb-0 small">Kelola dan lacak semua tiket permohonan Anda</p>
          </div>
          <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <iconify-icon icon="solar:add-circle-linear" class="me-2"></iconify-icon>Buat Tiket Baru
          </a>
        </div>

        <div class="row mb-3">
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
              </select>
              <label for="filterStatus">Status</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select" id="filterPriority">
                <option value="">Semua Prioritas</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
              <label for="filterPriority">Prioritas</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <input type="text" class="form-control" id="searchTicket" placeholder="Cari tiket...">
              <label for="searchTicket">Cari Tiket</label>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle" id="ticketsTable">
            <thead class="table-light">
              <tr>
                <th>No. Tiket</th>
                <th>Subjek</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Teknisi</th>
                <th>SLA Due</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tickets as $ticket)
              <tr class="ticket-row" data-status="{{ $ticket->status }}" data-priority="{{ $ticket->priority }}" data-subject="{{ $ticket->subject }}">
                <td>
                  <a href="{{ route('tickets.show', $ticket->id) }}" class="text-primary fw-semibold">{{ $ticket->ticket_number }}</a>
                </td>
                <td>
                  <div>
                    <p class="mb-0 fw-medium">{{ Str::limit($ticket->subject, 40) }}</p>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                    {{ $ticket->category->name }}
                  </span>
                </td>
                <td>
                  <span class="badge @if($ticket->priority == 'high') bg-danger-subtle text-danger @elseif($ticket->priority == 'medium') bg-warning-subtle text-warning @else bg-info-subtle text-info @endif rounded-pill px-3 py-2">
                    <iconify-icon icon="@if($ticket->priority == 'high') solar:fire-linear @elseif($ticket->priority == 'medium') solar:danger-circle-linear @else solar:shield-check-linear @endif" class="me-1"></iconify-icon>
                    {{ ucfirst($ticket->priority) }}
                  </span>
                </td>
                <td>
                  <span class="badge @if($ticket->status == 'open') bg-primary-subtle text-primary @elseif($ticket->status == 'in_progress') bg-warning-subtle text-warning @elseif($ticket->status == 'resolved') bg-success-subtle text-success @else bg-secondary-subtle text-secondary @endif rounded-pill px-3 py-2">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                  </span>
                </td>
                <td>
                  @if($ticket->assigned_to)
                    <div class="d-flex align-items-center gap-2">
                      <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="30" height="30">
                      <span>{{ $ticket->assigned_to ? $ticket->assigned_to : 'Belum ada teknisi' }}</span>
                    </div>
                  @else
                    <span class="text-muted small">Belum ditugaskan</span>
                  @endif
                </td>
                <td>
                  <div class="@if($ticket->sla_due_date && $ticket->sla_due_date < now() && in_array($ticket->status, ['open', 'in_progress'])) text-danger @elseif($ticket->sla_due_date && $ticket->sla_due_date->diffInHours(now()) < 12) text-warning @else text-success @endif">
                    <iconify-icon icon="solar:clock-circle-linear" class="me-1"></iconify-icon>
                    {{ $ticket->sla_due_date ? $ticket->sla_due_date->diffForHumans() : '-' }}
                  </div>
                </td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <iconify-icon icon="solar:menu-dots-circle-linear"></iconify-icon>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="{{ route('tickets.show', $ticket->id) }}">
                          <iconify-icon icon="solar:eye-linear" class="me-2"></iconify-icon>Lihat Detail
                        </a>
                      </li>
                      @if(in_array($ticket->status, ['open', 'in_progress']))
                      <li>
                        <a class="dropdown-item" href="{{ route('tickets.show', $ticket->id) }}#chat">
                          <iconify-icon icon="solar:chat-linear" class="me-2"></iconify-icon>Kirim Pesan
                        </a>
                      </li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="text-muted">
                    <iconify-icon icon="solar:ticket-linear" class="fs-1 d-block mb-3"></iconify-icon>
                    <p>Belum ada tiket yang dibuat</p>
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm mt-2">Buat Tiket Sekarang</a>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <p class="text-muted mb-0 small">Menampilkan {{ $tickets->count() }} tiket</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('filterStatus').addEventListener('change', filterTickets);
  document.getElementById('filterPriority').addEventListener('change', filterTickets);
  document.getElementById('searchTicket').addEventListener('input', filterTickets);

  function filterTickets() {
    const status = document.getElementById('filterStatus').value;
    const priority = document.getElementById('filterPriority').value;
    const search = document.getElementById('searchTicket').value.toLowerCase();

    const rows = document.querySelectorAll('.ticket-row');
    rows.forEach(row => {
      const rowStatus = row.getAttribute('data-status');
      const rowPriority = row.getAttribute('data-priority');
      const rowSubject = row.getAttribute('data-subject').toLowerCase();

      const statusMatch = !status || rowStatus === status;
      const priorityMatch = !priority || rowPriority === priority;
      const searchMatch = !search || rowSubject.includes(search);

      row.style.display = (statusMatch && priorityMatch && searchMatch) ? '' : 'none';
    });
  }
</script>
@endpush