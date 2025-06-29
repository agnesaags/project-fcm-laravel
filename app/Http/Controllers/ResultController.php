<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class ResultController extends Controller
{
    public function index()
    {
        return view('result-clustering');
    }

    public function minmax()
    {
        $original = session('original_data');
        $final_u = session('final_u_matrix');
        $hewan = ['kambing', 'ayam', 'sapi'];
        $labels = ['Kambing', 'Ayam', 'Sapi'];
        $clusterCount = session('cluster', 3); // default 3 jika tidak ada

        $clusterAnggota = [];
        if ($final_u && $original) {
            foreach ($final_u as $i => $uRow) {
                $maxIdx = array_search(max($uRow), $uRow);
                $clusterAnggota[$maxIdx][] = $original[$i];
            }
        }

        $minmax = [];
        for ($c = 0; $c < $clusterCount; $c++) {
            $anggota = $clusterAnggota[$c] ?? [];
            $minmaxCluster = [];
            foreach ($hewan as $h) {
                if (count($anggota) > 0) {
                    $logArr = array_map(function ($row) use ($h) {
                        return $row[$h] > 0 ? log10($row[$h]) : 0;
                    }, $anggota);

                    $min = min($logArr);
                    $max = max($logArr);

                    $min_kab = '';
                    $max_kab = '';
                    $min_asli = null;
                    $max_asli = null;
                    foreach ($anggota as $row) {
                        $logVal = $row[$h] > 0 ? log10($row[$h]) : 0;
                        if ($logVal == $min) {
                            $min_kab = $row['kabupaten'];
                            $min_asli = $row[$h];
                        }
                        if ($logVal == $max) {
                            $max_kab = $row['kabupaten'];
                            $max_asli = $row[$h];
                        }
                    }

                    $minmaxCluster[] = [
                        'hewan' => ucfirst($h),
                        'min' => $min,
                        'min_kab' => $min_kab,
                        'min_asli' => $min_asli,
                        'max' => $max,
                        'max_kab' => $max_kab,
                        'max_asli' => $max_asli,
                    ];
                } else {
                    $minmaxCluster[] = [
                        'hewan' => ucfirst($h),
                        'min' => null,
                        'min_kab' => null,
                        'min_asli' => null,
                        'max' => null,
                        'max_kab' => null,
                        'max_asli' => null,
                    ];
                }
            }
            $minmax[] = $minmaxCluster;
        }

        return view('result-minmax', [
            'labels' => $labels,
            'minmax' => $minmax,
            'clusterCount' => $clusterCount,
            'clusterAnggota' => $clusterAnggota,
        ]);
    }
}
