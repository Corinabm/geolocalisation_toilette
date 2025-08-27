<?php

namespace App\Models;

use App\Models\Toilette;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Localisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'adresse',
        'latitude',
        'longitude',
        'ville',
        'code_postal',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function toilette(): HasOne
    {
        return $this->hasOne(Toilette::class);
    }
}
