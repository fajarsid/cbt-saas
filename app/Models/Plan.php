<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'max_students',
        'max_exams',
        'max_admins',
        'max_users',
        'whatsapp_enabled',
        'custom_branding',
        'export_enabled',
        'api_access',
        'features',
        'is_active',
        'is_featured',
        'trial_days',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'whatsapp_enabled' => 'boolean',
            'custom_branding' => 'boolean',
            'export_enabled' => 'boolean',
            'api_access' => 'boolean',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'current_plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getBillingCycleTextAttribute(): string
    {
        return $this->billing_cycle === 'monthly' ? 'Bulanan' : 'Tahunan';
    }

    /**
     * Check if plan has a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return in_array($feature, $features);
    }

    /**
     * Get all features as array
     */
    public function getAllFeatures(): array
    {
        $features = [];

        if ($this->whatsapp_enabled) $features[] = 'Notifikasi WhatsApp';
        if ($this->custom_branding) $features[] = 'Custom Branding';
        if ($this->export_enabled) $features[] = 'Export Data';
        if ($this->api_access) $features[] = 'API Access';

        $features[] = "{$this->max_students} Peserta";
        $features[] = "{$this->max_exams} Ujian";
        $features[] = "{$this->max_users} Admin";

        return array_merge($features, $this->features ?? []);
    }
}
