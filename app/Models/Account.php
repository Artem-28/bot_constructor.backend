<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Account
 *
 * @property int $id
 * @property int $tariff_id
 * @property string $title
 * @property string $description
 *  */
class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected $with = [
        'accountTypes',
    ];

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    // Учителя добавленные к аккаунту
    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teachers');
    }

    public function accountTypes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(AccountType::class, 'account_account_types','account_id', 'type', 'id', 'code');
    }
}
