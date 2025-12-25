<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    protected $fillable = [
        'name',
        'size',
        'mime',
        'extension',
        'file_path',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function reward()
    {
        return $this->hasOne(Reward::class, 'logo_file_id');
    }
}
