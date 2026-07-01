@extends('layouts.app')

@section('title', 'Log Chatbot - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Riwayat Chatbot AI')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="mb-0 fw-semibold">Riwayat Chatbot</h5>
            <p class="text-muted mb-0 small">Riwayat dan statistik chatbot</p>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-4">
            <div class="border rounded-3 p-3 bg-primary-subtle">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle p-2">
                  <iconify-icon icon="solar:database-linear" class="text-primary fs-4"></iconify-icon>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ $totalQueries }}</h6>
                  <p class="text-muted mb-0 small">Total Query</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border rounded-3 p-3 bg-success-subtle">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle p-2">
                  <iconify-icon icon="solar:check-circle-linear" class="text-success fs-4"></iconify-icon>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ $correctPredictions }}</h6>
                  <p class="text-muted mb-0 small">Jumlah Status Benar</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="border rounded-3 p-3 bg-danger-subtle">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-white rounded-circle p-2">
                  <iconify-icon icon="solar:close-circle-linear" class="text-danger fs-4"></iconify-icon>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold">{{ $wrongPredictions }}</h6>
                  <p class="text-muted mb-0 small">Jumlah Status Salah</p>
                </div>
              </div>
            </div>
          </div>
        
        </div>

        <div class="alert alert-info d-flex align-items-start gap-2">
          <iconify-icon icon="solar:info-circle-linear" class="fs-5"></iconify-icon>
          <div>
            <strong>Validasi Chatbot</strong>
            <ul class="mb-0 small">
              <li>Validasi chatbot untuk melihat statistik chatbot</li>
              <li>Data yang divalidasi akan digunakan untuk pelatihan ulang model</li>
              <li>Klik tombol validasi pada setiap prediksi untuk menandai sebagai benar atau salah</li>
            </ul>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>User</th>
                <th>Query</th>
                <th>Prediksi</th>
                <th>Confidence</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $log)
              <tr>
                <td>
                  <p class="mb-0 small">{{ $log->created_at->format('d M Y') }}</p>
                  <p class="mb-0 text-muted small">{{ $log->created_at->format('H:i') }}</p>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="30" height="30">
                    <span class="small">{{ $log->user->name }}</span>
                  </div>
                </td>
                <td>
                  <p class="mb-0 small">{{ Str::limit($log->user_query, 60) }}</p>
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                    {{ $log->predictedCategory->name ?? "Nilai Confidence Sangat Rendah" }}
                  </span>
                </td>
                <td>
                  <div class="progress" style="height: 20px; width: 100px;">
                    <div class="progress-bar @if($log->confidence_score >= 80) bg-success @elseif($log->confidence_score >= 60) bg-warning @elseif($log->confidence_score >= 20) bg-danger @else bg-dark @endif" role="progressbar" style="width: {{ $log->confidence_score }}%">
                      {{ $log->confidence_score }}%
                    </div>
                  </div>
                  @if($log->confidence_score < 20)
                    
                  @endif
                </td>
                <td>
                  @if($log->is_correct === true)
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                      <iconify-icon icon="solar:check-circle-linear" class="me-1"></iconify-icon>Benar
                    </span>
                  @elseif($log->is_correct === false)
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">
                      <iconify-icon icon="solar:close-circle-linear" class="me-1"></iconify-icon>Salah
                    </span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                      <iconify-icon icon="solar:question-circle-linear" class="me-1"></iconify-icon>Belum Validasi
                    </span>
                  @endif
                </td>
                <td>
                  @if($log->is_correct === null)
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-success" onclick="validateLog({{ $log->id }}, true)">
                      <iconify-icon icon="solar:check-circle-linear"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="validateLog({{ $log->id }}, false)">
                      <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                    </button>
                  </div>
                  @else
                  <span class="text-muted small">Sudah validasi</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <div class="text-muted">
                    <iconify-icon icon="solar:chat-dots-linear" class="fs-1 d-block mb-3"></iconify-icon>
                    <p>Belum ada log prediksi</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <p class="text-muted mb-0 small">Menampilkan {{ $logs->count() }} log</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  {{-- <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="mb-3 fw-semibold">Distribusiii Akurasi per Kategori</h5>
        <div id="accuracyChart"></div>
      </div>
    </div>
  </div> --}}

  <div class="col-lg-6">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h5 class="mb-3 fw-semibold">Confidence Score Distribution</h5>
        <div id="confidenceChart"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Validasi Log Chatbot -->
<div class="modal fade" id="validateLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden shadow-lg">

            <div class="modal-header border-0 text-white"
                style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">
                <h5 class="modal-title fw-semibold text-white">
                    <iconify-icon icon="solar:shield-check-linear" class="me-2"></iconify-icon>
                    Validasi Prediksi Chatbot
                </h5>

                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body text-center p-5">

                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                        style="
                            width:90px;
                            height:90px;
                            background:linear-gradient(135deg,#1e3a8a,#2563eb);
                            box-shadow:0 15px 30px rgba(37,99,235,.25);
                        ">
                        <iconify-icon
                            icon="solar:shield-check-bold"
                            class="text-white"
                            style="font-size:45px;">
                        </iconify-icon>
                    </div>
                </div>

                <h4 class="fw-bold text-dark mb-2">
                    Konfirmasi Validasi
                </h4>

                <p class="text-muted mb-0">
                    Apakah hasil prediksi chatbot ini
                    <span id="validationText" class="fw-semibold text-primary"></span>?
                </p>

            </div>

            <div class="modal-footer border-0 justify-content-center pb-4">

                <button type="button"
                    class="btn btn-light px-4"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button"
                    class="btn text-white px-4"
                    id="confirmValidationBtn"
                    style="
                        background:linear-gradient(135deg,#1e3a8a,#2563eb);
                        border:none;
                    ">
                    <iconify-icon icon="solar:check-circle-linear" class="me-1"></iconify-icon>
                    Ya, Validasi
                </button>

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
  let selectedLogId = null;
  let selectedValidationStatus = null;
  var accuracyChartOptions = {
    series: [85, 78, 92, 88, 75, 90],
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 5 } },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories: ['Infrastruktur IT', 'Perangkat Keras', 'Software', 'Jaringan', 'Akses', 'Lainnya'] },
    yaxis: { title: { text: 'Akurasi (%)' }, max: 100 },
    fill: { opacity: 1 },
    colors: ['#6366f1'],
    legend: { show: false }
  };
  var accuracyChart = new ApexCharts(document.querySelector("#accuracyChart"), accuracyChartOptions);
  accuracyChart.render();

  var confidenceChartOptions = {
    series: [45, 30, 15, 5, 5],
    chart: { type: 'donut', height: 300 },
    labels: ['80-100%', '60-80%', '20-60%', '0-20%', 'Gagal (<20%)'],
    colors: ['#22c55e', '#f59e0b', '#ef4444', '#6c757d', '#1f2937'],
    legend: { position: 'bottom' },
    plotOptions: { pie: { donut: { size: '70%' } } }
  };
  var confidenceChart = new ApexCharts(document.querySelector("#confidenceChart"), confidenceChartOptions);
  confidenceChart.render();

  function validateLog(id, isCorrect) {

    selectedLogId = id;
    selectedValidationStatus = isCorrect;

    document.getElementById('validationText').innerHTML =
        isCorrect
            ? '<span class="text-success">BENAR</span>'
            : '<span class="text-danger">SALAH</span>';

    document.getElementById('confirmValidationBtn').onclick =
        confirmValidation;

    new bootstrap.Modal(
        document.getElementById('validateLogModal')
    ).show();
}

function confirmValidation() {

    const btn = document.getElementById(
        'confirmValidationBtn'
    );

    btn.disabled = true;

    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2"></span>
        Memproses...
    `;

    fetch('{{ route('chatbot.validate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector(
                'meta[name="csrf-token"]'
            ).content
        },
        body: JSON.stringify({
            log_id: selectedLogId,
            is_correct: selectedValidationStatus
        })
    })
    .then(response => response.json())
    .then(data => {

        bootstrap.Modal.getInstance(
            document.getElementById('validateLogModal')
        ).hide();

        location.reload();
    })
    .catch(error => {

        alert('Terjadi kesalahan saat validasi.');

        btn.disabled = false;

        btn.innerHTML = `
            <iconify-icon
                icon="solar:check-circle-linear"
                class="me-1">
            </iconify-icon>
            Ya, Validasi
        `;
    });
}
</script>
@endpush
