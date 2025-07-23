<?php namespace App\Controllers;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SensorReading;
use CodeIgniter\API\ResponseTrait;
use DateTime;
use DateTimeZone;
use Config\Services; // Penting: Import Services untuk mengakses cache
use App\Models\NotifikasiAirModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\ResponseModel; // Import model ResponseModel
class AuthController extends BaseController
{
    use ResponseTrait;

    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->responseModel = new ResponseModel();
    }

    public function showLoginForm()
    {
        $data = [
            'title' => 'Login Page',
            'validation' => $this->validation
        ];
        return view('Login', $data);
    }

   public function login()
    {
        $isRegister = $this->request->getPost('reg_name') && $this->request->getPost('reg_email');
        $model = new UserModel();

        if ($isRegister) {
            // REGISTER TANPA VALIDASI RULES
            $data = [
                'name' => $this->request->getPost('reg_name'),
                'email' => strtolower($this->request->getPost('reg_email')),
                'password' => $this->request->getPost('reg_password'), // SIMPAN LANGSUNG TANPA HASH
                'role' => 3,
                'is_active' => 1
            ];

            if ($model->save($data)) {
                return redirect()->to('/Login')->with('success', 'Pendaftaran berhasil! Silakan login');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal melakukan pendaftaran');
        } else {
            // LOGIN TANPA HASH
            $email = strtolower(trim($this->request->getPost('email')));
            $passwordInput = trim($this->request->getPost('password'));
            $user = $model->where('LOWER(email)', $email)->first();

            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Akun tidak ditemukan.');
            }

            if ($user['password'] !== $passwordInput) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Email atau password salah.');
            }

            if ($user['is_active'] != 1) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Akun belum diaktivasi.');
            }

            // Set session
            $this->session->regenerate(true);
            $this->session->set([
                'isLoggedIn' => true,
                'userData' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);

            // Redirect sesuai role
            switch ($user['role']) {
                case 1:
                    return redirect()->to('/admin/dashboard')->with('success', 'Selamat datang Admin!');
                case 2:
                    return redirect()->to('/admin/dashboard/chat/')->with('success', 'Selamat datang!');
                case 3:
                    return redirect()->to('/home/air')->with('success', 'Selamat datang!');
                default:
                    $this->session->destroy();
                    return redirect()->to('/auth/blocked')->with('error', 'Akses ditolak: Peran tidak valid.');
            }
        }
    }
public function viewLogs()
{
    // Create an instance of the ResponseModel to fetch logs
    $logModel = new ResponseModel();

    // Fetch the last 10 logs (you can adjust the limit as needed)
    $logs = $logModel->orderBy('timestamp', 'DESC')->findAll(10);

    // Pass the logs to the view
    $data = [
        'title' => 'Admin Login Logs',
        'logs'  => $logs
    ];

    return view('admin/view_logs', $data);
}
    public function dashboard()
    {
        // Cek session dan role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/Login');
        }
        
        $userData = session()->get('userData');
        $role = $userData['role'] ?? 0;

        // Logic redirect berdasarkan role
        switch($role) {
            case 1: // Admin
                return view('admin/Dasboard'); // Perhatikan penulisan case-sensitive
            case 2: // User biasa
                return redirect()->to('/user/dashboard');
            default: // Role tidak valid
                session()->destroy();
                return redirect()->to('/Login')->with('error', 'Akses ditolak');
        }
    }
public function chatui()
    {
        // Cek session dan role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/Login');
        }
        
        $userData = session()->get('userData');
        $role = $userData['role'] ?? 0;

        // Logic redirect berdasarkan role
        switch($role) {
            case 1: // Admin
                    return redirect()->to('/user/dashboard');
                
            case 2: // User biasa
            return view('admin/Chat'); // Perhatikan penulisan case-sensitive
            default: // Role tidak valid
                session()->destroy();
                return redirect()->to('/Login')->with('error', 'Akses ditolak');
        }
    }
//      public function chatui()
// {
//     // Cek session dan apakah pengguna sudah login
//     if (!session()->get('isLoggedIn')) {
//         return redirect()->to('/Login')->with('error', 'Silakan login untuk mengakses halaman ini.');
//     }
    
//     $userData = session()->get('userData');
//     $role = $userData['role'] ?? 0; // Pastikan 'role' ada di userData, default 0 jika tidak ada

//     // Izinkan role 1 (Admin) DAN role 2 (User yang ingin akses Chat UI)
//     if ($role === 1 || $role === 2) {
//         // Berdasarkan screenshot, nama file view Anda adalah 'Chat.php'
//         // dan terletak langsung di dalam folder 'admin'.
//         // Jadi, path yang benar adalah 'admin/Chat'.
//         return view('admin/Chat');
//     } else {
//         // Jika role bukan 1 atau 2, atau role tidak valid
//         session()->destroy(); // Hapus session untuk keamanan
//         return redirect()->to('/Login')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
//     }
// }

    public function showRegisterForm()
    {
        $data = [
            'title' => 'Registration Page',
            'validation' => $this->validation
        ];
        return view('auth/register', $data);
    }

    public function register()
    {
        // Validation Rules
        $rules = [
            'name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required' => '{field} wajib diisi',
                    'max_length' => '{field} maksimal 100 karakter'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[users.email]|max_length[100]',
                'errors' => [
                    'is_unique' => '{field} sudah terdaftar',
                    // ... error lainnya
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|max_length[100]',
                // ... error messages
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]',
                // ... error messages
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validation->getErrors());
        }

        // Save to Database
        $model = new UserModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => strtolower($this->request->getPost('email')),
            'password' => $this->request->getPost('password'),
            'role' => 2, // Default role user
            'is_active' => 1 // Aktifkan langsung (bisa diubah ke 0 jika perlu verifikasi email)
        ];

        if ($model->save($data)) {
            return redirect()->to('/login')->with('success', 'Pendaftaran berhasil! Silakan login');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal melakukan pendaftaran');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah logout');
    }

    public function blocked()
    {
        return view('auth/blocked', ['title' => 'Akses Diblokir']);
    }
    public function fetchAndSend(): ResponseInterface
    {
        // 🚀 API DATA FETCHING
        $api_url = 'https://udara.unis.ac.id/api/';

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($api_url);
            $data = json_decode($response->getBody(), true);

            // Jika status error atau data tidak lengkap, balas error 200 dengan pesan jelas
            if (
                $response->getStatusCode() !== 200 ||
                !isset($data['status']) ||
                $data['status'] !== 'success' ||
                !isset($data['data']['aqi']) ||
                !isset($data['data']['latest_data']['timestamp'])
            ) {
                // Jika ada pesan error dari API, tampilkan ke user
                $errorMsg = $data['message'] ?? '❌ Gagal mengambil data dari API atau data tidak lengkap';
                $errorCode = $data['error_code'] ?? null;

                return $this->response
                    ->setStatusCode(200)
                    ->setJSON([
                        'status' => 'error',
                        'message' => $errorMsg,
                        'error_code' => $errorCode,
                        'api_response' => $data
                    ]);
            }
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(502)
                ->setJSON([
                    'status' => 'error',
                    'message' => '❌ Gagal mengambil data dari API: ' . $e->getMessage()
                ]);
        }

        // ⏰ TIME CONVERSION
        try {
            $aqi = $data['data']['aqi'];
            $timestamp_gmt = $data['data']['latest_data']['timestamp'];
            $date = new \DateTime($timestamp_gmt, new \DateTimeZone('GMT'));
            $date->modify('+7 hours');
            $timestamp_wib = $date->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status' => 'error',
                    'message' => '⏰ Format waktu tidak valid: ' . $e->getMessage()
                ]);
        }

        // 📊 AIR QUALITY ANALYSIS
        $aqiValue = $aqi['value'] ?? 0;
        $aqiStatus = match (true) {
            $aqiValue <= 50 => 'BAIK 🌿',
            $aqiValue <= 100 => 'SEDANG 🌤️',
            $aqiValue <= 200 => 'TIDAK SEHAT 😷',
            $aqiValue <= 300 => 'SANGAT TIDAK SEHAT ⚠️',
            default => 'BERBAHAYA ☠️'
        };

        // 📩 MESSAGE FORMATTING
        $text = '🌫️ INDEKS KUALITAS UDARA 🌍' . "\n" .
                '========================' . "\n" .
                '🏷️ Label: ' . ($aqi['label'] ?? '-') . "\n" .
                '📈 Status: ' . $aqiStatus . "\n" .
                '📊 Nilai AQI: ' . ($aqi['value'] ?? '-') . "\n" .
                '🕒 Waktu Pengukuran (WIB): ' . $timestamp_wib . "\n\n" .
                'ℹ️ Skala AQI:' . "\n" .
                '0-50: Baik 🌿' . "\n" .
                '51-100: Sedang 🌤️' . "\n" .
                '101-200: Tidak Sehat 😷' . "\n" .
                '201-300: Sangat Tidak Sehat ⚠️' . "\n" .
                '300+: Berbahaya ☠️' . "\n\n" .
                '🔍 Detail: https://www.airnow.gov/aqi/aqi-basics/';

        // 📨 MESSAGE SENDING
        $postData = [
            'session' => 'default',
            'chatId' => '120363400501152692@newsletter',
            'text' => $text,
            'linkPreview' => true,
            'linkPreviewHighQuality' => true
        ];

        $send_url = 'http://103.85.60.82:3001/api/sendText?apikey=m0h4mm4d';
        $authorizedUser = 'ridwan';
        $authorizedPass = 'm0h4mm4d';

        try {
            $client = \Config\Services::curlrequest();
            $whatsappResponse = $client->post($send_url, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'auth' => [$authorizedUser, $authorizedPass],
                'json' => $postData
            ]);

            $body = json_decode($whatsappResponse->getBody(), true);

            if ($whatsappResponse->getStatusCode() === 200) {
                // 💾 AUDIT TRAIL
                $currentTime = date('Y-m-d H:i:s');
                $responseModel = new ResponseModel();
                try {
                    $responseModel->save([
                        'respon' => json_encode([
                            'status' => 'success',
                            'message' => '📤 Pesan terkirim',
                            'sent_text' => $text
                        ]),
                        'status' => 'success',
                        'tgl' => $currentTime
                    ]);
                } catch (\Exception $e) {
                    log_message('error', 'Gagal menyimpan log: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => '✅ Notifikasi berhasil dikirim!',
                    'sent_text' => $text,
                    'timestamp' => $currentTime,
                    'whatsapp_api_response' => $body
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengirim pesan ke WhatsApp API.',
                    'whatsapp_api_response' => $body,
                    'http_code' => $whatsappResponse->getStatusCode()
                ])->setStatusCode($whatsappResponse->getStatusCode());
            }
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi API WhatsApp: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }


 // New function to fetch notifications from the response table
 public function getNotifications()
    {
        // Get notifications
        $notifications = $this->responseModel->get_all_notifications();

        // Decode 'respon' field for each notification
        foreach ($notifications as &$notification) {
            $notification['respon'] = json_decode($notification['respon'], true); // Decode the JSON inside 'respon'
        }

        // Send the response as JSON
        return $this->response->setJSON($notifications);
    }
 
    public function air(): ResponseInterface
    {
        // Validasi input
        $validated = $this->validate([
            'tds'       => 'required|numeric|greater_than_equal_to[0]',
            'turbidity' => 'required|numeric|greater_than_equal_to[0]',
            'ph'        => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[14]',
            'suhu'      => 'required|numeric' // Added validation for suhu
        ]);

        // Jika validasi gagal, kirimkan respons dengan status 422
        if (!$validated) {
            return $this->response->setJSON([
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors()
            ])->setStatusCode(422); // Return 422 Unprocessable Entity
        }

        // Ambil data dari body request JSON
        $data = [
            'TDS_ppm'     => (string) $this->request->getJSON()->tds,
            'Turbidity_NTU' => (string) $this->request->getJSON()->turbidity,
            'pH'          => (string) $this->request->getJSON()->ph,
            'suhu'        => (string) $this->request->getJSON()->suhu // Added suhu
        ];

        // Debugging: log data yang diterima
        log_message('error', 'Received Data: ' . json_encode($data));

        // Buat model dan simpan data ke database
        $model = new SensorReading();
        if ($model->insert($data) === false) {
            // Jika gagal, beri respons kesalahan dengan status 500
            return $this->response->setJSON([
                'message' => 'Gagal menyimpan data',
                'errors'  => $model->errors()
            ])->setStatusCode(500); // Return 500 Internal Server Error
        }

        // Jika berhasil, kirimkan respons dengan status 201
        return $this->response->setJSON([
            'message' => 'Data berhasil disimpan',
            'data'    => $data
        ])->setStatusCode(201); // Return 201 Created
    }

public function history(): ResponseInterface
    {
        $hours = (int) $this->request->getGet('hours');

        // Validasi: hanya menerima 1–24 jam
        if ($hours < 1 || $hours > 24) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter "hours" harus berupa angka antara 1 sampai 24.'
            ])->setStatusCode(400);
        }

        $model = new SensorReading();
        $limitTime = date('Y-m-d H:i:s', strtotime("-$hours hour"));

        // Ambil data dari range waktu yang diminta, termasuk 'suhu'
        $data = $model
            ->select('id, TDS_ppm, Turbidity_NTU, pH, suhu, timestamp') // Added 'suhu' here
            ->where('timestamp >=', $limitTime)
            ->orderBy('timestamp', 'ASC')
            ->findAll();

        // Jika tidak ada data dalam rentang waktu
        if (empty($data)) {
            // Cek data lebih lama dari rentang waktu yang diminta, termasuk 'suhu'
            $latestData = $model
                ->select('id, TDS_ppm, Turbidity_NTU, pH, suhu, timestamp') // Added 'suhu' here as well
                ->where('timestamp <', $limitTime)
                ->orderBy('timestamp', 'ASC')
                ->findAll();

            // Jika ada data yang lebih lama, tampilkan data tersebut
            if (!empty($latestData)) {
                $data = $latestData;
                return $this->response->setJSON([
                    'success'       => true,
                    'hours'         => $hours,
                    'is_fallback'   => true,
                    'fallback_type' => 'no_new_data_in_range',
                    'data_count'    => count($data),
                    'data'          => $data
                ])->setStatusCode(200);
            } else {
                // Jika tidak ada data sama sekali
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang tersedia.'
                ])->setStatusCode(404);
            }
        }

        // Jika data ada dalam rentang waktu yang diminta
        return $this->response->setJSON([
            'success'       => true,
            'hours'         => $hours,
            'is_fallback'   => false,
            'data_count'    => count($data),
            'data'          => $data
        ])->setStatusCode(200);
    }

    public function Notifikasiwagrub()
    {
        $data = $this->request->getJSON();

        if (!$data) {
            return $this->fail('Invalid JSON payload', 400);
        }

        $model = new NotifikasiAirModel();

        $insertData = [
            'DS18B20'        => $data->DS18B20 ?? null,
            'TDS_SENSOR_PIN' => $data->TDS_SENSOR_PIN ?? null,
            'PH_SENSOR_PIN'  => $data->PH_SENSOR_PIN ?? null,
            'timestamp'      => date('Y-m-d H:i:s'),
        ];

        if ($model->insert($insertData)) {
            return $this->respond([
                'status'  => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $insertData
            ], 201);
        } else {
            return $this->failServerError('Gagal menyimpan data');
        }
    }


   public function kirimDataair()
    {
        // $allowed_ip = '103.85.60.82';
        // $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // if ($client_ip !== $allowed_ip) {
        //     return $this->failForbidden('Akses ditolak. IP Anda tidak diizinkan.');
        // }

        $model = new \App\Models\NotifikasiAirModel();
        $data = $model->orderBy('timestamp', 'DESC')->first();

        // Jika tidak ada data, hentikan proses
        if (empty($data)) {
            return $this->respondNoContent('Tidak ada data terbaru yang tersedia.');
        }

        // Konversi timestamp data terbaru ke WIB
        $latest_timestamp_utc = new DateTime($data['timestamp'], new DateTimeZone('UTC'));
        $latest_timestamp = $latest_timestamp_utc->setTimezone(new DateTimeZone('Asia/Jakarta'));

        // Dapatkan instance cache
        $cache = Services::cache();
        $cache_key = 'last_sent_whatsapp_timestamp_air'; // Kunci unik untuk cache

        // Dapatkan timestamp terakhir yang dikirim dari cache
        $last_sent_timestamp_str = $cache->get($cache_key);
        $last_sent_timestamp_from_cache = null;

        if ($last_sent_timestamp_str) {
            try {
                $last_sent_timestamp_from_cache = new DateTime($last_sent_timestamp_str, new DateTimeZone('Asia/Jakarta'));
            } catch (\Exception $e) {
                // Tangani error jika format timestamp di cache tidak valid
                log_message('error', 'Invalid timestamp format in cache: ' . $e->getMessage());
                $last_sent_timestamp_from_cache = null;
            }
        }

        // Bandingkan timestamp
        // Jika data terbaru TIDAK lebih baru dari yang terakhir dikirim (atau sama persis), jangan kirim
        if ($last_sent_timestamp_from_cache && $latest_timestamp <= $last_sent_timestamp_from_cache) {
            return $this->respond('Tidak ada data baru untuk dikirim.', 200);
        }

        // --- Lanjutkan dengan logika pengolahan data dan pengiriman jika ada data baru ---
        $tds = floatval($data['TDS_SENSOR_PIN']);
        $suhu = floatval($data['DS18B20']);
        $ph = floatval($data['PH_SENSOR_PIN']);

        // Evaluasi TDS
        if ($tds < 100) {
            $kategori_tds = "Baik";
        } elseif ($tds <= 300) {
            $kategori_tds = "Sedang";
        } else {
            $kategori_tds = "Tidak Baik";
        }

        // Evaluasi Suhu
        $kategori_suhu = ($suhu >= 25 && $suhu <= 35) ? "Baik" : "Tidak Baik";

        // Evaluasi pH dengan kategori lebih detail
        if ($ph < 4.5) {
            $kategori_ph = "Sangat Asam";
        } elseif ($ph < 5.0) {
            $kategori_ph = "Asam";
        } elseif ($ph <= 8.5) {
            $kategori_ph = "Baik";
        } elseif ($ph <= 9.5) {
            $kategori_ph = "Basa";
        } else {
            $kategori_ph = "Sangat Basa";
        }

        // Penilaian status pH (hanya "Baik" dianggap Baik, lainnya Tidak Baik)
        $kategori_ph_status = ($kategori_ph === "Baik") ? "Baik" : "Tidak Baik";

        // Penilaian Umum
        $kategori_umum = ($kategori_tds === "Tidak Baik" || $kategori_suhu === "Tidak Baik" || $kategori_ph_status === "Tidak Baik")
            ? "Tidak Baik" : (($kategori_tds === "Sedang") ? "Sedang" : "Baik");

        // Format waktu untuk WhatsApp
        $waktu_wib = $latest_timestamp->format('Y-m-d H:i:s');

        // Format untuk WhatsApp
        $formattedText = "📡 *Data Sensor Air Terbaru:*\n\n"
            . "🌡️ Suhu: {$suhu} °C ({$kategori_suhu})\n"
            . "💧 TDS: {$tds} ppm ({$kategori_tds})\n"
            . "🧪 pH: {$ph} ({$kategori_ph})\n"
            . "🕒 Waktu: {$waktu_wib} WIB\n\n"
            . "📊 *Kategori Umum:* *{$kategori_umum}*";

        // Payload ke API WhatsApp
        $payload = [
            "chatId" => "120363418510991359@g.us",
            "reply_to" => null,
            "text" => $formattedText,
            "linkPreview" => false,
            "linkPreviewHighQuality" => false,
            "session" => "default"
        ];

        $payload_json = json_encode($payload);

        $authorizedUser = 'ridwan';
        $authorizedPass = 'm0h4mm4d';

        $ch = curl_init('http://103.85.60.82:3001/api/sendText?apikey=m0h4mm4d');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_USERPWD, "$authorizedUser:$authorizedPass");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            // Simpan timestamp terbaru ke cache setelah berhasil dikirim
            $cache->save($cache_key, $latest_timestamp->format('Y-m-d H:i:s'), 60 * 60 * 24 * 30); // Simpan selama 30 hari

            return $this->respond([
                'status' => 'success',
                'message' => 'Data berhasil dikirim ke API',
                'response_api' => json_decode($response)
            ]);
        } else {
            return $this->failServerError('Gagal mengirim data ke API, HTTP code: ' . $httpCode);
        }
    }



 public function sendWaterQualityUpdate(): ResponseInterface
    {
        // 1. Ambil data dari API kualitas air
        $airQualityApiUrl = 'https://udara.unis.ac.id/air/';
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($airQualityApiUrl);
            $data = json_decode($response->getBody(), true);

            if ($response->getStatusCode() !== 200 || !isset($data['data'][0])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengambil data kualitas air dari API.',
                    'api_response' => $data
                ])->setStatusCode(500);
            }

            $waterData = $data['data'][0];

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi API kualitas air: ' . $e->getMessage()
            ])->setStatusCode(500);
        }

        // 2. Format teks untuk pesan WhatsApp
        $formattedText = "Monitoring Kualitas Air Terkini:\n\n" .
                         "💧 TDS: " . $waterData['TDS_ppm'] . " ppm\n" .
                         "🌫️ Kekeruhan: " . $waterData['Turbidity_NTU'] . " NTU\n" .
                         "⚖️ pH: " . $waterData['pH'] . "\n" .
                         "🌡️ Suhu: " . $waterData['suhu'] . " °C\n" .
                         "✨ Skor Kualitas: " . $waterData['quality_score'] . "\n" .
                         "✅ Kategori: *" . $waterData['kategori'] . "*\n\n" .
                         "Detail Fuzzy:\n" .
                         "  TDS Baik: " . $waterData['TDS_baik'] . ", Buruk: " . $waterData['TDS_buruk'] . "\n" .
                         "  Kekeruhan Baik: " . $waterData['Turbidity_baik'] . ", Buruk: " . $waterData['Turbidity_buruk'] . "\n" .
                         "  pH Ideal: " . $waterData['pH_ideal'] . ", Asam: " . $waterData['pH_asam'] . ", Basa: " . $waterData['pH_basa'] . "\n\n" .
                         "*(Pesan otomatis dari sistem monitoring air)*";

        // 3. Siapkan payload untuk API WhatsApp
        $whatsappApiUrl = 'http://103.85.60.82:3001/api/sendText?apikey=m0h4mm4d';
        $chatId = '120363417237285852@newsletter';
        $authorizedUser = 'ridwan';
        $authorizedPass = 'm0h4mm4d';

        $payload = [
            "chatId" => $chatId,
            "reply_to" => null,
            "text" => $formattedText,
            "linkPreview" => false,
            "linkPreviewHighQuality" => false,
            "session" => "default"
        ];

        try {
            $client = \Config\Services::curlrequest();
            $whatsappResponse = $client->post($whatsappApiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'auth' => [$authorizedUser, $authorizedPass],
                'json' => $payload
            ]);

            $body = json_decode($whatsappResponse->getBody(), true);

            if ($whatsappResponse->getStatusCode() === 200) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pesan kualitas air berhasil dikirim ke WhatsApp.',
                    'whatsapp_api_response' => $body
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal mengirim pesan ke WhatsApp API.',
                    'whatsapp_api_response' => $body,
                    'http_code' => $whatsappResponse->getStatusCode()
                ])->setStatusCode($whatsappResponse->getStatusCode());
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi API WhatsApp: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }



}