<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_serial_number',
        'device_name',
        'device_type',
        'user_id',
        'farm_id',
        'installation_location',
        'farm_upi',
        'sensor_types',
        'latitude',
        'longitude',
        'notes',
        'firmware_version',
        'status',
        'battery_level',
        'assigned_by',
        'installed_at',
        'last_reading_at',
        'last_maintenance_at'
    ];

    protected $casts = [
        'sensor_types' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'battery_level' => 'integer',
        'installed_at' => 'datetime',
        'last_reading_at' => 'datetime',
        'last_maintenance_at' => 'datetime'
    ];

    /**
     * Get the user that owns the device
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who assigned this device
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the farm that the device is installed in
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the soil data readings from this device
     */
    public function soilData()
    {
        return $this->hasMany(SoilData::class);
    }

    /**
     * Get the latest soil data reading
     */
    public function latestReading()
    {
        return $this->hasOne(SoilData::class)->latestOfMany();
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'active': return 'success';
            case 'inactive': return 'warning';
            case 'maintenance': return 'info';
            case 'offline': return 'danger';
            default: return 'secondary';
        }
    }

    /**
     * Get battery level status
     */
    public function getBatteryStatusAttribute()
    {
        if (!$this->battery_level) return 'unknown';

        if ($this->battery_level >= 80) return 'excellent';
        if ($this->battery_level >= 50) return 'good';
        if ($this->battery_level >= 20) return 'low';
        return 'critical';
    }

    /**
     * Get battery color based on level
     */
    public function getBatteryColorAttribute()
    {
        if (!$this->battery_level) return 'secondary';

        if ($this->battery_level >= 80) return 'success';
        if ($this->battery_level >= 50) return 'info';
        if ($this->battery_level >= 20) return 'warning';
        return 'danger';
    }

    /**
     * Check if device is online
     */
    public function isOnline()
    {
        return $this->status === 'active' &&
               $this->last_reading_at &&
               $this->last_reading_at->diffInMinutes(now()) <= 60;
    }

    /**
     * Get formatted sensor types
     */
    public function getFormattedSensorTypesAttribute()
    {
        if (!$this->sensor_types || !is_array($this->sensor_types)) {
            return 'No sensors';
        }

        return implode(', ', $this->sensor_types);
    }

    /**
     * Scope for active devices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for devices by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for devices by farm
     */
    public function scopeByFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }
}
