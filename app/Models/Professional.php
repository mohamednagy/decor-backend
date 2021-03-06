<?php

namespace App\Models;

use App\Modules\SearchEngine\ProfessionalNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Professional extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'uid',
        'slug',
        'company_name',
        'about',
        'services',
        'logo',
        'phone1',
        'phone2',
        'lat_lng',
        'full_address',
        'work_scope',
        'social'
    ];

    protected $casts = [
        'social' => 'array'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'professional_categories');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professional_users');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProfessionalReview::class);
    }

    public function toSearchableArray(): array
    {
        return ProfessionalNormalizer::toSearchableArray($this);
    }

    public function searchableAs(): string
    {
        return 'professionals_index';
    }
}
