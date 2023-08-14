<?php

namespace App\Models\Project;

use App\Models\Tariff\TariffProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property int $tariff_id
 * @property string $created_at
 * @property int $user_id
 * @property string $title
 *  */
class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tariff_id',
        'title'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tariff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TariffProject::class, 'tariff_id', 'id');
    }
}
