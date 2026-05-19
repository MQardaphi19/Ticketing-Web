@extends('layouts.app')

@section('title', 'Laporan & Analitik - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Laporan & Analitik')

@section('content')
<div class="row">
  <div class="col-lg-3">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Total Tiket</h6>
            <p class="text-muted mb-0">Semua tiket dalam sistem</p>
          </div>
          <div class="bg-primary-subtle rounded-circle p-3">
            <iconify-icon icon="solar:ticket-linear" class="text-primary fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($totalTickets) }}</h2>
        <p class="text-muted small mb-0">Periode ini</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Tiket Selesai</h6>
            <p class="text-muted mb-0">Tiket berhasil diselesaikan</p>
          </div>
          <div class="bg-success-subtle rounded-circle p-3">
            <iconify-icon icon="solar:check-circle-linear" class="text-success fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($resolvedTickets) }}</h2>
        <p class="text-muted small mb-0">{{ number_format(($resolvedTickets / $totalTickets) * 100, 1) }}% dari total</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">Tiket Terbuka</h6>
            <p class="text-muted mb-0">Tiket sedang diproses</p>
          </div>
          <div class="bg-warning-subtle rounded-circle p-3">
            <iconify-icon icon="solar:clock-circle-linear" class="text-warning fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ number_format($openTickets) }}</h2>
        <p class="text-muted small mb-0">{{ number_format(($openTickets / $totalTickets) * 100, 1) }}% dari total</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3">
    <div class="card overflow-hidden shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <h6 class="mb-0 fw-bold fs-4">SLA Compliance</h6>
            <p class="text-muted mb-0">Tepat waktu selesai</p>
          </div>
          <div class="bg-info-subtle rounded-circle p-3">
            <iconify-icon icon="solar:chart-linear" class="text-info fs-3"></iconify-icon>
          </div>
        </div>
        <h2 class="fw-bold mt-3 mb-0">{{ $slaCompliance }}%</h2>
        <div class="progress mt-2" style="height: 6px;">
          <div class="progress-bar bg-info" role="progressbar" style="width: {{ $slaCompliance }}%"></div>
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
            <button class="btn btn-light dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
              2025
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">2025</a></li>
              <li><a class="dropdown-item" href="#">2024</a></li>
            </ul>
          </div>
        </div>
        <div id="monthlyChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Distribusi Prioritas</h5>
        </div>
        <div id="priorityChart"></div>
      </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Rata-rata Resolusi</h5>
        </div>
        <div class="text-center">
          <h2 class="fw-bold mb-0">{{ $avgResolutionTime }} Jam</h2>
          <p class="text-muted mb-2">Waktu rata-rata penyelesaian tiket</p>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: 70%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Performa per Kategori</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Kategori</th>
                <th>Tiket</th>
                <th>Selesai</th>
                <th>SLA</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categoryStats as $stat)
              <tr>
                <td>{{ $stat->name }}</td>
                <td>{{ $stat->tickets }}</td>
                <td>{{ $stat->resolved }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress" style="width: 60px; height: 6px;">
                      <div class="progress-bar @if($stat->sla >= 90) bg-success @elseif($stat->sla >= 70) bg-warning @else bg-danger @endif" role="progressbar" style="width: {{ $stat->sla }}%"></div>
                    </div>
                    <span class="small">{{ $stat->sla }}%</span>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Performa Teknisi</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama</th>
                <th>Ditugaskan</th>
                <th>Selesai</th>
                <th>Rata-rata</th>
              </tr>
            </thead>
            <tbody>
              @foreach($technicianStats as $tech)
              <tr>
                <td>{{ $tech->name }}</td>
                <td>{{ $tech->assigned }}</td>
                <td>{{ $tech->resolved }}</td>
                <td>
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-2">
                    {{ $tech->avg_time }} Jam
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
</div>

<div class="row mt-4">
  <div class="col-lg-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0 fw-semibold">Ringkasan Pelanggaran SLA</h5>
          <button class="btn btn-primary btn-sm" onclick="exportReport()">
            <iconify-icon icon="solar:download-linear" class="me-2"></iconify-icon>Export Laporan
          </button>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Prioritas</th>
                <th>Total Tiket</th>
                <th>Tiket Selesai</th>
                <th>Tiket Terlambat</th>
                <th>Persentase Terlambat</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($priorityStats as $stat)
              <tr>
                <td>
                  <span class="badge @if($stat['priority'] == 'High') bg-danger-subtle text-danger @elseif($stat['priority'] == 'Medium') bg-warning-subtle text-warning @else bg-info-subtle text-info @endif rounded-pill px-3 py-2">
                    {{ $stat['priority'] }}
                  </span>
                </td>
                <td>{{ $stat['count'] }}</td>
                <td>{{ $stat['resolved'] }}</td>
                <td>{{ $stat['late'] }}</td>
                <td>{{ number_format(($stat['late'] / $stat['count']) * 100, 1) }}%</td>
                <td>
                  @if($stat['late'] == 0)
                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Sangat Baik</span>
                  @elseif(($stat['late'] / $stat['count']) * 100 < 10)
                    <span class="badge bg-success-subtle text-success rounded-pill px-2">Baik</span>
                  @elseif(($stat['late'] / $stat['count']) * 100 < 20)
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Perlu Perhatian</span>
                  @else
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Perlu Perbaikan</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  var monthlyChartOptions = {
    series: [
      { name: 'Dibuat', data: {{ $monthlyStats->pluck('created') }} },
      { name: 'Selesai', data: {{ $monthlyStats->pluck('resolved') }} },
      { name: 'Terlambat', data: {{ $monthlyStats->pluck('late') }} }
    ],
    chart: { type: 'bar', height: 350, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 5 } },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories: {{ $monthlyStats->pluck('month') }} },
    yaxis: { title: { text: 'Jumlah Tiket' } },
    fill: { opacity: 1 },
    colors: ['#6366f1', '#22c55e', '#ef4444'],
    legend: { position: 'top', horizontalAlign: 'center' }
  };
  var monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyChartOptions);
  monthlyChart.render();

  var priorityChartOptions = {
    series: [{{ $priorityStats[0]['count'] }}, {{ $priorityStats[1]['count'] }}, {{ $priorityStats[2]['count'] }}],
    chart: { type: 'donut', height: 300 },
    labels: ['High', 'Medium', 'Low'],
    colors: ['#ef4444', '#f59e0b', '#22c55e'],
    legend: { position: 'bottom' },
    plotOptions: { pie: { donut: { size: '70%' } } }
  };
  var priorityChart = new ApexCharts(document.querySelector("#priorityChart"), priorityChartOptions);
  priorityChart.render();

  function exportReport() {
    alert('Laporan akan diunduh dalam format Excel/PDF. Fitur ini akan segera tersedia.');
  }
</script>
@endpush