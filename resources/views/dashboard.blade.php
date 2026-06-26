@extends('layouts.app')

@section('title', 'Dashboard - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Dashboard')

@section('content')

    <style>
        /* ===========================
       DASHBOARD PREMIUM KOMINFO
    =========================== */

        .dashboard-card {
            border: none !important;
            border-radius: 20px !important;
            overflow: hidden;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
            color: #fff !important;
            box-shadow: 0 12px 30px rgba(37, 99, 235, .25);
            transition: all .3s ease;
            position: relative;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(37, 99, 235, .35);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, .08);
            border-radius: 50%;
        }

        .dashboard-card h6 {
            color: #ffffff !important;
            font-size: 16px;
            font-weight: 700;
        }

        .dashboard-card p {
            color: rgba(255, 255, 255, .75) !important;
            margin-bottom: 0;
        }

        .dashboard-card h2 {
            color: #ffffff !important;
            font-size: 38px;
            font-weight: 800;
            margin-top: 20px;
        }

        .dashboard-card .bg-primary-subtle,
        .dashboard-card .bg-warning-subtle,
        .dashboard-card .bg-success-subtle {
            background: rgba(255, 255, 255, .15) !important;
            backdrop-filter: blur(10px);
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-card .text-primary,
        .dashboard-card .text-warning,
        .dashboard-card .text-success {
            color: #ffffff !important;
        }

        /* Card Statistik */
        .dashboard-chart-card {
            border: none !important;
            border-radius: 20px !important;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .dashboard-chart-card .card-body {
            padding: 28px;
        }

        .dashboard-chart-card h5 {
            font-weight: 700;
            color: #1e293b;
        }

        /* Card Table */
        .dashboard-table-card {
            border: none !important;
            border-radius: 20px !important;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .dashboard-table-card .card-body {
            padding: 28px;
        }

        /* Hero Dashboard */
        .dashboard-hero {
            background: linear-gradient(135deg,
                    #1e3a8a,
                    #2563eb);
            border-radius: 24px;
            padding: 35px 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(37, 99, 235, .25);
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, .05);
            border-radius: 50%;
            right: -80px;
            top: -80px;
        }

        .dashboard-hero h1 {
            color: white;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .dashboard-hero p {
            color: rgba(255, 255, 255, .8);
            margin-bottom: 0;
        }

        /* Table */
        .table thead th {
            border: none;
            background: #f8fafc;
            color: #334155;
            font-weight: 700;
        }

        .table tbody tr {
            transition: .2s;
        }

        .table tbody tr:hover {
            background: #f8fbff;
        }

        /* Badge Status */
        .badge {
            border-radius: 999px;
            font-weight: 600;
            padding: 8px 14px;
        }

        /* Chart Container */
        #ticketChart,
        #categoryChart {
            min-height: 350px;
        }



        /* Garis Chart */
        .dashboard-chart-card,
        .dashboard-table-card {
            position: relative;

            border: 2px solid transparent;
            border-radius: 22px !important;

            background:
                linear-gradient(135deg,
                    #ffffff 0%,
                    #f8fafc 45%,
                    #eef4ff 100%) padding-box,

                linear-gradient(135deg,
                    #1e3a8a,
                    #2563eb,
                    #60a5fa) border-box;

            box-shadow:
                0 10px 30px rgba(15, 23, 42, .06),
                0 2px 8px rgba(15, 23, 42, .04);

            transition: all .3s ease;
        }

        .dashboard-chart-card,
        .dashboard-table-card {
            position: relative;
            border-radius: 22px !important;
            background: #fff;
            overflow: hidden;

            box-shadow:
                0 10px 30px rgba(15, 23, 42, .06),
                0 2px 8px rgba(15, 23, 42, .04);
        }

        .dashboard-chart-card::before,
        .dashboard-table-card::before {
            content: '';
            position: absolute;
            inset: 0;
            padding: 2px;
            border-radius: 22px;

            background: linear-gradient(135deg,
                    #1e3a8a,
                    #2563eb,
                    #60a5fa);

            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);

            -webkit-mask-composite: xor;
            mask-composite: exclude;

            pointer-events: none;
        }
    </style>


    <div class="dashboard-hero">
        <h1>Halo, {{ auth()->user()->name }}</h1>
        <p>
            Selamat datang di Sistem Ticketing Diskominfo.
            Pantau tiket, performa layanan, dan aktivitas sistem secara real-time.
        </p>
    </div>

    @can('view dashboard tickets menu')
        {{-- List Tickets Stats Menu --}}
        <div class="row">
            <div class="col-lg-4">
                <div class="card dashboard-card">
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
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-card">
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
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-card">
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
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card dashboard-chart-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-semibold">Statistik Tiket Bulanan</h5>
                        </div>
                        <div id="ticketChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-chart-card">
                    <div class="card-body p-4">
                        <h5 class="mb-4 fw-semibold">Distribusi Kategori</h5>
                        <div id="categoryChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card dashboard-chart-card">
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
                                    @foreach ($recentTickets as $ticket)
                                        <tr>
                                            <td><a href="#"
                                                    class="text-primary fw-semibold">{{ $ticket->ticket_number }}</a></td>
                                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td>{{ $ticket->user->name }}</td>
                                            <td>{{ $ticket->category->name }}</td>
                                            <td>
                                                <span
                                                    class="badge @if ($ticket->status == 'open') bg-primary-subtle text-primary @elseif($ticket->status == 'in_progress') bg-warning-subtle text-warning @elseif($ticket->status == 'resolved') bg-success-subtle text-success @else bg-secondary-subtle text-secondary @endif rounded-pill px-3 py-2">
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
                <div class="card dashboard-chart-card">
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
                                        <div class="progress-bar bg-danger" role="progressbar"
                                            style="width: {{ $highPriorityPercentage }}%"></div>
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
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: {{ $mediumPriorityPercentage }}%"></div>
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
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ $lowPriorityPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endcan

    @can('view dashboard model menu')
        @if (true)
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card dashboard-chart-card">
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
                    <div class="card dashboard-chart-card">
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
                                        <iconify-icon icon="solar:check-circle-linear"
                                            class="text-success fs-3 mb-2"></iconify-icon>
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
    @endcan

    @can('view dashboard my tickets menu')

        <div class="row">
            <div class="col-lg-4">
                <div class="card dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-bold fs-4">Total Tiket Saya</h6>
                                <p class="text-muted mb-0">Semua tiket saya dalam sistem</p>
                            </div>
                            <div class="bg-primary-subtle rounded-circle p-3">
                                <iconify-icon icon="mdi:ticket" class="text-primary fs-3"></iconify-icon>
                            </div>
                        </div>
                        <h2 class="fw-bold mt-3 mb-0">{{ number_format($totalTickets) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-bold fs-4">Tiket Terbuka Saya</h6>
                                <p class="text-muted mb-0">Tiket saya yang sedang diproses</p>
                            </div>
                            <div class="bg-warning-subtle rounded-circle p-3">
                                <iconify-icon icon="mdi:clock" class="text-warning fs-3"></iconify-icon>
                            </div>
                        </div>
                        <h2 class="fw-bold mt-3 mb-0">{{ number_format($openTickets) }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-bold fs-4">Tiket Selesai Saya</h6>
                                <p class="text-muted mb-0">Tiket saya yang sudah selesai</p>
                            </div>
                            <div class="bg-success-subtle rounded-circle p-3">
                                <iconify-icon icon="mdi:check-circle" class="text-success fs-3"></iconify-icon>
                            </div>
                        </div>
                        <h2 class="fw-bold mt-3 mb-0">{{ number_format($resolvedTickets) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="w-100">
                <div class="card dashboard-chart-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-semibold">Tiket Terbaru</h5>
                            <a href="{{ route('tickets.my') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Tiket</th>
                                        <th>Subjek</th>
                                        <th>Kategori</th>
                                        <th>Teknisi</th>
                                        <th>SLA</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!$myTickets->isEmpty())
                                        @foreach ($myTickets as $ticket)
                                            <tr>
                                                <td><a href="#"
                                                        class="text-primary fw-semibold">{{ $ticket->ticket_number }}</a></td>
                                                <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                                <td>{{ $ticket->category->name }}</td>
                                                <td>
                                                    @if ($ticket->assigned_to)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="{{ asset('assets/images/profile/user1.jpg') }}"
                                                                class="rounded-circle" width="30" height="30">
                                                            <span>{{ $ticket->assigned_to ? $ticket->assigned_to : 'Belum ada teknisi' }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Belum ditugaskan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div
                                                        class="@if ($ticket->sla_due_date && $ticket->sla_due_date < now() && in_array($ticket->status, ['open', 'in_progress'])) text-danger @elseif($ticket->sla_due_date && $ticket->sla_due_date->diffInHours(now()) < 12) text-warning @else text-success @endif">
                                                        <iconify-icon icon="solar:clock-circle-linear"
                                                            class="me-1"></iconify-icon>
                                                        {{ $ticket->sla_due_date ? $ticket->sla_due_date->diffForHumans() : '-' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($ticket->status == 'open') bg-primary-subtle text-primary @elseif($ticket->status == 'in_progress') bg-warning-subtle text-warning @elseif($ticket->status == 'resolved') bg-success-subtle text-success @else bg-secondary-subtle text-secondary @endif rounded-pill px-3 py-2">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

@endsection

@if (auth()->user()->role !== 'pegawai-dinas')
    @push('scripts')
        <script>
            var ticketChartOptions = {
                series: [{
                        name: 'Dibuat',
                        data: @json($monthlyCreated)
                    },
                    {
                        name: 'Selesai',
                        data: @json($monthlyResolved)
                    },
                    {
                        name: 'Terlambat',
                        data: @json($monthlyLate)
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 5
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($months)
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Tiket'
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#6366f1', '#22c55e', '#ef4444'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                }
            };
            var ticketChart = new ApexCharts(document.querySelector("#ticketChart"), ticketChartOptions);
            ticketChart.render();

            var categoryChartOptions = {
                series: @json($categoryData),
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: @json($categoryLabels),
                colors: ['#2f32fa', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6'],
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%'
                        }
                    }
                }
            };
            var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryChartOptions);
            categoryChart.render();

            function trainModel() {
                if (confirm('Apakah Anda yakin ingin melatih ulang model AI? Proses ini akan mengambil beberapa waktu.')) {
                    fetch('/train-model', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message);
                            location.reload();
                        })
                        .catch(error => alert('Terjadi kesalahan saat melatih model.'));
                }
            }
        </script>
    @endpush
@endif
