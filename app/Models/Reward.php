<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    /** @use HasFactory<\Database\Factories\RewardFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'points',
        'logo_file_id',
    ];

    public function logoFile()
    {
        return $this->belongsTo(UploadedFile::class, 'logo_file_id');
    }
}
