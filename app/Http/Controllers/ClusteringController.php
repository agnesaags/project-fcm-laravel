<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class ClusteringController extends Controller
{
    public function index()
    {
        return view('parameter');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cluster' => 'required|integer|min:2|max:10',
            'pangkat' => 'required|numeric|min:1',
            'error' => 'required|numeric|min:0',
            'max_iter' => 'required|integer|min:1',
        ]);

        Session::forget('u_matrix');
        Session::forget('final_u_matrix');
        Session::forget('centroids');
        Session::forget('cluster_summary');
        Session::forget('cluster_scatter_data');
        Session::forget('top_kabupaten_chart');
        Session::forget('pci');
        Session::forget('iterations');
        Session::forget('objective');
        Session::forget('optimum_pci_list');

        Session::put('pangkat', $request->pangkat);
        Session::put('error', $request->error);
        Session::put('max_iter', $request->max_iter);

        $normalizeResult = $this->normalize();
        if ($normalizeResult instanceof \Illuminate\Http\RedirectResponse) {
            return $normalizeResult;
        }

        if ($request->has('optimum')) {
            $bestCluster = 2;
            $bestPci = 0;
            $pciList = [];

            for ($c = 2; $c <= 10; $c++) {
                Session::put('cluster', $c);
                $this->clustering(false);
                $this->runFcm();
                $pci = Session::get('pci');
                $pciList[$c] = $pci;
                if ($pci > $bestPci) {
                    $bestPci = $pci;
                    $bestCluster = $c;
                }
            }

            Session::put('cluster', $bestCluster);
            Session::put('optimum_pci_list', $pciList);

            $this->clustering(false);
            $this->runFcm();

            return redirect()->route('clustering.step1');
        } else {
            Session::put('cluster', $request->cluster);
            $this->clustering(false);
            return redirect()->route('clustering.step1');
        }
    }

    public function normalize()
    {
        $latestUpload = Upload::latest()->first();
        if (!$latestUpload) {
            return redirect()->route('upload.index')->with('error', 'Tidak ada file upload.');
        }

        $path = public_path($latestUpload->path);
        if (!file_exists($path)) {
            return redirect()->route('upload.index')->with('error', 'File upload tidak ditemukan.');
        }

        $data = [];
        if (($handle = fopen($path, "r")) !== false) {
            while (($row = fgetcsv($handle, 1000, ";")) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        if (!$data || count($data) < 2) {
            return redirect()->route('upload.index')->with('error', 'File CSV kosong atau tidak valid.');
        }
        $header = array_map('trim', array_shift($data));

        $map = array_flip($header);
        foreach (['kabupaten', 'kambing', 'sapi', 'ayam'] as $col) {
            if (!isset($map[$col])) {
                return redirect()->route('upload.index')->with('error', "Kolom '$col' tidak ditemukan di file CSV.");
            }
        }

        $kambing = [];
        $sapi = [];
        $ayam = [];
        $original_data = [];

        foreach ($data as $row) {
            if (
                !isset($row[$map['kabupaten']], $row[$map['kambing']], $row[$map['sapi']], $row[$map['ayam']]) ||
                trim($row[$map['kabupaten']]) === '' ||
                trim($row[$map['kambing']]) === '' ||
                trim($row[$map['sapi']]) === '' ||
                trim($row[$map['ayam']]) === ''
            ) {
                continue;
            }
            $val_kambing = floatval(str_replace(',', '', $row[$map['kambing']]));
            $val_sapi    = floatval(str_replace(',', '', $row[$map['sapi']]));
            $val_ayam    = floatval(str_replace(',', '', $row[$map['ayam']]));

            $kambing[] = $val_kambing;
            $sapi[]    = $val_sapi;
            $ayam[]    = $val_ayam;
            $original_data[] = [
                'kabupaten' => $row[$map['kabupaten']],
                'kambing'   => $val_kambing,
                'sapi'      => $val_sapi,
                'ayam'      => $val_ayam,
            ];
        }

        if (count($original_data) === 0) {
            return redirect()->route('upload.index')->with('error', 'Data CSV tidak valid atau kosong.');
        }

        Session::put('original_data', $original_data);

        $norm_kambing = $this->normalizeArray($kambing);
        $norm_sapi    = $this->normalizeArray($sapi);
        $norm_ayam    = $this->normalizeArray($ayam);

        $normalized = [];
        foreach ($original_data as $idx => $row) {
            $normalized[] = [
                'kabupaten' => $row['kabupaten'],
                'kambing'   => $norm_kambing[$idx],
                'sapi'      => $norm_sapi[$idx],
                'ayam'      => $norm_ayam[$idx],
            ];
        }
        Session::put('normalized_data', $normalized);
    }

    private function normalizeArray($array)
    {
        $min = min($array);
        $max = max($array);

        return array_map(function ($value) use ($min, $max) {
            if ($max == $min) return 0;
            return ($value - $min) / ($max - $min);
        }, $array);
    }

    public function clustering($redirect = true)
    {
        $normalized = Session::get('normalized_data');
        $cluster = Session::get('cluster');

        if (!$normalized || !$cluster) {
            return redirect()->route('parameter.index')->with('error', 'Data atau parameter tidak tersedia.');
        }

        Session::forget('u_matrix');

        $n = count($normalized);
        $u = [];

        $hashSource = json_encode($normalized) . '|' . $cluster;
        $hash = hash('sha256', $hashSource);

        $seed = hexdec(substr($hash, 0, 8));
        mt_srand($seed);

        for ($i = 0; $i < $n; $i++) {
            $row = [];
            $total = 0;

            for ($j = 0; $j < $cluster; $j++) {
                $rand = mt_rand(1, 1000) / 1000;
                $row[] = $rand;
                $total += $rand;
            }

            $row = array_map(function ($value) use ($total) {
                return $value / $total;
            }, $row);

            $u[] = $row;
        }

        Session::put('u_matrix', $u);

        if ($redirect) {
            return redirect()->route('run-fcm')->with('success', 'Matriks derajat keanggotaan awal berhasil dibuat dan dikunci.');
        }
    }

    public function step1()
    {
        $data = Session::get('normalized_data');
        $u = Session::get('u_matrix');
        $c = Session::get('cluster');
        $m = Session::get('pangkat');

        if (!$data || !$u) {
            return redirect()->route('parameter.index')->with('error', 'Data tidak tersedia.');
        }

        $n = count($data);
        $d = 3;

        $centroids = [];
        for ($k = 0; $k < $c; $k++) {
            $num = array_fill(0, $d, 0);
            $denom = 0;

            for ($i = 0; $i < $n; $i++) {
                $attributes = [
                    $data[$i]['kambing'],
                    $data[$i]['ayam'],
                    $data[$i]['sapi'],
                ];
                for ($j = 0; $j < $d; $j++) {
                    $num[$j] += pow($u[$i][$k], $m) * $attributes[$j];
                }
                $denom += pow($u[$i][$k], $m);
            }

            for ($j = 0; $j < $d; $j++) {
                $centroids[$k][$j] = $denom == 0 ? 0 : $num[$j] / $denom;
            }
        }

        $objectives = [];
        $sumP = array_fill(0, $c, 0);
        $sumPt = 0;

        for ($i = 0; $i < $n; $i++) {
            $attributes = [
                $data[$i]['kambing'],
                $data[$i]['ayam'],
                $data[$i]['sapi'],
            ];

            $p = array_fill(0, $c, 0);

            foreach ($centroids as $k => $cVal) {
                $distance = 0;
                for ($j = 0; $j < $d; $j++) {
                    $distance += pow($attributes[$j] - $cVal[$j], 2);
                }
                $weight = pow($u[$i][$k], $m);
                $contribution = $weight * $distance;
                $p[$k] = $contribution;
            }

            $pt = array_sum($p);

            foreach ($p as $k => $val) {
                $sumP[$k] += $val;
            }
            $sumPt += $pt;

            $rowObj = [
                'kabupaten' => $data[$i]['kabupaten'],
                'pt' => number_format($pt, 4, '.', ''),
            ];
            for ($k = 0; $k < $c; $k++) {
                $rowObj['p' . ($k + 1)] = number_format($p[$k], 4, '.', '');
            }
            $objectives[] = $rowObj;
        }

        $rowObj = [
            'kabupaten' => 'Fungsi Objektif Akhir Iterasi 1',
            'pt' => number_format($sumPt, 4, '.', ''),
        ];
        for ($k = 0; $k < $c; $k++) {
            $rowObj['p' . ($k + 1)] = '';
        }
        $objectives[] = $rowObj;

        return view('cluster-step1', [
            'normalized' => $data,
            'uMatrix' => $u,
            'centroids' => $centroids,
            'objectives' => $objectives,
            'totalPt' => number_format($sumPt, 4, '.', ''),
        ]);
    }

    public function step2()
    {
        $data = Session::get('normalized_data');
        $u0 = Session::get('u_matrix');
        $c = Session::get('cluster');
        $m = Session::get('pangkat');

        if (!$data || !$u0) {
            return redirect()->route('clustering.step1')->with('error', 'Data tidak tersedia.');
        }

        $n = count($data);
        $d = 3;

        $centroids = [];
        for ($k = 0; $k < $c; $k++) {
            $num = array_fill(0, $d, 0);
            $denom = 0;

            for ($i = 0; $i < $n; $i++) {
                $attributes = [$data[$i]['kambing'], $data[$i]['ayam'], $data[$i]['sapi']];
                for ($j = 0; $j < $d; $j++) {
                    $num[$j] += pow($u0[$i][$k], $m) * $attributes[$j];
                }
                $denom += pow($u0[$i][$k], $m);
            }

            for ($j = 0; $j < $d; $j++) {
                $centroids[$k][$j] = $denom == 0 ? 0 : $num[$j] / $denom;
            }
        }

        $u1 = [];
        $objectives = [];
        $sumP = array_fill(0, $c, 0);
        $sumPt = 0;

        for ($i = 0; $i < $n; $i++) {
            $attributes = [$data[$i]['kambing'], $data[$i]['ayam'], $data[$i]['sapi']];

            $distances = [];
            foreach ($centroids as $k => $cVal) {
                $distance = 0;
                for ($j = 0; $j < $d; $j++) {
                    $distance += pow($attributes[$j] - $cVal[$j], 2);
                }
                $distances[$k] = sqrt($distance);
            }

            $row = [];
            foreach ($distances as $k => $dk) {
                $sum = 0;
                foreach ($distances as $kk => $dkk) {
                    $ratio = ($dk == 0 || $dkk == 0) ? 1 : $dk / $dkk;
                    $sum += pow($ratio, 2 / ($m - 1));
                }
                $row[$k] = 1 / $sum;
            }
            $u1[$i] = $row;

            $p = array_fill(0, $c, 0);
            foreach ($row as $k => $uVal) {
                $weight = pow($uVal, $m);
                $contribution = $weight * pow($distances[$k], 2);
                $p[$k] = $contribution;
            }
            $pt = array_sum($p);

            foreach ($p as $k => $val) {
                $sumP[$k] += $val;
            }
            $sumPt += $pt;

            $rowObj = [
                'kabupaten' => $data[$i]['kabupaten'],
                'pt' => number_format($pt, 4, '.', ''),
            ];
            for ($k = 0; $k < $c; $k++) {
                $rowObj['p' . ($k + 1)] = number_format($p[$k], 4, '.', '');
            }
            $objectives[] = $rowObj;
        }

        $rowObj = [
            'kabupaten' => 'Fungsi Objektif Akhir Iterasi 2',
            'pt' => number_format($sumPt, 4, '.', ''),
        ];
        for ($k = 0; $k < $c; $k++) {
            $rowObj['p' . ($k + 1)] = '';
        }
        $objectives[] = $rowObj;

        Session::put('u1_matrix', $u1);
        Session::put('objective_iter2', $objectives);

        return view('cluster-step2', [
            'u1' => $u1,
            'objectives' => $objectives,
            'totalPt' => number_format($sumPt, 4, '.', ''),
        ]);
    }

    public function runFcm()
    {
        $data = Session::get('normalized_data');
        $u = Session::get('u_matrix');
        $c = Session::get('cluster');
        $m = Session::get('pangkat');
        $error_min = Session::get('error');
        $max_iter = Session::get('max_iter');

        if (!$data || !$u) {
            return redirect()->route('upload.index')->with('error', 'Data tidak ditemukan.');
        }

        $n = count($data);
        $d = 3;
        $iteration = 0;
        $previous_objective = 0;
        $current_objective = INF;

        do {
            $centroids = [];
            for ($k = 0; $k < $c; $k++) {
                $num = array_fill(0, $d, 0);
                $denom = 0;

                for ($i = 0; $i < $n; $i++) {
                    $attributes = [$data[$i]['kambing'], $data[$i]['ayam'], $data[$i]['sapi']];
                    for ($j = 0; $j < $d; $j++) {
                        $num[$j] += pow($u[$i][$k], $m) * $attributes[$j];
                    }
                    $denom += pow($u[$i][$k], $m);
                }

                for ($j = 0; $j < $d; $j++) {
                    $centroids[$k][$j] = $denom == 0 ? 0 : $num[$j] / $denom;
                }
            }

            $new_u = [];
            $current_objective = 0;

            for ($i = 0; $i < $n; $i++) {
                $attributes = [$data[$i]['kambing'], $data[$i]['ayam'], $data[$i]['sapi']];
                $distances = [];

                for ($k = 0; $k < $c; $k++) {
                    $distance = 0;
                    for ($j = 0; $j < $d; $j++) {
                        $distance += pow($attributes[$j] - $centroids[$k][$j], 2);
                    }
                    $distances[$k] = sqrt($distance);
                }

                for ($k = 0; $k < $c; $k++) {
                    $sum = 0;
                    for ($kk = 0; $kk < $c; $kk++) {
                        $ratio = ($distances[$k] == 0 || $distances[$kk] == 0) ? 1 : $distances[$k] / $distances[$kk];
                        $sum += pow($ratio, 2 / ($m - 1));
                    }
                    $new_u[$i][$k] = 1 / $sum;
                }

                for ($k = 0; $k < $c; $k++) {
                    $current_objective += pow($new_u[$i][$k], $m) * pow($distances[$k], 2);
                }
            }

            $error = abs($current_objective - $previous_objective);
            $previous_objective = $current_objective;
            $u = $new_u;

            $iteration++;
        } while ($error > $error_min && $iteration < $max_iter);

        Session::put('centroids', $centroids);
        Session::put('final_u_matrix', $u);
        Session::put('iterations', $iteration);
        Session::put('objective', $current_objective);

        $normalized = Session::get('normalized_data');
        $clusterSummary = [];

        foreach ($centroids as $clusterIdx => $centroid) {
            $maxVal = max($centroid);
            $dominantIndex = array_search($maxVal, $centroid);
            $hewan = ['Kambing', 'Ayam', 'Sapi'][$dominantIndex];

            $minDist = INF;
            $kabupatenDominan = '';

            foreach ($normalized as $row) {
                $attrs = [$row['kambing'], $row['ayam'], $row['sapi']];
                $distance = 0;
                for ($j = 0; $j < 3; $j++) {
                    $distance += pow($attrs[$j] - $centroid[$j], 2);
                }
                $distance = sqrt($distance);

                if ($distance < $minDist) {
                    $minDist = $distance;
                    $kabupatenDominan = $row['kabupaten'];
                }
            }

            $clusterSummary[] = [
                'cluster' => $clusterIdx + 1,
                'hewan' => $hewan,
                'nilai' => $maxVal,
                'kabupaten' => $kabupatenDominan
            ];
        }
        Session::put('cluster_summary', $clusterSummary);

        $clusterScatter = [];
        foreach ($u as $i => $uRow) {
            $maxIdx = array_keys($uRow, max($uRow))[0];
            $row = $normalized[$i];
            $maxHewan = max($row['kambing'], $row['ayam'], $row['sapi']);
            $hewan = 'Kambing';
            if ($row['ayam'] == $maxHewan) $hewan = 'Ayam';
            if ($row['sapi'] == $maxHewan) $hewan = 'Sapi';

            $clusterScatter[$maxIdx][] = [
                'kabupaten' => $row['kabupaten'],
                'kambing' => $row['kambing'],
                'ayam' => $row['ayam'],
                'sapi' => $row['sapi'],
                'hewan' => $hewan
            ];
        }
        Session::put('cluster_scatter_data', $clusterScatter);

        $kabupatenTernak = [];
        foreach ($normalized as $row) {
            $total = $row['kambing'] + $row['ayam'] + $row['sapi'];
            $kabupatenTernak[] = [
                'kabupaten' => $row['kabupaten'],
                'kambing' => $row['kambing'],
                'ayam' => $row['ayam'],
                'sapi' => $row['sapi'],
                'total' => $total
            ];
        }
        usort($kabupatenTernak, fn($a, $b) => $b['total'] <=> $a['total']);
        $top3Kabupaten = array_slice($kabupatenTernak, 0, 3);
        Session::put('top_kabupaten_chart', $top3Kabupaten);

        $n = count($u);
        $c = count($u[0]);
        $sum = 0;
        foreach ($u as $row) {
            foreach ($row as $membership) {
                $sum += pow($membership, 2);
            }
        }
        $pci = $n > 0 ? $sum / $n : 0;
        Session::put('pci', $pci);

        // Ubah route ke result.clustering (bukan result.index)
        return redirect()->route('result.clustering')->with('success', 'Fuzzy C-Means berhasil dijalankan!');
    }
}