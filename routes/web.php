<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\PCIController;
use App\Http\Controllers\ResultController;

Route::get('/', [UploadController::class, 'index'])->name('upload.index');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

Route::get('/parameter', [ClusteringController::class, 'index'])->name('parameter.index');
Route::post('/parameter', [ClusteringController::class, 'store'])->name('parameter.store');

Route::get('/normalize', [ClusteringController::class, 'normalize'])->name('normalize');
Route::get('/clustering', [ClusteringController::class, 'clustering'])->name('clustering');
Route::get('/run-fcm', [ClusteringController::class, 'runFcm'])->name('run-fcm');

Route::get('/clustering/step1', [ClusteringController::class, 'step1'])->name('clustering.step1');
Route::get('/clustering/step2', [ClusteringController::class, 'step2'])->name('clustering.step2');

Route::get('/pci', [PCIController::class, 'index'])->name('pci.index');

Route::get('/result/clustering', [ResultController::class, 'index'])->name('result.clustering');
Route::get('/result/minmax', [ResultController::class, 'minmax'])->name('result.minmax');