<!-- filepath: d:\Fuzzy\project-fcm-laravel\resources\views\result-clustering.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hasil Clustering</title>
    <!-- Import Bootstrap & FontAwesome untuk styling dan ikon -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #d8eaa5;
            color: #000;
        }

        .form-card {
            background-color: #fffde7;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 1000px;
            margin: auto;
        }

        .form-card h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #5d4037;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .step {
            display: flex;
            align-items: center;
        }

        .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #904f2b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #904f2b;
            background-color: transparent;
        }

        .completed .circle {
            background-color: #904f2b;
            color: #d8eaa5;
        }

        .line {
            width: 40px;
            height: 2px;
            background-color: #904f2b;
            margin: 0 8px;
        }

        .btn-simpan {
            background-color: #8bc34a;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 500;
            border: none;
        }

        .btn-simpan:hover {
            background-color: #7cb342;
        }

        .accordion-button:not(.collapsed) {
            background-color: #d8eaa5;
            color: black;
        }

        .table th,
        .table td {
            color: #000;
        }

        .table-responsive {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 10px;
                max-width: 100%;
            }

            .stepper {
                flex-direction: column;
                gap: 8px;
            }

            .line {
                width: 2px;
                height: 30px;
                margin: 8px 0;
            }

            .btn-simpan {
                width: 100%;
                padding: 10px 0;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }

        @media (max-width: 576px) {
            .form-card h2 {
                font-size: 1.2rem;
            }

            .circle {
                width: 24px;
                height: 24px;
                font-size: 13px;
            }

            .table th,
            .table td {
                font-size: 12px;
                padding: 4px;
            }
        }
    </style>
</head>

<body class="container py-5">
    <!-- Stepper: visualisasi tahapan proses -->
    <div class="stepper">
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <div class="step">
            <div class="circle">5</div>
        </div>
    </div>
    <div class="form-card">
        <h2>Hasil Clustering Fuzzy C-Means</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <h4>Informasi Umum</h4>
        <ul>
            <li>Jumlah Iterasi: {{ session('iterations') }}</li>
            <li>Nilai Fungsi Objektif Akhir: {{ session('objective') }}</li>
        </ul>

        <h4>Derajat Keanggotaan Akhir:</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr class="table-warning">
                        <th>Data ke-</th>
                        <th>Kabupaten</th>
                        @foreach (session('centroids') as $index => $centroid)
                            <th>Derajat Keanggotaan {{ $index + 1 }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('final_u_matrix') as $i => $uRow)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ session('normalized_data')[$i]['kabupaten'] ?? '' }}</td>
                            @foreach ($uRow as $u)
                                <td>{{ number_format($u, 4) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h4 class="mt-4">Hasil Cluster</h4>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr class="table-warning">
                        <th rowspan="2">Data ke-</th>
                        <th rowspan="2">Kabupaten</th>
                        @for ($i = 0; $i < count(session('centroids')); $i++)
                            <th rowspan="2">P{{ $i + 1 }}/Pt</th>
                        @endfor
                        <th colspan="{{ count(session('centroids')) }}">Cluster</th>
                    </tr>
                    <tr class="table-warning">
                        @for ($i = 0; $i < count(session('centroids')); $i++)
                            <th>C{{ $i + 1 }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('final_u_matrix') as $i => $uRow)
                        @php
                            $kabupaten = session('normalized_data')[$i]['kabupaten'] ?? '';
                            $P = [];
                            $Pt = 0;
                            foreach ($uRow as $u) {
                                $P[] = pow($u, 2);
                                $Pt += pow($u, 2);
                            }
                            $r = [];
                            foreach ($P as $val) {
                                $r[] = $Pt > 0 ? $val / $Pt : 0;
                            }
                            $maxR = max($r);
                        @endphp

                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $kabupaten }}</td>
                            @foreach ($r as $val)
                                <td>{{ number_format($val, 4) }}</td>
                            @endforeach
                            @foreach ($r as $val)
                                <td class="{{ $val == $maxR ? 'table-success' : '' }}">
                                    @if ($val == $maxR)
                                        &#10003;
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (session('optimum_pci_list'))
            @php
                $pciList = session('optimum_pci_list');
                $labels = array_keys($pciList);
                $data = array_values($pciList);
                $clusterOptimum = session('cluster');
                $optimumIdx = array_search($clusterOptimum, $labels);
            @endphp

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="text-center mb-4 fw-bold">Diagram Batang Nilai PCI untuk Setiap Jumlah Cluster</h4>
                    <canvas id="pciBarChart" height="120"></canvas>
                    <div class="mt-4">
                        <div class="alert alert-info text-center fs-5">
                            <strong>Kesimpulan:</strong>
                            Nilai PCI tertinggi diperoleh pada <b>Cluster ke-{{ $clusterOptimum }}</b> dengan nilai PCI
                            <b>{{ number_format($pciList[$clusterOptimum], 4) }}</b>.
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const pciLabels = {!! json_encode($labels) !!};
                const pciData = {!! json_encode($data) !!};
                const optimumIdx = pciLabels.indexOf('{{ $clusterOptimum }}');

                new Chart(document.getElementById('pciBarChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: pciLabels,
                        datasets: [{
                            label: 'Nilai PCI',
                            data: pciData,
                            backgroundColor: pciLabels.map((_, i) => i === optimumIdx ? '#36a2eb' : '#ffcd56'),
                            borderColor: pciLabels.map((_, i) => i === optimumIdx ? '#1976d2' : '#ffb300'),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'PCI: ' + context.parsed.y.toFixed(4);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jumlah Cluster'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                max: 1,
                                title: {
                                    display: true,
                                    text: 'Nilai PCI'
                                }
                            }
                        }
                    }
                });
            </script>
        @endif

        <hr class="my-5">

        <h4 class="mt-4">Nilai Partition Coefficient Index (PCI)</h4>
        @if (session('pci'))
            <div class="alert alert-warning fs-5">
                <strong>PCI:</strong> {{ number_format(session('pci'), 4) }}
            </div>
        @endif

        <!-- Tombol menuju visualisasi min max -->
        <div class="text-end mt-4">
            <a href="{{ route('result.minmax') }}" class="btn btn-simpan">
                <i class="fas fa-chart-bar"></i> Lihat hasil visualisasi Min Max
            </a>
        </div>
    </div>
</body>

</html>