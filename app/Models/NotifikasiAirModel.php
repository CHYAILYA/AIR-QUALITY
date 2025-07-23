<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiAirModel extends Model
{
    protected $table = 'NotifikasiAir';
    protected $primaryKey = 'id';
    protected $allowedFields = ['DS18B20', 'TDS_SENSOR_PIN', 'PH_SENSOR_PIN', 'timestamp'];
    protected $useTimestamps = false;
}
