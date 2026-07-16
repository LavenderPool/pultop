<?php

namespace App\Services;

use App\Models\Proxy;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProxyService
{
    /**
     * Полная замена списка прокси из текста.
     *
     * @return array{count: int, lines: list<string>}
     */
    public function replaceFromText(string $text): array
    {
        $parsed = $this->parseText($text);

        DB::transaction(function () use ($parsed): void {
            Proxy::query()->delete();

            foreach ($parsed as $row) {
                Proxy::query()->create($row);
            }
        });

        return [
            'count' => count($parsed),
            'lines' => array_map(
                fn (array $row) => $row['host'].':'.$row['port'].(filled($row['username'] ?? null) ? ':'.$row['username'] : ''),
                $parsed,
            ),
        ];
    }

    /**
     * @return list<array{host: string, port: int, username: ?string, password: ?string, is_active: bool}>
     */
    public function parseText(string $text): array
    {
        $lines = preg_split('/\R/', $text) ?: [];
        $result = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            try {
                $result[] = $this->parseLine($line);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException(
                    'Строка '.($index + 1).': '.$e->getMessage(),
                );
            }
        }

        return $result;
    }

    /**
     * @return array{host: string, port: int, username: ?string, password: ?string, is_active: bool}
     */
    public function parseLine(string $line): array
    {
        $parts = explode(':', $line);

        if (count($parts) < 2) {
            throw new InvalidArgumentException('ожидается host:port[:user[:password]]');
        }

        $host = trim($parts[0]);
        $port = (int) trim($parts[1]);

        if ($host === '' || $port < 1 || $port > 65535) {
            throw new InvalidArgumentException('некорректный host или port');
        }

        $username = null;
        $password = null;

        if (count($parts) === 3) {
            $username = trim($parts[2]);
            if ($username === '') {
                $username = null;
            }
        } elseif (count($parts) >= 4) {
            $username = trim($parts[2]);
            $password = implode(':', array_slice($parts, 3));
            if ($username === '') {
                $username = null;
            }
            if ($password === '') {
                $password = null;
            }
        }

        return [
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'is_active' => true,
        ];
    }

    /**
     * @return list<array{id: int, host: string, port: int, label: string}>
     */
    public function listForAdmin(): array
    {
        return Proxy::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Proxy $proxy) => [
                'id' => $proxy->id,
                'host' => $proxy->host,
                'port' => $proxy->port,
                'label' => $proxy->host.':'.$proxy->port,
            ])
            ->all();
    }
}
