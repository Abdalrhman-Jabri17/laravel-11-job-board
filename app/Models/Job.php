<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'location',
        'salary',
        'description',
        'experience',
        'category'
    ];
    public static array $experience = ['entry', 'intermediate', 'senior'];
    public static array $category = ['IT', 'Finance', 'Sales', 'Marketing'];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
    public function hasAppliedUser(Authenticatable|User|int $user): bool
    {
        return $this->where('id', $this->id)
            ->whereHas(
                'jobApplications',
                fn($query) =>
                $query->where('user_id', '=', $user->id ?? $user)
            )->exists();
    }
    public function scopeFilters(Builder|QueryBuilder $query, array $filters): Builder|QueryBuilder
    {
        return $query->when($filters['search'] ?? '', function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('employer', function ($query) use ($search) {
                        $query->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        })->when($filters['min_salary'] ?? '', function ($query, $minSalary) {
            $query->where('salary', '>=' ?? '', $minSalary);
        })->when($filters['max_salary'] ?? '', function ($query, $maxSalary) {
            $query->where('salary', '<=', $maxSalary);
        })->when($filters['experience'] ?? '', function ($query, $experience) {
            $query->where('experience', $experience);
        })->when($filters['category'] ?? '', function ($query, $category) {
            $query->where('category', $category);
        });
    }
}
