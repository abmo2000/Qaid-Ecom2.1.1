<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $guarded = ['id' , 'created_at' , 'updated_at'];


    public function customer(){
        return $this->morphTo();
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->customer?->name ?? 'Guest Customer';
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->customer?->email ?? null;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->customer?->phone ?? null;
    }

    public function adminCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_creator_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

       public function products()
    {
        return $this->morphedByMany(Product::class, 'typeable', 'order_items')
            ->using(OrderItem::class)
            ->withPivot([ 'quantity', 'amount'])
            ->withTimestamps();
    }

    public function packages()
    {
        return $this->morphedByMany(Package::class, 'typeable', 'order_items')
            ->using(OrderItem::class)
            ->withPivot([ 'quantity', 'amount'])
            ->withTimestamps();
    }

    public function productTrials()
    {
        return $this->morphedByMany(ProductTrial::class, 'typeable', 'order_items')
            ->using(OrderItem::class)
            ->withPivot(['quantity', 'amount'])
            ->withTimestamps();
    }

   
    public function items():HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isAdminCreated(): bool
    {
        return ! is_null($this->admin_creator_id);
    }

    public static function statusMeta(): array
    {
        return [
            OrderStatus::PENDING->value => [
                'label' => 'Pending',
                'description' => 'Your order has been received and is waiting for confirmation.',
            ],
            OrderStatus::PROCESSING->value => [
                'label' => 'Processing',
                'description' => 'Your order is being prepared by the team.',
            ],
            OrderStatus::SHIPPED->value => [
                'label' => 'Shipped',
                'description' => 'Your order is on the way and will arrive soon.',
            ],
            OrderStatus::RECEIVED->value => [
                'label' => 'Received',
                'description' => 'You marked the order as received.',
            ],
            OrderStatus::COMPLETED->value => [
                'label' => 'Completed',
                'description' => 'Your order has been completed successfully.',
            ],
            OrderStatus::CANCELLED->value => [
                'label' => 'Cancelled',
                'description' => 'This order has been cancelled.',
            ],
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMeta()[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof_path ? Storage::disk('public')->url($this->payment_proof_path) : null;
    }

    public function getStatusDescriptionAttribute(): string
    {
        return self::statusMeta()[$this->status]['description'] ?? '';
    }

    public function getStatusStepIndexAttribute(): int
    {
        $keys = array_keys(self::statusMeta());
        $position = array_search($this->status, $keys, true);

        return $position === false ? 0 : $position;
    }
}
