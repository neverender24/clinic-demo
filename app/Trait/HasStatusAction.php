<?php

namespace App\Trait;

trait HasStatusAction
{
    public static function statusLabel($record)
    {
        return static::isDone($record) ? 'Set to Pending' : 'Done';
    }
    
    public static function statusColor($record)
    {
        return static::isDone($record) ? 'primary' : 'success';
    }
    
    public static function statusIcon($record)
    {
        return static::isDone($record) ? 'heroicon-o-clock' : 'heroicon-o-check';
    }

    protected static function isDone($record): bool
    {
        return $record->status->value == 'Done';
    }
}
