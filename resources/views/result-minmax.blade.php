<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visualisasi Min & Max Hewan Ternak per Cluster</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #d8eaa5; color: #000; }
        .form-card { background-color: #fffde7; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 30px; max-width: 900px; margin: auto; }
        .form-card h2 { font-size: 1.5rem; font-weight: 600; color: #5d4037; text-align: center; margin-bottom: 1rem; }
        #hover-info { min-height: 1.5em; margin-top: 10px; text-align: center; font-size: 1rem; color: #333; }
    </style>
</head>
<body class="container py-5">
    <div class="form-card">
        <h2>Visualisasi Min & Max Tiap Hewan Ternak per Cluster</h2>
        @for ($c = 0; $c < $clusterCount; $c++)
            <div class="mb-5">
                <h4 class="mb-3">Cluster {{ $c + 1 }}</h4>
                @if (empty($clusterAnggota[$c]))
                    <div class="alert alert-danger text-center">Tidak ada anggota pada Cluster {{ $c + 1 }}.</div>
                @else
                    <canvas id="minMaxHewanChart{{ $c }}" height="180"></canvas>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Min:</strong>
                            <ul>
                                @foreach ($minmax[$c] as $m)
                                    <li>
                                        {{ $m['hewan'] }}: <b>{{ $m['min_kab'] }}</b>
                                        ({{ number_format($m['min_asli']) }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Max:</strong>
                            <ul>
                                @foreach ($minmax[$c] as $m)
                                    <li>
                                        {{ $m['hewan'] }}: <b>{{ $m['max_kab'] }}</b>
                                        ({{ number_format($m['max_asli']) }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endfor
        <div class="alert alert-info mt-3">
            <small>
                Nilai pada grafik dan informasi di atas diambil dari <b>dataset asli</b>.<br>
                Nilai pada grafik adalah hasil <b>logaritma basis 10</b> dari data asli.<br>
                Sumbu Y dibatasi sampai 10 (log₁₀(10.000.000.000)).<br>
                Contoh: log₁₀(10) = 1, log₁₀(100) = 2, log₁₀(1.000.000) = 6, dst.
            </small>
        </div>
        <div class="text-left mt-4">
            <a href="{{ route('result.clustering') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Hasil Clustering
            </a>
        </div>
    </div>
    <script>
@for ($c = 0; $c < $clusterCount; $c++)
    @if (!empty($clusterAnggota[$c]))
        const labels{{ $c }} = {!! json_encode(array_column($minmax[$c], 'hewan')) !!};
        const minData{{ $c }} = {!! json_encode(array_map(fn($m) => number_format($m['min'], 4, '.', ''), $minmax[$c])) !!};
        const maxData{{ $c }} = {!! json_encode(array_map(fn($m) => number_format($m['max'], 4, '.', ''), $minmax[$c])) !!};
        const minKab{{ $c }} = {!! json_encode(array_column($minmax[$c], 'min_kab')) !!};
        const maxKab{{ $c }} = {!! json_encode(array_column($minmax[$c], 'max_kab')) !!};
        const minAsli{{ $c }} = {!! json_encode(array_column($minmax[$c], 'min_asli')) !!};
        const maxAsli{{ $c }} = {!! json_encode(array_column($minmax[$c], 'max_asli')) !!};

        new Chart(document.getElementById('minMaxHewanChart{{ $c }}').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels{{ $c }},
                datasets: [
                    {
                        label: 'Min',
                        data: minData{{ $c }},
                        backgroundColor: ['#fff59d', '#b3e5fc', '#ef9a9a'],
                        borderColor: ['#fbc02d', '#0288d1', '#b71c1c'],
                        borderWidth: 1,
                        kabupaten: minKab{{ $c }},
                        asli: minAsli{{ $c }},
                    },
                    {
                        label: 'Max',
                        data: maxData{{ $c }},
                        backgroundColor: ['#fbc02d', '#0288d1', '#b71c1c'],
                        borderColor: ['#fbc02d', '#0288d1', '#b71c1c'],
                        borderWidth: 1,
                        kabupaten: maxKab{{ $c }},
                        asli: maxAsli{{ $c }},
                    }
                ]
            },
            options: {
                indexAxis: 'x',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataset = context.dataset;
                                const idx = context.dataIndex;
                                const label = context.chart.data.labels[idx];
                                const logval = context.parsed.y;
                                const kabupaten = dataset.kabupaten ? dataset.kabupaten[idx] : '';
                                const asli = dataset.asli ? dataset.asli[idx] : '';
                                // Tampilkan sesuai permintaan
                                return [
                                    `${label}`,
                                    `${context.dataset.label.toLowerCase()}: ${logval}`,
                                    `angka dataset: ${asli}`,
                                    `kabupaten: ${kabupaten}`
                                ];
                            }
                        }
                    }
                },
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Hewan Ternak' } },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 10,
                        title: { display: true, text: 'log₁₀(Jumlah Hewan Ternak)' }
                    }
                }
            }
        });
    @endif
@endfor
</script>
</body>
</html>