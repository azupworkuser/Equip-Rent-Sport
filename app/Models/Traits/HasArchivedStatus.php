<?php

namespace App\Models\Traits;

use App\Models\Scopes\ArchivedStatus;
use Illuminate\Support\Facades\Log;

trait HasArchivedStatus
{
    public static function bootHasArchivedStatus(): void
    {
        Log::info('bootArchivedStatus');
        static::addGlobalScope(new ArchivedStatus());
    }

    /**
     * Get the archived status name.
     *
     * @return string
     */
    public function getArchivedStatusName(): string
    {
        return defined(static::class . '::ARCHIVED_STATUS') ? static::ARCHIVED_STATUS : 'archived';
    }


    public function getStatusColumn(): string
    {
        return defined(static::class . '::STATUS_COLUMN') ? static::STATUS_COLUMN : 'status';
    }

    public function getQualifiedStatusColumn(): string
    {
        return $this->qualifyColumn($this->getStatusColumn());
    }
}
