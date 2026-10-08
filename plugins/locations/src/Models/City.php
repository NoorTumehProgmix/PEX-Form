<?php

namespace Progmix\Locations\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\LocalizationTrait;
use Juzaweb\Ecommerce\Models\Shipping;
use Juzaweb\Ecommerce\Models\ShippingRate;

class City extends Model
{
    use LocalizationTrait;
    public $timestamps = false;
    // protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'state_id'];
    protected $localizationFields = ['name'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function shippingRates()
    {
        return $this->belongsToMany(ShippingRate::class);
    }

    public function getFieldName(): string
    {
        return 'name';
    }
}
