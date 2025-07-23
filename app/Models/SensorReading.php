<?php

namespace App\Models;

use CodeIgniter\Model;

class SensorReading extends Model
{
    protected $table = 'air';
    protected $primaryKey = 'idPrimary';
    protected $allowedFields = ['TDS_ppm', 'Turbidity_NTU', 'pH'];
    public $useTimestamps = false;
}
class NotifikasiAirModel extends Model
{
    protected $table = 'NotifikasiAir';
    protected $primaryKey = 'id';
    protected $allowedFields = ['DS18B20', 'TDS_SENSOR_PIN', 'PH_SENSOR_PIN', 'timestamp'];
    protected $useTimestamps = false;
}