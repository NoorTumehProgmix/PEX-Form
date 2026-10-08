<?php

namespace Progmix\Locations\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Models\User;
use Illuminate\Support\Facades\Lang;
use Juzaweb\CMS\Traits\LocalizationTrait;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
    use LocalizationTrait;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'code', 'phonecode','has_states','active'];
    protected $localizationFields = ['name'];



    public function states()
    {
        return $this->hasMany(State::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function getFieldName(): string
    {
        return 'name';
    }

    public function getNameAttribute($value)
    {
        $names = json_decode($value, true);
        return $names[Lang::locale()] ??  $names["en"] ?? '';
    }
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }
}
