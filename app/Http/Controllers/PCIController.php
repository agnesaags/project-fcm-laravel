<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Import Request untuk menangani input HTTP (tidak dipakai di sini)
use Illuminate\Support\Facades\Session; // Import Session untuk menyimpan/mengambil data session

class PCIController extends Controller
{
    // Method untuk menghitung Partition Coefficient Index (PCI)
    public function index()
    {
        $u = Session::get('final_u_matrix'); // Ambil matriks derajat keanggotaan dari session

        // Jika data tidak ditemukan, redirect ke halaman upload dengan pesan error
        if (!$u) {
            return redirect()->route('upload.index')->with('error', 'Data derajat keanggotaan tidak ditemukan.');
        }

        $n = count($u); // jumlah data (baris pada matriks U)
        $c = count($u[0]); // jumlah cluster (kolom pada matriks U)

        $sum = 0; // Inisialisasi penjumlahan
        foreach ($u as $i => $row) { // Loop setiap data
            foreach ($row as $j => $membership) { // Loop setiap cluster
                $sum += pow($membership, 2); // Tambahkan kuadrat derajat keanggotaan
            }
        }

        $pci = $sum / $n; // Hitung Partition Coefficient Index

        // Simpan PCI ke session
        Session::put('pci', $pci);

        // Setelah hitung PCI, kembali ke hasil clustering dengan pesan sukses
        return redirect()->route('result.index')->with('success', 'Partition Coefficient Index berhasil dihitung!');
    }
}

//
// Penjelasan file:
// File ini adalah controller Laravel untuk menghitung Partition Coefficient Index (PCI) dari matriks derajat keanggotaan (U).
// - Data U diambil dari session ('final_u_matrix').
// - PCI dihitung dengan rumus: PCI = (jumlah kuadrat semua derajat keanggotaan) / jumlah data.
// - Hasil PCI disimpan ke session ('pci').
// - Jika data tidak ada, redirect ke upload. Jika sukses, redirect ke hasil clustering.
// - Tidak ada tampilan, hanya proses dan redirect.
//