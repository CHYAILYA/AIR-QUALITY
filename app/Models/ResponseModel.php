<?php 
namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table = 'responses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['respon', 'status', 'tgl'];
    protected $useTimestamps = false;
    
    // Validasi data
    protected $validationRules = [
        'respon' => 'required',
        'status' => 'required|max_length[50]',
        'tgl' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'respon' => [
            'required' => 'Field respon wajib diisi'
        ],
        'status' => [
            'required' => 'Field status wajib diisi',
            'max_length' => 'Status maksimal 50 karakter'
        ],
        'tgl' => [
            'required' => 'Tanggal wajib diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];
    public function get_all_notifications()
    {
        return $this->findAll(); // Fetch all notifications from the table
    }
    // Method untuk log response
    public function logResponse(array $data): bool
    {
        try {
            if (!$this->validate($data)) {
                log_message('error', 'Validation Error: ' . json_encode($this->errors()));
                return false;
            }
            
            return $this->insert($data) !== false;
        } catch (\Exception $e) {
            log_message('error', 'Database Error: ' . $e->getMessage());
            return false;
        }
    }

    // Method untuk mendapatkan notifikasi dengan pagination
    public function getPaginatedNotifications(int $perPage = 10)
    {
        return $this->orderBy('tgl', 'DESC')
                   ->paginate($perPage);
    }

    // Method untuk notifikasi terbaru (24 jam terakhir)
    public function getRecentNotifications()
    {
        return $this->where('tgl >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                   ->orderBy('tgl', 'DESC')
                   ->findAll();
    }

    // Method untuk membersihkan log lama (older than 30 days)
    public function deleteOldNotifications(): int
    {
        return $this->where('tgl <', date('Y-m-d H:i:s', strtotime('-30 days')))
                   ->delete();
    }
}