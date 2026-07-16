<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'is_active',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Proxy>  $query
     * @return Builder<Proxy>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function toHttpProxyUrl(): string
    {
        $auth = '';

        if (filled($this->username)) {
            $user = rawurlencode((string) $this->username);
            $pass = rawurlencode((string) ($this->password ?? ''));
            $auth = "{$user}:{$pass}@";
        }

        return "http://{$auth}{$this->host}:{$this->port}";
    }

    public function displayLabel(): string
    {
        $label = "{$this->host}:{$this->port}";

        if (filled($this->username)) {
            $label .= ':'.$this->username;
            if (filled($this->password)) {
                $label .= ':***';
            }
        }

        return $label;
    }
}
