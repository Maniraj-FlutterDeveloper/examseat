<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'data',
        'file_path',
        'file_type',
        'generated_at',
        'status',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the report that owns the result.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Scope a query to only include successful results.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed results.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if the result was successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    /**
     * Check if the result has a file.
     *
     * @return bool
     */
    public function hasFile()
    {
        return !empty($this->file_path);
    }

    /**
     * Get the file URL.
     *
     * @return string|null
     */
    public function getFileUrl()
    {
        if ($this->hasFile()) {
            return asset('storage/' . $this->file_path);
        }

        return null;
    }

    /**
     * Get the file extension.
     *
     * @return string|null
     */
    public function getFileExtension()
    {
        if ($this->hasFile()) {
            return pathinfo($this->file_path, PATHINFO_EXTENSION);
        }

        return null;
    }

    /**
     * Get the file size in a human-readable format.
     *
     * @return string|null
     */
    public function getFileSize()
    {
        if ($this->hasFile() && file_exists(storage_path('app/public/' . $this->file_path))) {
            $bytes = filesize(storage_path('app/public/' . $this->file_path));
            $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
            
            for ($i = 0; $bytes > 1024; $i++) {
                $bytes /= 1024;
            }
            
            return round($bytes, 2) . ' ' . $units[$i];
        }

        return null;
    }
}

