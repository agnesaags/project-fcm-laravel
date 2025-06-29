<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Input Parameter Clustering</title>
    <!-- Import Bootstrap & FontAwesome untuk styling dan ikon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: #d8eaa5;
        }
        .form-card {
            background-color: #fffde7;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: auto;
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
        .text-center{
            color: #904f2b;
            font-weight: bold;
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
            .form-card h3 {
                font-size: 1.2rem;
            }
            .circle {
                width: 24px;
                height: 24px;
                font-size: 13px;
            }
        }
    </style>
    <script>
        // Validasi form sebelum submit
        function validateForm() {
            let valid = true;
            let cluster = document.getElementById('cluster');
            let pangkat = document.getElementById('pangkat');
            let error = document.getElementById('error');
            let max_iter = document.getElementById('max_iter');

            // Hapus pesan error sebelumnya
            document.querySelectorAll('.invalid-feedback').forEach(e => e.remove());
            cluster.classList.remove('is-invalid');
            pangkat.classList.remove('is-invalid');
            error.classList.remove('is-invalid');
            max_iter.classList.remove('is-invalid');

            // Cluster: min 3, max 10
            if (cluster.value < 3 || cluster.value > 10) {
                showAlert(cluster, 'Jumlah cluster minimal 3 dan maksimal 10.');
                valid = false;
            }

            // Pangkat: harus 2
            if (pangkat.value != 2) {
                showAlert(pangkat, 'Pangkat harus bernilai 2.');
                valid = false;
            }

            // Error: harus 0.001
            if (parseFloat(error.value) !== 0.001) {
                showAlert(error, 'Error minimum harus 0.001.');
                valid = false;
            }

            // Maksimal iterasi: harus 1000
            if (max_iter.value != 1000) {
                showAlert(max_iter, 'Maksimal iterasi harus 1000.');
                valid = false;
            }

            return valid;
        }

        // Fungsi untuk menampilkan pesan error pada input
        function showAlert(input, message) {
            input.classList.add('is-invalid');
            let div = document.createElement('div');
            div.className = 'invalid-feedback';
            div.innerText = message;
            input.parentNode.appendChild(div);
        }
    </script>
</head>
<body class="py-5">
    <!-- Stepper: visualisasi tahapan proses -->
    <div class="stepper">
        <div class="step completed">
            <div class="circle"><i class="fas fa-check"></i></div>
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

    <div class="form-card">
        <h3 class="text-center fw-bold mb-3">Input Parameter Clustering</h3>

        <!-- Tampilkan pesan sukses jika ada -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Tampilkan pesan error validasi jika ada -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form input parameter clustering -->
        <form action="{{ route('parameter.store') }}" method="POST" onsubmit="return validateForm()">
            @csrf

            <!-- Input jumlah cluster -->
            <div class="mb-3">
                <label for="cluster" class="form-label">Jumlah Cluster (c)</label>
                <input type="number" class="form-control" name="cluster" id="cluster" min="3" max="10" required>
            </div>

            <!-- Input pangkat -->
            <div class="mb-3">
                <label for="pangkat" class="form-label">Pangkat (w)</label>
                <input type="number" step="0.1" class="form-control" name="pangkat" id="pangkat" min="2" max="2" required>
            </div>

            <!-- Input error minimum -->
            <div class="mb-3">
                <label for="error" class="form-label">Error Minimum (ξ)</label>
                <input type="number" step="0.001" class="form-control" name="error" id="error" min="0.001" max="0.001" required>
            </div>

            <!-- Input maksimal iterasi -->
            <div class="mb-3">
                <label for="max_iter" class="form-label">Maksimal Iterasi</label>
                <input type="number" class="form-control" name="max_iter" id="max_iter" min="1000" max="1000" required>
            </div>

            <!-- Checkbox untuk mencari cluster optimum -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="optimum" name="optimum" value="1">
                <label class="form-check-label" for="optimum">Cari Cluster Optimum (PCI Tertinggi)</label>
            </div>

            <!-- Tombol submit -->
            <div class="d-grid">
                <button type="submit" class="btn btn-simpan">Simpan Parameter</button>
            </div>
        </form>
    </div>

</body>
</html>

{{--
Penjelasan file:
File ini adalah view untuk input parameter clustering Fuzzy C-Means.
- Menampilkan stepper proses.
- Form input: jumlah cluster, pangkat, error minimum, maksimal iterasi, dan opsi cari cluster optimum.
- Validasi form dilakukan dengan JavaScript sebelum submit.
- Pesan error/sukses tampil otomatis.
- Sudah responsive dengan media query dan layout Bootstrap.
--}}