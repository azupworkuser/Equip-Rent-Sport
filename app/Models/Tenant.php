<?php

namespace App\Models;

use App\Exceptions\NoPrimaryDomainException;
use App\CoreLogic\Services\TenantS3Bucket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * @property-read string $plan_name The tenant's subscription plan name
 * @property-read bool $on_active_subscription Is the tenant actively subscribed (not on grace period)
 * @property-read bool $can_use_app Can the tenant use the application (is on trial or subscription)
 *
 * @property-read Domain[]|Collection $domains
 */
class Tenant extends BaseTenant implements \Stancl\Tenancy\Contracts\Tenant
{
    use HasFactory;
    use HasDomains;
    use Billable;

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    /**
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'stripe_id',
            'pm_type',
            'pm_last_four',
            'trial_ends_at',
        ];
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    // phpcs:disable
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primary_domain()
    {
        return $this->hasOne(Domain::class)->where('is_primary', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fallback_domain()
    {
        return $this->hasOne(Domain::class)->where('is_fallback', true);
    }
    // phpcs:enable

    /**
     * @param $route
     * @param $parameters
     * @param $absolute
     * @return array|string|string[]
     * @throws NoPrimaryDomainException
     */
    public function route($route, $parameters = [], $absolute = true)
    {
        if (! $this->primary_domain) {
            throw new NoPrimaryDomainException();
        }

        $domain = $this->primary_domain->domain;

        $parts = explode('.', $domain);
        if (count($parts) === 1) { // If subdomain
            $domain = Domain::domainFromSubdomain($domain);
        }

        return tenant_route($domain, $route, $parameters, $absolute);
    }

    /**
     * @param $user_id
     * @return string
     * @throws NoPrimaryDomainException
     */
    public function impersonationUrl($user_id): string
    {
        $token = tenancy()->impersonate($this, $user_id, $this->route('tenant.dashboard'), 'web')->token;

        return $this->route('tenant.impersonate', ['token' => $token]);
    }

    /**
     * @return string
     */
    public function getPlanNameAttribute(): string
    {
        return config('saas.plans')[$this->subscription('default')->stripe_price];
    }

    /**
     * @return string
     */
    public function getOnActiveSubscriptionAttribute(): bool
    {
        return $this->subscribed('default') && ! $this->subscription('default')->cancelled();
    }

    /**
     * @return bool
     */
    public function getCanUseAppAttribute(): bool
    {
        return $this->onTrial() || $this->subscribed('default');
    }

    /**
     * @return TenantS3Bucket
     */
    public function bucket(): TenantS3Bucket
    {
        return new TenantS3Bucket($this);
    }

    /**
     * @return HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @param string|null $userId
     * @return bool
     */
    public function isInAdminTeam(?string $userId = null): bool
    {
        $domain = $this->primary_domain->domain;
        return $this
            ->teams()
            ->whereHas('tenant.domains', function ($query) use ($domain) {
                $query->where('domain', $domain)->where('tenant_id', $this->getKey());
            })
            ->where('name', 'Admins')
            ->whereHas('users', function ($query) use ($userId) {
                return $query->where('user_id', $userId ?? auth()->id());
            })
            ->exists();
    }

    /**
     * @return HasMany
     */
    public function assetCategories(): HasMany
    {
        return $this->hasMany(AssetCategory::class);
    }
}
