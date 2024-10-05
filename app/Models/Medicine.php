<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Medicine extends Model
{
    use HasFactory,Searchable,softDeletes;

    public function scopeFilterExpiredAndTrashed(Builder $query, $request)
    {

        if (Auth::user()->hasRole('admin')) {
            if ($request->has('expired')) {
                $query->where('expires_at', '<', now());
            } else {
                $query->where('expires_at', '>', now());
            }

            if ($request->has('trashed')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }
        }

    }

    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::addGlobalScope('global', function (Builder $builder) {
            if (! Auth::user()->hasRole('admin')) {
                $builder->withoutTrashed()->where('expires_at', '>', now());
            }
        });
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray()
    {
        return [
            'scientific_name' => $this->scientific_name,
            'trade_name' => $this->trade_name,
            'type' => $this->type,
            // You can include other fields as necessary
        ];
    }
}
