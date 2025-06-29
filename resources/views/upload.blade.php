<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Data Hewan Ternak</title>
    <!-- Import Bootstrap & FontAwesome untuk styling dan ikon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #d2e39a; /* hijau lembut */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        .container {
            padding: 20px;
        }
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
        .card {
            background-color: #fffde7; /* Latar kartu kuning lembut */
            border-radius: 12px;
        }
        .card-title {
            color: #904f2b; /* hijau tua */
            font-weight: bold;
        }
        .btn-upload {
            background-color: #8bc34a;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 500;
            border: none;
        }
        .btn-upload:hover {
            background-color: #7cb342;
        }
        @media (max-width: 768px) {
            .container {
                padding: 5px;
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
            .btn-upload {
                width: 100%;
                padding: 10px 0;
            }
        }
        @media (max-width: 576px) {
            .card-title {
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
<body class="py-5">

    <!-- Stepper: visualisasi tahapan proses -->
    <div class="stepper">
        <div class="step">
            <div class="circle">1</div>
        </div>
        <div class="line"></div>
        <div class="step">
            <div class="circle">2</div>
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

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body">
                        <!-- Judul halaman -->
                        <h2 class="card-title mb-4 text-center">Clustering Hewan Ternak</h2>
                        <!-- Subjudul -->
                        <p class="text-center">Upload File CSV Hewan Ternak</p>

                        <!-- Pesan sukses jika ada -->
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <!-- Pesan error validasi jika ada -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form upload file CSV -->
                        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">Pilih file CSV:</label>
                                <input type="file" class="form-control" name="csv_file" id="csv_file" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-upload">Upload</button>
                            </div>
                        </form>
                        <!-- Akhir form -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

{{--
Penjelasan file:
File ini adalah view untuk upload data hewan ternak dalam format CSV.
- Menampilkan stepper proses di atas.
- Form upload file CSV dengan validasi dan pesan error/sukses.
- Menggunakan Bootstrap agar tampilan rapi dan responsive.
- Stepper dan card sudah responsive dengan media query.
--}}