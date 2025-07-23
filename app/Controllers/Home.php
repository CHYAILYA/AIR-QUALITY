<?php

namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;
class Home extends BaseController
{
      protected $session;

    public function __construct() 
    {
// Ambil service session secara benar
        $this->session = \Config\Services::session();
        // Load API key from environment variable or config file
        $this->geminiApiKey = getenv('GEMINI_API_KEY') ?: '';
    }

    public function getRecommendation()
    {
        $status = $this->request->getVar('status');
        
        if (!$status) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Status parameter is required'
            ])->setStatusCode(400);
        }

        // Default recommendations as fallback
        $defaultRecommendations = [
            'baik' => "Kualitas udara baik, tetap jaga kebersihan lingkungan",
            'sedang' => "Kualitas udara sedang, batasi aktivitas luar ruangan", 
            'buruk' => "Kualitas udara buruk, gunakan masker jika keluar rumah"
        ];

        try {
            $client = \Config\Services::curlrequest();
            
            $prompt = "Berikan rekomendasi detail untuk kondisi kualitas udara {$status} dalam format berikut:

REKOMENDASI: [berikan rekomendasi dalam 1-2 kalimat]

GAMBAR: [berikan 1 link gambar preview yang relevan dengan kondisi udara, gunakan format URL lengkap https://]

BERITA: [berikan 1 link berita terkait kualitas udara dari media terpercaya, gunakan format URL lengkap https://]

Pastikan setiap bagian dipisahkan dengan baris baru dan menggunakan format yang tepat.";

            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-04-17:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->geminiApiKey
                ],
                'json' => [
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [[
                            'text' => $prompt
                        ]]
                    ]]
                ]
            ]);

            // Kemudian tambahkan parsing untuk memisahkan bagian-bagian respons
            $result = json_decode($response->getBody(), true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Parse response
            $parts = array_filter(explode("\n", $text));
            $parsed = [
                'recommendation' => '',
                'image_url' => '',
                'news_url' => ''
            ];

            foreach ($parts as $part) {
                if (strpos($part, 'REKOMENDASI:') !== false) {
                    $parsed['recommendation'] = trim(str_replace('REKOMENDASI:', '', $part));
                } elseif (strpos($part, 'GAMBAR:') !== false) {
                    $parsed['image_url'] = trim(str_replace('GAMBAR:', '', $part));
                } elseif (strpos($part, 'BERITA:') !== false) {
                    $parsed['news_url'] = trim(str_replace('BERITA:', '', $part));
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $parsed
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Gemini API Error: ' . $e->getMessage());
            
            // Return default recommendation on error
            return $this->respond([
                'status' => 'success',
                'recommendation' => $defaultRecommendations[strtolower($status)] ?? '-'
            ]);
        }
    }
    public function index()
    {
        // 1) Ambil data dari API eksternal
        $client = \Config\Services::curlrequest();
        try {
            $res = $client->get('https://udara.unis.ac.id/api/');
            $json = json_decode($res->getBody(), true);
            
            if (!isset($json['status']) || $json['status'] !== 'success') {
                throw new \Exception('API response error');
            }
            $apiData = $json['data'];
        } catch (\Exception $e) {
            log_message('error', 'API Error: ' . $e->getMessage());
            $apiData = $this->getFallbackData();
        }

        // 2) Proses data utama
        $latest = $apiData['latest_data'];
        $fuzzy = $apiData['fuzzy_results'];
        
        // 3) Hitung status sensor
        $sensorStatus = [
            'sharp'  => $this->getSensorStatus($fuzzy['Sharp (Debu)'] ?? []),
            'mq7'    => $this->getSensorStatus($fuzzy['MQ7 (CO)'] ?? []),
            'mq135'  => $this->getSensorStatus($fuzzy['MQ135 (CO2)'] ?? []),
            'mq131'  => $this->getSensorStatus($fuzzy['MQ131 (O3)'] ?? [])
        ];

        // 4) Ambil data hourly langsung dari database
        $hourly_data = $this->getHourlyData();

        // 5) Siapkan data untuk view TERLEBIH DAHULU
        $viewData = [
            'aqi'             => $apiData['aqi'],
            'fuzzy_results'   => $fuzzy,
            'latest_data'     => $latest,
            'sensor_status'   => $sensorStatus,
            'overall_aqi'     => $apiData['overall_aqi'],
            'api_timestamp'   => $apiData['timestamp'],
            'hourly_data'     => $hourly_data,
            'aqiInfo'         => $this->getAqiStatusDescription($apiData['aqi']['value'])
        ];
    
        // 6) Baru tambahkan rekomendasi Gemini
        $viewData['health_recommendations'] = $this->getGeminiRecommendations(
            $viewData['aqi']['status'], 
            $viewData['aqi']['value']
        );

        return view('Dasboard', $viewData);
    }

public function air()
{
    $userData = $this->session->get('userData');
    if (!$userData || $userData['role'] != 3) {
        $this->session->destroy();
        return redirect()->to('/Login')->with('error', 'Akses ditolak');
    }
    return view('air');
}
    private $geminiApiKey = '';
    private function getGeminiRecommendations($aqiStatus, $aqiValue)
    {
        $cacheKey = 'gemini_recommendations_' . $aqiStatus;
        $cache = \Config\Services::cache();
        
        // Cek cache setiap 30 menit
        if($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $client = \Config\Services::curlrequest();
        
        $prompt = "Buat rekomendasi kesehatan berdasarkan AQI {$aqiValue} dengan status {$aqiStatus} untuk warga Tangerang. 
        Gunakan struktur:
        - Aktivitas luar ruangan
        - Penggunaan masker
        - Ventilasi udara
        - Alat pelindung
        Format dalam HTML sederhana tanpa styling, maksimal 4 poin.";

        try {
            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
                'query' => ['key' => $this->geminiApiKey],
                'json' => [
                    'contents' => [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'timeout' => 10
            ]);

            $result = json_decode($response->getBody(), true);
            $recommendations = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Simpan ke cache 30 menit
            $cache->save($cacheKey, $recommendations, 1800);

            return $recommendations;

        } catch (\Exception $e) {
            log_message('error', 'Gemini API Error: '.$e->getMessage());
            return $this->getDefaultRecommendations($aqiStatus);
        }
    }

    private function getDefaultRecommendations($aqiStatus)
{
    // Konversi status ke format yang konsisten
    $statusMap = [
        'baik' => 'Baik',
        'sedang' => 'Sedang',
        'tidak-sehat-untuk-kelompok-sensitif' => 'Tidak Sehat Kelompok Sensitif',
        'tidak-sehat' => 'Tidak Sehat',
        'sangat-tidak-sehat' => 'Sangat Tidak Sehat',
        'berbahaya' => 'Berbahaya'
    ];

    $normalizedStatus = strtolower(str_replace(' ', '-', $aqiStatus));
    $statusKey = $statusMap[$normalizedStatus] ?? 'unknown';

    $defaults = [
        'Baik' => '
            <div class="recommendation-item">
                <span class="material-icons">nature_people</span>
                <div>Aman untuk aktivitas luar ruangan</div>
            </div>
            <div class="recommendation-item">
                <span class="material-icons">air</span>
                <div>Pertahankan ventilasi alami</div>
            </div>',
            
        'Sedang' => '
            <div class="recommendation-item">
                <span class="material-icons">elderly</span>
                <div>Kelompok sensitif hindari aktivitas panjang di luar</div>
            </div>
            <div class="recommendation-item">
                <span class="material-icons">air</span>
                <div>Batasi buka jendela saat lalu lintas padat</div>
            </div>',
            
        'Tidak Sehat Kelompok Sensitif' => '
            <div class="recommendation-item">
                <span class="material-icons">warning</span>
                <div>Pengurangan aktivitas luar ruangan untuk anak-anak dan lansia</div>
            </div>
            <div class="recommendation-item">
                <span class="material-icons">air</span>
                <div>Gunakan pemurni udara ruangan</div>
            </div>',
            
        'Tidak Sehat' => '
            <div class="recommendation-item">
                <span class="material-icons">masks</span>
                <div>Wajib pakai masker N95 di luar</div>
            </div>
            <div class="recommendation-item">
                <span class="material-icons">air</span>
                <div>Gunakan air purifier dengan HEPA filter</div>
            </div>'
    ];

    return $defaults[$statusKey] ?? '<div class="recommendation-item">
        <span class="material-icons">error</span>
        <div>Rekomendasi sementara tidak tersedia</div>
    </div>';
}
    private function getHourlyData()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("SELECT tgl, aqi_udara AS aqi, conc_pm25 AS pm25, conc_co AS co, conc_no2 AS no2, conc_o3 AS o3
            FROM histori_Aqi
            WHERE tgl >= NOW() - INTERVAL 24 HOUR
            ORDER BY tgl ASC");

        return $query->getResult();
    }

    private function getFallbackData()
    {
        return [
            'aqi' => [
                'label'  => '–',
                'status' => 'unknown',
                'value'  => 0
            ],
            'fuzzy_results' => [],
            'latest_data'   => [
                'id'        => 0,
                'mq131'     => 0,
                'mq135'     => 0,
                'mq7'       => 0,
                'sharp'     => 0,
                'timestamp' => date('c')
            ],
            'overall_aqi' => [
                'confidence' => 0,
                'details'    => [],
                'status'     => 'unknown'
            ],
            'timestamp' => date('c')
        ];
    }

    private function getSensorStatus(array $fuzzyData)
    {
        if (empty($fuzzyData)) return 'unknown';
        
        arsort($fuzzyData);
        return key($fuzzyData);
    }

    private function getAqiStatusDescription(int $aqiValue)
{
    $statusMap = [
        50  => ['Baik', 'Kualitas udara baik'],
        100 => ['Sedang', 'Dampak terbatas'],
        150 => ['Tidak Sehat Kelompok Sensitif', 'Berisiko untuk kelompok sensitif'],
        200 => ['Tidak Sehat', 'Berisiko untuk semua'],
        300 => ['Sangat Tidak Sehat', 'Berbahaya untuk populasi umum'],
        PHP_INT_MAX => ['Berbahaya', 'Darurat kesehatan']
    ];

    foreach ($statusMap as $threshold => $status) {
        if ($aqiValue <= $threshold) {
            return [
                'label' => $status[0],
                'desc'  => $status[1]
            ];
        }
    }
    
    // Fallback jika tidak ada yang cocok
    return [
        'label' => 'Tidak Diketahui',
        'desc'  => 'Data kualitas udara tidak tersedia'
    ];
}

    public function Login(): string
    {
        return view('Login.php');
    }

    public function settings(): string
    {
        return view('settings.php');
    }

    public function analytics()
    {
        $db = \Config\Database::connect();

        $current_data = $db->query("SELECT timestamp, sharp, mq7, mq135, mq131
             FROM data_udara
             WHERE DATE(timestamp) = CURDATE()
             ORDER BY timestamp DESC
             LIMIT 30")->getResult();

        $history = $db->query("SELECT tgl, ispu_pm25, ispu_co, ispu_no2, ispu_udara
             FROM histori
             ORDER BY tgl DESC
             LIMIT 30")->getResult();
 $history2 = $db->query("SELECT tgl, aqi_udara, conc_pm25, conc_co, conc_no2, conc_o3, aqi_status, health_recommendation
 FROM histori_Aqi
 ORDER BY tgl DESC
 LIMIT 25")->getResult();

        return view('analytics', [
            'current_data' => $current_data,
            'history'      => array_reverse($history),
            'history2'      => array_reverse($history2),
        ]);
    }

    public function storeSensorData()
    {
        // Get raw input data and log it
        $raw_input = file_get_contents('php://input');
        log_message('debug', 'Raw sensor data received: ' . $raw_input);

        // Allow both GET and POST for testing
        $method = $this->request->getMethod();
        $sensorData = [];
        
        if ($method === 'POST') {
            $contentType = $this->request->getHeaderLine('Content-Type');
            if (strpos($contentType, 'application/json') !== false) {
                $sensorData = json_decode($raw_input, true);
            } else {
                $sensorData = [
                    'mq7'   => (float)$this->request->getPost('mq7'),
                    'mq135' => (float)$this->request->getPost('mq135'),
                    'sharp' => (float)$this->request->getPost('sharp'),
                    'mq131' => (float)$this->request->getPost('mq131')
                ];
            }
        } else {
            $sensorData = [
                'mq7'   => (float)$this->request->getGet('mq7'),
                'mq135' => (float)$this->request->getGet('mq135'),
                'sharp' => (float)$this->request->getGet('sharp'),
                'mq131' => (float)$this->request->getGet('mq131')
            ];
        }

        log_message('debug', 'Processed sensor data: ' . json_encode($sensorData));

        foreach ($sensorData as $key => $value) {
            if ($value === null || !is_numeric($value)) {
                log_message('error', "Invalid $key value: " . var_export($value, true));
                return $this->response->setJSON([
                    'error'  => "Invalid $key value",
                    'status' => 'error'
                ]);
            }
        }

        $db = \Config\Database::connect();
        
        try {
            $result = $db->query(
                "INSERT INTO data_udara(timestamp, mq7, mq135, sharp, mq131) 
                 VALUES(NOW(), ?, ?, ?, ?)",
                [
                    $sensorData['mq7'],
                    $sensorData['mq135'],
                    $sensorData['sharp'],
                    $sensorData['mq131']
                ]
            );

            if ($result && $db->affectedRows() > 0) {
                // Pass array directly, not stdClass
                $aqi_data = $this->calculateIspuValues($sensorData);
                $this->saveIspuHistory($aqi_data);

                return $this->response->setJSON([
                    'status'       => 'OK',
                    'message'      => 'Data stored successfully',
                    'insert_id'    => $db->insertID(),
                    'ispu_values'  => $aqi_data,
                    'stored_data'  => $sensorData
                ]);
            } else {
                log_message('error', 'Database insertion failed');
                return $this->response->setJSON([
                    'status' => 'error',
                    'error'  => 'Database insertion failed'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Database error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'error'  => $e->getMessage()
            ]);
        }
    }

    private function calculateIspuValues(array $sensorData): array
    {
        return [
            'ispu_pm25' => $this->calculateIspu($sensorData['sharp']),
            'ispu_co'   => $this->calculateIspu($sensorData['mq7']),
            'ispu_no2'  => $this->calculateIspu($sensorData['mq135'])
        ];
    }

    private function calculateIspu($value)
    {
        if ($value <= 50) return $value;
        if ($value <= 150) return 50 + (($value - 50) * 49) / 100;
        return 99 + (($value - 150) * 50) / 100;
    }

    private function saveIspuHistory(array $data)
    {
        $avg = ($data['ispu_pm25'] + $data['ispu_co'] + $data['ispu_no2']) / 3;
        return \Config\Database::connect()->query(
            "INSERT INTO histori(tgl, ispu_pm25, ispu_co, ispu_no2, ispu_udara) 
             VALUES(NOW(), ?, ?, ?, ?)",
            [
                $data['ispu_pm25'],
                $data['ispu_co'],
                $data['ispu_no2'],
                round($avg, 2),
            ]
        );
    }

    public function getLatestData()
    {
        $client = \Config\Services::curlrequest();
        try {
            $res = $client->get('https://udara.unis.ac.id/api/');
            $json = json_decode($res->getBody(), true);
    
            if (!isset($json['status']) || $json['status'] !== 'success') {
                throw new \Exception('API returned error or non-success status');
            }
            $apiData = $json['data'];
        } catch (\Exception $e) {
            $apiData = [
                'sharp' => '0',
                'mq7'   => '0',
                'mq135' => '0',
                'mq131' => '0'
            ];
        }
    
        return $this->response->setJSON($apiData);
    }
    public function getChartData()
    {
        $minutes = $this->request->getGet('minutes') ?? 30;
        
        $db = \Config\Database::connect();
        $query = $db->query("SELECT 
            tgl,
            aqi_udara AS aqi,
            DATE_FORMAT(tgl, '%H:%i') AS time
            FROM histori_Aqi
            WHERE tgl >= NOW() - INTERVAL ? MINUTE
            ORDER BY tgl ASC", [$minutes]);
    
        $data = $query->getResult();
        
        return $this->response->setJSON($data);
    }
    private function get_full_aqi_status(int $aqi)
    {
        if ($aqi <= 50) {
            return [
                'label' => 'Baik',
                'desc'  => 'Kualitas udara baik dan tidak berisiko bagi kesehatan.'
            ];
        } elseif ($aqi <= 100) {
            return [
                'label' => 'Sedang',
                'desc'  => 'Beberapa individu yang sangat sensitif mungkin mengalami gejala pernapasan ringan.'
            ];
        } elseif ($aqi <= 150) {
            return [
                'label' => 'Tidak sehat bagi kelompok sensitif',
                'desc'  => 'Kelompok sensitif harus mengurangi aktivitas luar ruangan.'
            ];
        } elseif ($aqi <= 200) {
            return [
                'label' => 'Tidak sehat',
                'desc'  => 'Kemungkinan peningkatan efek buruk bagi semua orang.'
            ];
        } elseif ($aqi <= 300) {
            return [
                'label' => 'Sangat tidak sehat',
                'desc'  => 'Dampak kesehatan serius dapat dialami oleh publik umum.'
            ];
        } else {
            return [
                'label' => 'Berbahaya',
                'desc'  => 'Risiko kesehatan serius. Hindari aktivitas luar ruangan.'
            ];
        }
    }

    
}
