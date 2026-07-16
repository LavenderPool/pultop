<?php

namespace App\Enums;

enum ParseRunStatus: string
{
    case Running = 'running';
    case Success = 'success';
    case Partial = 'partial';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Running => 'Выполняется',
            self::Success => 'Успешно',
            self::Partial => 'Частично',
            self::Failed => 'Ошибка',
        };
    }
}
