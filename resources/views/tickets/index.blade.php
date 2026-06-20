
@extends('layouts.app')

@section('title', 'Semua Tiket - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Semua Tiket')

@section('content')

<style>

/* ===================================================
   PAGE HEADER
=================================================== */

.ticket-page-header{
    background:
        linear-gradient(
            135deg,
            rgba(37,99,235,.08),
            rgba(96,165,250,.04)
        );
    border:1px solid rgba(37,99,235,.10);
    border-radius:20px;
    padding:24px;
    margin-bottom:24px;
}

.ticket-page-header h5{
    font-weight:700;
    color:#1e293b;
}

.ticket-page-header p{
    color:#64748b;
}

/* ===================================================
   FILTER CARD
=================================================== */

.ticket-filter-card{
    position:relative;
    border:none !important;
    border-radius:22px !important;
    overflow:hidden;
    background:#ffffff !important;
    box-shadow:
        0 10px 30px rgba(15,23,42,.06);
}

.ticket-filter-card::before{
    content:'';
    position:absolute;
    inset:0;
    padding:1px;
    border-radius:22px;

    background:linear-gradient(
        135deg,
        #2563eb,
        #60a5fa,
        #93c5fd
    );

    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);

    -webkit-mask-composite:xor;
            mask-composite:exclude;

    pointer-events:none;
}

/* ===================================================
   TABLE CARD
=================================================== */

.ticket-table-card{
    position:relative;
    border:none !important;
    border-radius:22px !important;
    overflow:hidden;
    background:#ffffff !important;

    box-shadow:
        0 15px 40px rgba(15,23,42,.08);
}

.ticket-table-card::before{
    content:'';
    position:absolute;
    inset:0;
    padding:1px;
    border-radius:22px;

    background:linear-gradient(
        135deg,
        #1e40af,
        #2563eb,
        #60a5fa
    );

    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);

    -webkit-mask-composite:xor;
            mask-composite:exclude;

    pointer-events:none;
}

/* ===================================================
   CARD TITLE
=================================================== */

.ticket-card-title{
    font-size:1.1rem;
    font-weight:700;
    color:#1e293b;
}

.ticket-card-subtitle{
    color:#64748b;
    font-size:.9rem;
}

/* ===================================================
   FILTER INPUT
=================================================== */

.form-floating>.form-control,
.form-floating>.form-select{
    border-radius:14px !important;
    border:1px solid #dbeafe !important;
    background:#f8fbff;
}

.form-floating>.form-control:focus,
.form-floating>.form-select:focus{
    border-color:#2563eb !important;

    box-shadow:
        0 0 0 .20rem rgba(37,99,235,.15) !important;
}

/* ===================================================
   TABLE
=================================================== */

#ticketsTable{
    margin-bottom:0;
}

#ticketsTable thead th{
    background:#f8fbff !important;
    color:#1e293b !important;
    font-weight:700;
    border-bottom:1px solid #dbeafe;
    padding:16px;
}

#ticketsTable tbody td{
    padding:16px;
    vertical-align:middle;
    border-color:#eef4ff;
}

#ticketsTable tbody tr{
    transition:.25s ease;
}

#ticketsTable tbody tr:hover{
    background:#f8fbff;
    transform:scale(1.002);
}

/* ===================================================
   BADGE
=================================================== */

.badge{
    font-weight:600;
    letter-spacing:.2px;
}

/* ===================================================
   BUTTON
=================================================== */

.btn-primary{
    border:none !important;

    background:linear-gradient(
        135deg,
        #1e40af,
        #2563eb
    ) !important;

    box-shadow:
        0 6px 20px rgba(37,99,235,.25);
}

.btn-primary:hover{
    transform:translateY(-2px);
}

.btn-light{
    border:1px solid #dbeafe !important;
    background:#ffffff !important;
}

.btn-light:hover{
    background:#f8fbff !important;
}

/* ===================================================
   DROPDOWN
=================================================== */

.dropdown-menu{
    border:none !important;
    border-radius:16px !important;

    box-shadow:
        0 15px 35px rgba(15,23,42,.10);
}

.dropdown-item{
    padding:.75rem 1rem;
}

.dropdown-item:hover{
    background:#eff6ff;
}

/* ===================================================
   CHECKBOX
=================================================== */

.form-check-input:checked{
    background-color:#2563eb;
    border-color:#2563eb;
}

/* ===================================================
   PAGINATION AREA
=================================================== */

.ticket-footer{
    border-top:1px solid #e2e8f0;
    margin-top:20px;
    padding-top:20px;
}

/* ===================================================
   EMPTY STATE
=================================================== */

.ticket-empty{
    padding:60px 0;
}

.ticket-empty iconify-icon{
    color:#60a5fa;
}


/* ==========================================
   MODAL TUGASKAN TIKET PREMIUM
========================================== */

.assign-modal{
    border:none !important;
    border-radius:24px !important;
    overflow:hidden;

    box-shadow:
        0 25px 60px rgba(15,23,42,.25);
}

.assign-modal .modal-header{
    background:linear-gradient(
        135deg,
        #172554,
        #1e3a8a,
        #2563eb
    );

    padding:24px 28px;
}

.assign-modal .modal-body{
    padding:28px;
    background:#ffffff;
}

.assign-modal .modal-footer{
    background:#f8fafc;
    padding:20px 28px;
}

.assign-icon{
    width:58px;
    height:58px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:18px;

    background:rgba(255,255,255,.15);

    backdrop-filter:blur(10px);
}

.assign-icon iconify-icon{
    font-size:28px;
    color:#ffffff;
}

.assign-modal .modal-title{
    color:#ffffff;
}

.assign-input{
    height:52px;
    border-radius:14px !important;

    border:1px solid #dbeafe !important;
    background:#f8fbff !important;
}

.assign-input:focus{
    border-color:#2563eb !important;

    box-shadow:
        0 0 0 .20rem rgba(37,99,235,.15) !important;
}

.btn-cancel{
    border-radius:12px;
    padding:10px 20px;

    border:1px solid #dbeafe !important;
    background:#ffffff !important;
}

.btn-assign{
    border:none !important;

    border-radius:12px;

    padding:10px 24px;

    color:#fff !important;

    background:linear-gradient(
        135deg,
        #172554,
        #2563eb
    ) !important;

    box-shadow:
        0 10px 25px rgba(37,99,235,.25);
}

.btn-assign:hover{
    transform:translateY(-2px);
}
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="card ticket-table-card">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="mb-0 fw-semibold">Daftar Semua Tiket</h5>
            <p class="text-muted mb-0 small">Kelola semua tiket dalam sistem</p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-2">
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
          <div class="col-md-2">
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
          <div class="col-md-2">
            <div class="form-floating">
              <select class="form-select" id="filterCategory">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
              <label for="filterCategory">Kategori</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating">
              <select class="form-select" id="filterAssigned">
                <option value="">Semua Teknisi</option>
                <option value="unassigned">Belum Ditugaskan</option>
                @foreach($technicians as $technician)
                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                @endforeach
              </select>
              <label for="filterAssigned">Teknisi</label>
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
                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                <th>No. Tiket</th>
                <th>Subjek</th>
                <th>Pemohon</th>
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
              <tr class="ticket-row" data-status="{{ $ticket->status }}" data-priority="{{ $ticket->priority }}" data-category="{{ $ticket->category_id }}" data-assigned="{{ $ticket->assigned_to ?? 'unassigned' }}" data-subject="{{ $ticket->subject }}">
                <td><input type="checkbox" class="form-check-input ticket-checkbox" value="{{ $ticket->id }}"></td>
                <td>
                  <a href="{{ route('tickets.show', $ticket->id) }}" class="text-primary fw-semibold">{{ $ticket->ticket_number }}</a>
                </td>
                <td>
                  <div>
                    <p class="mb-0 fw-medium">{{ Str::limit($ticket->subject, 35) }}</p>
                    <small class="text-muted">{{ $ticket->created_at->format('d M Y, H:i') }}</small>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="30" height="30">
                    <div>
                      <p class="mb-0 fw-medium small">{{ $ticket->user->name }}</p>
                      <small class="text-muted">{{ $ticket->user->department }}</small>
                    </div>
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
                      <span class="small">{{ $ticket->assigned_to }}</span>
                    </div>
                  @else
                    <span class="text-muted small">Belum ditugaskan</span>
                  @endif
                </td>
                <td>
                  <div class="@if($ticket->sla_due_date && $ticket->sla_due_date < now() && in_array($ticket->status, ['open', 'in_progress'])) text-danger @elseif($ticket->sla_due_date && $ticket->sla_due_date->diffInHours(now()) < 12) text-warning @else text-success @endif small">
                    <iconify-icon icon="solar:clock-circle-linear" class="me-1"></iconify-icon>
                    {{ $ticket->sla_due_date ? $ticket->sla_due_date->format('d M H:i') : '-' }}
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
                      <li>
                        <a class="dropdown-item" href="#" onclick="quickAssign({{ $ticket->id }})">
                          <iconify-icon icon="solar:user-plus-linear" class="me-2"></iconify-icon>Tugaskan
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#" onclick="quickChangeStatus({{ $ticket->id }})">
                          <iconify-icon icon="solar:refresh-linear" class="me-2"></iconify-icon>Ubah Status
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="text-center py-5">
                  <div class="text-muted">
                    <iconify-icon icon="solar:ticket-linear" class="fs-1 d-block mb-3"></iconify-icon>
                    <p>Belum ada tiket dalam sistem</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            <button class="btn btn-sm btn-light me-2" onclick="bulkAssign()">Tugaskan Terpilih</button>
            <button class="btn btn-sm btn-light" onclick="bulkChangeStatus()">Ubah Status Terpilih</button>
          </div>
          <div>
            <p class="text-muted mb-0 small">Menampilkan {{ $tickets->count() }} tiket</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="quickAssignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content assign-modal">

            <div class="modal-header border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="assign-icon">
                        <iconify-icon icon="solar:user-plus-linear"></iconify-icon>
                    </div>

                    <div>
                        <h5 class="modal-title mb-1 fw-bold">
                            Tugaskan Tiket
                        </h5>
                        <small class="text-light opacity-75">
                            Pilih teknisi untuk menangani tiket yang dipilih
                        </small>
                    </div>
                </div>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <form method="POST"
                  action="{{ route('tickets.bulk.assign') }}"
                  id="assignForm">

                @csrf

                <div class="modal-body">

                    <input type="hidden"
                           name="ticket_ids"
                           id="assignTicketIds">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">
                            Teknisi
                        </label>

                        <input type="text"
                               name="assigned_to"
                               class="form-control assign-input"
                               placeholder="Masukkan nama teknisi"
                               required>
                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button type="button"
                            class="btn btn-cancel"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-assign">
                        <iconify-icon
                            icon="solar:user-plus-linear"
                            class="me-1">
                        </iconify-icon>
                        Tugaskan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="quickStatusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg"
     style="border-radius:20px; overflow:hidden;">

    <div class="modal-header border-0 text-white"
         style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">

        <h5 class="modal-title fw-bold text-white">
            <iconify-icon icon="solar:pen-linear" class="me-2"></iconify-icon>
            Ubah Status Tiket
        </h5>

        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
    </div>

    <form method="POST"
          action="{{ route('tickets.bulk.status') }}"
          id="statusForm">

        @csrf

        <div class="modal-body p-4">

            <input type="hidden"
                   name="ticket_ids"
                   id="statusTicketIds">

            <div class="text-center mb-4">

                <div style="
                    width:80px;
                    height:80px;
                    margin:auto;
                    border-radius:50%;
                    background:rgba(30,58,138,.1);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                ">
                    <iconify-icon
                        icon="solar:refresh-circle-linear"
                        style="font-size:42px;color:#1e3a8a;">
                    </iconify-icon>
                </div>

                <h5 class="fw-bold mt-3 mb-1">
                    Ubah Status Tiket
                </h5>

                <p class="text-muted mb-0">
                    Pilih status baru untuk tiket yang dipilih.
                </p>

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Status Baru
                </label>

                <select class="form-select form-select-lg"
                        name="status"
                        required
                        style="
                            border-radius:12px;
                            border:2px solid #dbeafe;
                        ">
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>

            </div>

        </div>

        <div class="modal-footer border-0 px-4 pb-4">

            <button type="button"
                    class="btn btn-light px-4"
                    data-bs-dismiss="modal"
                    style="border-radius:12px;">
                Batal
            </button>

            <button type="submit"
                    class="btn text-white px-4"
                    style="
                        background:linear-gradient(135deg,#1e3a8a,#2563eb);
                        border:none;
                        border-radius:12px;
                        box-shadow:0 10px 25px rgba(37,99,235,.25);
                    ">
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('filterStatus').addEventListener('change', filterTickets);
  document.getElementById('filterPriority').addEventListener('change', filterTickets);
  document.getElementById('filterCategory').addEventListener('change', filterTickets);
  document.getElementById('filterAssigned').addEventListener('change', filterTickets);
  document.getElementById('searchTicket').addEventListener('input', filterTickets);

  document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
      checkbox.checked = this.checked;
    });
  });

  function filterTickets() {
    const status = document.getElementById('filterStatus').value;
    const priority = document.getElementById('filterPriority').value;
    const category = document.getElementById('filterCategory').value;
    const assigned = document.getElementById('filterAssigned').value;
    const search = document.getElementById('searchTicket').value.toLowerCase();

    const rows = document.querySelectorAll('.ticket-row');
    rows.forEach(row => {
      const rowStatus = row.getAttribute('data-status');
      const rowPriority = row.getAttribute('data-priority');
      const rowCategory = row.getAttribute('data-category');
      const rowAssigned = row.getAttribute('data-assigned');
      const rowSubject = row.getAttribute('data-subject').toLowerCase();

      const statusMatch = !status || rowStatus === status;
      const priorityMatch = !priority || rowPriority === priority;
      const categoryMatch = !category || rowCategory === category;
      const assignedMatch = !assigned || rowAssigned === assigned;
      const searchMatch = !search || rowSubject.includes(search);

      row.style.display = (statusMatch && priorityMatch && categoryMatch && assignedMatch && searchMatch) ? '' : 'none';
    });
  }

  function quickAssign(ticketId) {
    document.getElementById('assignTicketIds').value = ticketId;
    new bootstrap.Modal(document.getElementById('quickAssignModal')).show();
  }

  function quickChangeStatus(ticketId) {
    document.getElementById('statusTicketIds').value = ticketId;
    new bootstrap.Modal(document.getElementById('quickStatusModal')).show();
  }

  function bulkAssign() {
    const selectedTickets = [];
    document.querySelectorAll('.ticket-checkbox:checked').forEach(checkbox => {
      selectedTickets.push(checkbox.value);
    });
    
    if (selectedTickets.length === 0) {
      alert('Pilih minimal satu tiket');
      return;
    }
    
    document.getElementById('assignTicketIds').value = selectedTickets.join(',');
    new bootstrap.Modal(document.getElementById('quickAssignModal')).show();
  }

  function bulkChangeStatus() {
    const selectedTickets = [];
    document.querySelectorAll('.ticket-checkbox:checked').forEach(checkbox => {
      selectedTickets.push(checkbox.value);
    });
    
    if (selectedTickets.length === 0) {
      alert('Pilih minimal satu tiket');
      return;
    }
    
    document.getElementById('statusTicketIds').value = selectedTickets.join(',');
    new bootstrap.Modal(document.getElementById('quickStatusModal')).show();
  }
</script>
@endpush
