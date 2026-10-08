<?php

namespace Progmix\Locations\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Models\User;
use Illuminate\Support\Facades\Lang;
use Juzaweb\CMS\Traits\LocalizationTrait;
use Illuminate\Database\Eloquent\Builder;

class State extends Model
{
    use LocalizationTrait;
    public $timestamps = false;
    // protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'country_id','iso_code','active'];
    protected $localizationFields = ['name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function getNameAttribute($value)
    {
        $names = json_decode($value, true);
        return $names[Lang::locale()] ??  $names["en"] ?? '';
    }
    public function getFieldName(): string
    {
        return 'name';
    }
    public function scopeActive(Builder $query)
    {
        return $query->where('active', 1);
    }

}
