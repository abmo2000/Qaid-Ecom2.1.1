<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\AdminRole;
use App\Models\CustomerTraffic;
use Filament\Panel;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_name',
        'is_approved',
        'extra_permissions',
        'phone',
        'address',
        'google_id',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'role_name' => 'string',
            'extra_permissions' => 'array',
        ];
    }

     /**
     * Get the user role.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->role_name, AdminRole::toArray(), true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_name === AdminRole::SUPER_ADMIN->value;
    }

    public function isSalesAdmin(): bool
    {
        return $this->role_name === AdminRole::SALES_ADMIN->value;
    }

    public function isAccountingAdmin(): bool
    {
        return $this->role_name === AdminRole::ACCOUNTING_ADMIN->value;
    }

    public function isAssetAdmin(): bool
    {
        return $this->role_name === AdminRole::ASSET_ADMIN->value;
    }

    public function hasExtraPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->extra_permissions ?? []);
    }

    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function isCustomer(): bool
    {
        return $this->role_name === 'customer';
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function orders(){
        return $this->morphMany(Order::class , 'customer');
    }

    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'admin_creator_id');
    }

    public function traffic(): HasMany
    {
        return $this->hasMany(CustomerTraffic::class);
    }

    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }

    public function createdCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    

    public function getOrdersCountAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getTrafficCountAttribute(): int
    {
        return $this->traffic()->count();
    }

    public function getLastOrderAtAttribute(): ?Carbon
    {
        $date = $this->orders()->latest('created_at')->value('created_at');

        return $date ? Carbon::parse($date) : null;
    }

    public function getLastVisitedAtAttribute(): ?Carbon
    {
        $date = $this->traffic()->latest('created_at')->value('created_at');

        return $date ? Carbon::parse($date) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && ($this->isSuperAdmin() || $this->isApproved());
    }
}
