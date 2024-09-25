<?php

namespace App\Models;

use http\Env\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use Spatie\Permission\Models\Role;

class Medicine extends Model
{
    use HasFactory,Searchable,softDeletes;

    protected $guarded=['id'];

    protected static function boot()
    {
        parent::boot();


//        static::creating(function ($medicine) {
//            if (!Auth::user() || !Auth::user()->hasRole('admin')) {
//                throw new AuthorizationException('Unauthorized');
//            }
//        });
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
