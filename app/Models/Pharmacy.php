<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pharmacy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function path()
    {
        return "/pharmacies/{$this->id}";
    }

    public function availabilities() : HasMany
    {
        return $this->hasMany(Availability::class);
    }
}
