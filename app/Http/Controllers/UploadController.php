<?php

namespace App\Http\Controllers; // Namespace controller Laravel

use Illuminate\Http\Request; // Import class Request untuk menangani input HTTP
use Illuminate\Support\Facades\Storage; // Import Storage untuk manajemen file (tidak dipakai di kode ini)
use App\Models\Upload; // Import model Upload

class UploadController extends Controller
{
    // Menampilkan halaman upload
    public function index()
    {
        // Mengembalikan view upload.blade.php
        return view('upload');
    }

    // Proses upload file CSV
    public function store(Request $request)
    {
        // Validasi file yang diupload harus bertipe csv/txt dan maksimal 2MB
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048', // max 2MB
        ]);

        // Jika ada file yang diupload
        if ($request->file('csv_file')) {
            $file = $request->file('csv_file'); // Ambil file dari request
            $filename = time() . '_' . $file->getClientOriginalName(); // Nama file unik (timestamp + nama asli)
            $destinationPath = public_path('/uploads'); // Path tujuan penyimpanan file

            // Pastikan folder uploads sudah ada, jika belum buat foldernya
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Pindahkan file ke folder uploads
            $file->move($destinationPath, $filename);

            // Simpan informasi file ke database
            $upload = new Upload();
            $upload->filename = $filename; // Simpan nama file
            $upload->path = '/uploads/' . $filename; // Simpan path file
            $upload->save(); // Simpan ke database

            // Redirect ke halaman parameter cluster dengan pesan sukses
            return redirect()->route('parameter.index')->with('success', 'File berhasil diupload!');
        }

        // Jika gagal upload, kembali ke halaman upload dengan pesan error
        return back()->with('error', 'Terjadi kesalahan saat upload file.');
    }
}

//
// Penjelasan file:
// File ini adalah controller Laravel untuk proses upload file CSV.
// - Method index() menampilkan halaman upload.
// - Method store() menangani upload file, validasi, simpan file ke folder uploads, dan simpan info file ke database.
// - Jika upload sukses, redirect ke halaman parameter clustering dengan pesan sukses.
// - Jika gagal, kembali ke halaman upload dengan pesan error.
//