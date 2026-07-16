<?php

namespace App\Services\Rates;

use App\Models\Proxy;
use Illuminate\Support\Collection;

class ProxyPool
{
    /** @var Collection<int, Proxy> */
    private Collection $proxies;

    private int $index = 0;

    public function __construct()
    {
        $this->reload();
    }

    public function reload(): void
    {
        $this->proxies = Proxy::query()->active()->orderBy('id')->get();
        $this->index = 0;
    }

    public function isEmpty(): bool
    {
        return $this->proxies->isEmpty();
    }

    public function count(): int
    {
        return $this->proxies->count();
    }

    public function next(): ?Proxy
    {
        if ($this->proxies->isEmpty()) {
            return null;
        }

        $proxy = $this->proxies[$this->index % $this->proxies->count()];
        $this->index++;

        return $proxy;
    }

    public function random(): ?Proxy
    {
        if ($this->proxies->isEmpty()) {
            return null;
        }

        return $this->proxies->random();
    }
}
