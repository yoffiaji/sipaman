<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nama', 'email', 'nib', 'password', 'role_id', 'status_akun',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function auditTrails(): HasMany
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(ImportLog::class);
    }

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role->nama_role ?? null, $roles);
    }

    public function isActive(): bool
    {
        return $this->status_akun === 'aktif';
    }

    /**
     * Akun pelaku usaha auto-create dari import belum punya password.
     * Selama password null, akun ini tidak bisa login — admin harus
     * set password dulu (manual atau generate) lewat menu user management.
     */
    public function needsPasswordSetup(): bool
    {
        return $this->password === null;
    }
}
