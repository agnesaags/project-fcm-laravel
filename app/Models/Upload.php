<?php

namespace App\Models; // Mendefinisikan namespace model ini

use Illuminate\Database\Eloquent\Factories\HasFactory; // Trait untuk factory Eloquent
use Illuminate\Database\Eloquent\Model; // Kelas dasar model Eloquent

// Model Upload untuk tabel 'uploads'
class Upload extends Model
{
    use HasFactory; // Mengaktifkan fitur factory pada model

    // Nama tabel yang digunakan model ini
    protected $table = 'uploads';

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'filename', // Nama file yang diupload
        'path',     // Path/letak file di server
    ];
}

//
// Penjelasan file:
// File ini adalah model Eloquent Laravel untuk tabel 'uploads'.
// Model ini digunakan untuk menyimpan dan mengelola data file upload (nama file dan path).
// Dengan $fillable, hanya kolom 'filename' dan 'path' yang bisa diisi secara mass-assignment.
// Model ini juga mendukung factory untuk kebutuhan seeding/testing.
//