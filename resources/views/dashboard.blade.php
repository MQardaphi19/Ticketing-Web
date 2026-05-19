@extends('layouts.app')

@section('title', 'Dashboard - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Total Tiket</h6>
            <p class="text-muted mb-0">Semua tiket dalam sistem</p>
          </div>
          <div class="bg-primary-subtle rounded-circle p-3">
            <iconify-icon icon="mdi:ticket" class="text-primary fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($totalTickets) }}</h2>
        <div class="mt-2">
          <span class="badge bg-success-subtle text-success rounded-pill px-2">
            <iconify-icon icon="mdi:arrow-up"></iconify-icon> 12%
          </span>
          <span class="text-muted small ms-2">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Tiket Terbuka</h6>
            <p class="text-muted mb-0">Tiket yang sedang diproses</p>
          </div>
          <div class="bg-warning-subtle rounded-circle p-3">
            <iconify-icon icon="mdi:clock" class="text-warning fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($openTickets) }}</h2>
        <div class="mt-2">
          <span class="badge bg-danger-subtle text-danger rounded-pill px-2">
            <iconify-icon icon="mdi:arrow-down"></iconify-icon> 5%
          </span>
          <span class="text-muted small ms-2">dari minggu lalu</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Tiket Selesai</h6>
            <p class="text-muted mb-0">Tiket yang sudah selesai</p>
          </div>
          <div class="bg-success-subtle rounded-circle p-3">
            <iconify-icon icon="mdi:check-circle" class="text-success fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($resolvedTickets) }}</h2>
        <div class="mt-2">
          <span class="badge bg-success-subtle text-success rounded-pill px-2">
            <iconify-icon icon="mdi:arrow-up"></iconify-icon> 18%
          </span>
          <span class="text-muted small ms-2">dari bulan lalu</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Statistik Tiket Bulanan</h5>
          <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
              2025
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">2025</a></li>
              <li><a class="dropdown-item" href="#">2024</a></li>
            </ul>
          </div>
        </div>
        <div id="ticketChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="mb-4 fw-semibold">Distribusi Kategori</h5>
        <div id="categoryChart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Tiket Terbaru</h5>
          <a href="{{ route('tickets.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No. Tiket</th>
                <th>Subjek</th>
                <th>Pemohon</th>
                <th>Kategori</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentTickets as $ticket)
              <tr>
                <td><a href="#" class="text-primary fw-semibold">{{ $ticket->ticket_number }}</a></td>
                <td>{{ Str::limit($ticket->subject, 30) }}</td>
                <td>{{ $ticket->user->name }}</td>
                <td>{{ $ticket->category->name }}</td>
                <td>
                  <span class="badge @if($ticket->status == 'open') bg-primary-subtle text-primary @elseif($ticket->status == 'in_progress') bg-warning-subtle text-warning @elseif($ticket->status == 'resolved') bg-success-subtle text-success @else bg-secondary-subtle text-secondary @endif rounded-pill px-3 py-2">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Prioritas Tiket</h5>
        </div>
        <div class="d-flex flex-column gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-danger-subtle rounded-circle p-2">
              <iconify-icon icon="mdi:fire" class="text-danger fs-4"></iconify-icon>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">High</span>
                <span class="text-muted">{{ $highPriorityCount }}</span>
              </div>
              <div class="progress mt-1" style="height: 6px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $highPriorityPercentage }}%"></div>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning-subtle rounded-circle p-2">
              <iconify-icon icon="mdi:alert-circle" class="text-warning fs-4"></iconify-icon>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Medium</span>
                <span class="text-muted">{{ $mediumPriorityCount }}</span>
              </div>
              <div class="progress mt-1" style="height: 6px;">
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $mediumPriorityPercentage }}%"></div>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center gap-3">
            <div class="bg-info-subtle rounded-circle p-2">
              <iconify-icon icon="mdi:shield-check" class="text-info fs-4"></iconify-icon>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Low</span>
                <span class="text-muted">{{ $lowPriorityCount }}</span>
              </div>
              <div class="progress mt-1" style="height: 6px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $lowPriorityPercentage }}%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">SLA Compliance</h5>
        </div>
        <div class="text-center">
          <h2 class="fw-bold mb-0">{{ $slaCompliance }}%</h2>
          <p class="text-muted mb-2">Tiket diselesaikan tepat waktu</p>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar @if($slaCompliance >= 90) bg-success @elseif($slaCompliance >= 70) bg-warning @else bg-danger @endif" role="progressbar" style="width: {{ $slaCompliance }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if(true)
<div class="row mt-4">
  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Model AI Training</h5>
          <span class="badge bg-success-subtle text-success rounded-pill px-2">Terlatih</span>
        </div>
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="bg-primary-subtle rounded-circle p-3">
            <iconify-icon icon="mdi:cpu-64-bit" class="text-primary fs-2"></iconify-icon>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-0 fw-semibold">Naive Bayes Classifier</h6>
            <p class="text-muted mb-0 small">Akurasi: {{ $modelAccuracy }}%</p>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted small">Total Data Latih:</span>
          <span class="fw-semibold">{{ $trainingDataCount }}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted small">Terakhir Dilatih:</span>
          <span class="fw-semibold">{{ $lastTrainedAt }}</span>
        </div>
        <button onclick="trainModel()" class="btn btn-primary w-100 mt-3">
          <iconify-icon icon="mdi:refresh" class="me-2"></iconify-icon>Latih Ulang Model
        </button>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Performa Chatbot</h5>
        </div>
        <div class="row g-3">
          <div class="col-6">
            <div class="border rounded-3 p-3 text-center bg-primary-subtle">
              <iconify-icon icon="mdi:chat" class="text-primary fs-3 mb-2"></iconify-icon>
              <h5 class="mb-0 fw-bold">{{ $totalQueries }}</h5>
              <p class="text-muted mb-0 small">Total Query</p>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 text-center bg-success-subtle">
              <iconify-icon icon="solar:check-circle-linear" class="text-success fs-3 mb-2"></iconify-icon>
              <h5 class="mb-0 fw-bold">{{ $correctPredictions }}</h5>
              <p class="text-muted mb-0 small">Prediksi Benar</p>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 text-center bg-info-subtle">
              <iconify-icon icon="mdi:target" class="text-info fs-3 mb-2"></iconify-icon>
              <h5 class="mb-0 fw-bold">{{ $avgConfidence }}%</h5>
              <p class="text-muted mb-0 small">Rata-rata Conf.</p>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 text-center bg-warning-subtle">
              <iconify-icon icon="mdi:chart-bar" class="text-warning fs-3 mb-2"></iconify-icon>
              <h5 class="mb-0 fw-bold">{{ $chatbotAccuracy }}%</h5>
              <p class="text-muted mb-0 small">Akurasi</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
  var ticketChartOptions = {
    series: [
      { name: 'Dibuat', data: @json($monthlyCreated) },
      { name: 'Selesai', data: @json($monthlyResolved) },
      { name: 'Terlambat', data: @json($monthlyLate) }
    ],
    chart: { type: 'bar', height: 350, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 5 } },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories: @json($months) },
    yaxis: { title: { text: 'Jumlah Tiket' } },
    fill: { opacity: 1 },
    colors: ['#6366f1', '#22c55e', '#ef4444'],
    legend: { position: 'top', horizontalAlign: 'center' }
  };
  var ticketChart = new ApexCharts(document.querySelector("#ticketChart"), ticketChartOptions);
  ticketChart.render();

  var categoryChartOptions = {
    series: @json($categoryData),
    chart: { type: 'donut', height: 350 },
    labels: @json($categoryLabels),
    colors: ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
    legend: { position: 'bottom' },
    plotOptions: { pie: { donut: { size: '70%' } } }
  };
  var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryChartOptions);
  categoryChart.render();

  function trainModel() {
    if (confirm('Apakah Anda yakin ingin melatih ulang model AI? Proses ini akan mengambil beberapa waktu.')) {
      fetch('/admin/train-model', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(response => response.json())
        .then(data => { alert(data.message); location.reload(); })
        .catch(error => alert('Terjadi kesalahan saat melatih model.'));
    }
  }
</script>
@endpush