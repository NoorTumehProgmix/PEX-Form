<?php

namespace Progmix\PaymentMethods\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;


/**
 * Progmix\PaymentMethods\Models\PaymentMethod
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property array|null $data
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereFilter($params = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @property int|null $site_id
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereSiteId($value)
 * @mixin \Eloquent
 */
class PaymentMethod extends Model
{
    use ResourceModel;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $table = 'payment_methods';
    protected string $fieldName = 'name';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public static function getPaymentMethods(): array
    {
        return [
            'cod'    => trans('paymentMethods::content.data.payment_methods.cod'),
            'paypal' => trans('paymentMethods::content.data.payment_methods.paypal'),
            'arabBank' => trans('paymentMethods::content.data.payment_methods.arabBank'),
        ];
    }
//    public function payment_transactions(): HasMany
//    {
//        return $this->hasMany(
//            PaymentTransaction::class,
//            'payment_method_id',
//            'id'
//        );
//    }

    public function scopeActive($builder)
    {
        return $builder->where('active', '=', self::STATUS_ACTIVE);
    }
}
