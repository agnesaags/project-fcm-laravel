<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Clustering - Step 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive meta -->
    <!-- Import Bootstrap & FontAwesome untuk styling dan ikon -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Warna dan layout utama */
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
        /* Stepper proses */
        .stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap; /* Responsive: wrap stepper on small screens */
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
        .card-title {
            color: #904f2b;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
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
        /* Responsive: tampilan mobile/tablet */
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
        <!-- Step 1 selesai -->
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <!-- Step 2 selesai -->
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <!-- Step 3 selesai -->
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
        </div>
        <div class="line"></div>
        <!-- Step 4 aktif -->
        <div class="step">
            <div class="circle">4</div>
        </div>
        <div class="line"></div>
        <!-- Step 5 -->
        <div class="step">
            <div class="circle">5</div>
        </div>
    </div>
    <div class="form-card">
        <h2 class="card-title">Iterasi Kedua</h2>
        <div class="accordion" id="accordionStep2">

            <!-- Derajat Keanggotaan Iterasi 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        1. Derajat Keanggotaan Iterasi 2
                    </button>
                </h2>
                <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel derajat keanggotaan iterasi 2 -->
                        <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-warning">
                                    <th>Data ke-</th>
                                    @foreach ($u1[0] as $index => $val)
                                        <th>Derajat Keanggotaan {{ $index + 1 }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($u1 as $i => $row)
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

            <!-- Fungsi Objektif Iterasi 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        2. Fungsi Objektif Iterasi 2
                    </button>
                </h2>
                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                    data-bs-parent="#accordionStep2">
                    <div class="accordion-body">
                        <!-- Tabel fungsi objektif iterasi 2 -->
                        <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-warning">
                                    <th>No</th>
                                    <th>Kabupaten</th>
                                    @for ($i = 0; $i < count($u1[0]); $i++)
                                        <th>P{{ $i + 1 }}</th>
                                    @endfor
                                    <th>Pt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($objectives as $index => $row)
                                    @if (str_contains($row['kabupaten'], 'Fungsi Objektif'))
                                        <tr class="table-warning">
                                            <td colspan="{{ 2 + count($u1[0]) }}" class="text-center">
                                                <strong>{{ $row['kabupaten'] }}</strong>
                                            </td>
                                            <td><strong>{{ $row['pt'] }}</strong></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row['kabupaten'] }}</td>
                                            @for ($i = 0; $i < count($u1[0]); $i++)
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
        <!-- Tombol untuk menjalankan FCM hingga selesai -->
        <div class="mt-4 text-end">
            <a href="{{ route('run-fcm') }}" class="btn btn-simpan">
                Lihat hasil Clutering Fuzzy C-Means
            </a>
        </div>
    </div>
</body>
</html>

{{--
Penjelasan kode:
- <head>: Import Bootstrap, FontAwesome, dan custom CSS untuk tampilan dan responsivitas.
- Stepper: Menampilkan tahapan proses, menandai step ke-4 sebagai aktif.
- .form-card: Card utama berisi seluruh konten step 2.
- Accordion:
    1. Accordion pertama menampilkan tabel derajat keanggotaan hasil iterasi kedua ($u1).
    2. Accordion kedua menampilkan tabel fungsi objektif hasil iterasi kedua ($objectives).
- Semua tabel dibungkus .table-responsive agar bisa discroll di layar kecil.
- Tombol di bawah untuk menjalankan FCM hingga selesai.
- CSS media query memastikan tampilan tetap rapi di layar kecil (mobile/tablet).

Penjelasan file:
File ini adalah view untuk menampilkan hasil iterasi kedua Fuzzy C-Means.
- Menampilkan stepper proses.
- Accordion berisi: tabel derajat keanggotaan dan fungsi objektif iterasi kedua.
- Data diambil dari controller (variabel: $u1, $objectives).
- Tidak ada proses perhitungan di view, hanya menampilkan hasil.
- Sudah responsive dengan table scroll dan stepper wrap.
--}}