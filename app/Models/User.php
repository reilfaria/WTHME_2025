<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'nim', 'angkatan', 'kelompok', 'divisi',
        'must_change_password',
        'device_fingerprint', 'fingerprint_set_at','gender',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'fingerprint_set_at'   => 'datetime',
            'password'             => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    // Akses portal panitia: panitia, admin, bendahara
    public function isPanitia(): bool
    {
        return in_array($this->role, ['panitia', 'admin', 'bendahara','mentor', 'korlap', 'ketuplak']);
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBendahara(): bool
    {
        return in_array($this->role, ['bendahara', 'admin']);
    }

    public function isMentor(): bool
    {
        return in_array($this->role, ['mentor', 'admin']);
    }

    public function isKorlap(): bool
    {
        return in_array($this->role, ['korlap', 'admin', 'ketuplak']);
    }

    public function absensiPeserta()
    {
        return $this->hasMany(\App\Models\AbsensiPeserta::class);
    }

    public function absensiPanitia()
    {
        return $this->hasMany(\App\Models\AbsensiPanitia::class);
    }

    public function kasTransaksi()
    {
        return $this->hasMany(\App\Models\KasTransaksi::class, 'created_by');
    }
}