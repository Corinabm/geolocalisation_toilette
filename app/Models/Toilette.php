<?php

namespace App\Models;

use App\Models\Localisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Toilette extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'nom',
        'description',
        'horaires',
        'etat',
        'localisation_id',
    ];


    public function localisation(): BelongsTo
    {
        return $this->belongsTo(Localisation::class);
    }
}
