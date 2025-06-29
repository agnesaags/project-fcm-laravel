<!-- filepath: d:\Fuzzy\project-fcm-laravel\resources\views\cluster-step1.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Clustering - Step 1</title>
    <!-- Bootstrap & FontAwesome untuk styling dan ikon -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        <div class="step">
            <div class="circle">3</div>
        </div>
        <div class="line"></div>
        <div class="step">
            <div class="circle">4</div>
        </div>
        <div class="line"></div>
        <div class="step">
            <div class="circle">5</div>
        </div>
    </div>

    <div class="form-card">
        <h2>Normalisasi & Iterasi 1</h2>
        <div class="accordion" id="accordionStep2">

            <!-- 1. Normalisasi Data -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        1. Normalisasi Data
                    </button>
                </h2>
                <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel hasil normalisasi data -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Kabupaten/Kota</th>
                                        <th>Kambing</th>
                                        <th>Ayam</th>
                                        <th>Sapi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($normalized as $row)
                                        <tr>
                                            <td>{{ $row['kabupaten'] }}</td>
                                            <td>{{ number_format($row['kambing'], 4) }}</td>
                                            <td>{{ number_format($row['ayam'], 4) }}</td>
                                            <td>{{ number_format($row['sapi'], 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Derajat Keanggotaan Iterasi 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        2. Derajat Keanggotaan Iterasi 1
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel derajat keanggotaan awal -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Data ke-</th>
                                        @foreach ($uMatrix[0] as $index => $val)
                                            <th>Derajat Keanggotaan {{ $index + 1 }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($uMatrix as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            @foreach ($row as $val)
                                                <td>{{ number_format($val, 4) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pusat Cluster Iterasi 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                        3. Pusat Cluster
                    </button>
                </h2>
                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel pusat cluster hasil iterasi 1 -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Cluster</th>
                                        <th>Kambing</th>
                                        <th>Ayam</th>
                                        <th>Sapi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($centroids as $i => $c)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ number_format($c[0], 4) }}</td>
                                            <td>{{ number_format($c[1], 4) }}</td>
                                            <td>{{ number_format($c[2], 4) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Fungsi Objektif Iterasi 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                        4. Fungsi Objektif Iterasi 1
                    </button>
                </h2>
                <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel perhitungan fungsi objektif -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-warning">
                                    <tr>
                                        <th>No</th>
                                        <th>Kabupaten</th>
                                        @for ($i = 0; $i < count($centroids); $i++)
                                            <th>P{{ $i + 1 }}</th>
                                        @endfor
                                        <th>Pt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($objectives as $index => $row)
                                        @if (str_contains($row['kabupaten'], 'Fungsi Objektif'))
                                            <tr class="table-warning">
                                                <td colspan="{{ 2 + count($centroids) }}" class="text-center">
                                                    <strong>{{ $row['kabupaten'] }}</strong>
                                                </td>
                                                <td><strong>{{ $row['pt'] }}</strong></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $row['kabupaten'] }}</td>
                                                @for ($i = 0; $i < count($centroids); $i++)
                                                    <td>{{ $row['p' . ($i + 1)] }}</td>
                                                @endfor
                                                <td>{{ $row['pt'] }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tombol untuk lanjut ke iterasi berikutnya -->
        <div class="mt-4 text-end">
            <a href="{{ route('clustering.step2') }}" class="btn btn-simpan">Lanjut ke Iterasi 2</a>
        </div>
    </div>
</body>

</html>

{{-- 
Penjelasan file:
File ini adalah view untuk menampilkan hasil normalisasi dan iterasi pertama Fuzzy C-Means.
- Menampilkan stepper proses.
- Accordion berisi: tabel dataset asli, tabel normalisasi, derajat keanggotaan, pusat cluster, dan fungsi objektif.
- Data diambil dari controller (variabel: $original, $normalized, $uMatrix, $centroids, $objectives).
- Tidak ada proses perhitungan di view, hanya menampilkan hasil.
- Sudah responsive dengan table scroll dan stepper wrap.
--}}