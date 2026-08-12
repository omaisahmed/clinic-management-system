<?php

declare(strict_types=1);

namespace Modules\Queue\Services;

use Illuminate\Support\Facades\DB;
use Modules\Queue\Enums\QueueStatus;
use Modules\Queue\Models\QueueEntry;

class QueueService
{
    public function issueToken(?string $clinicId = null): int
    {
        return DB::transaction(function () use ($clinicId): int {
            $query = QueueEntry::query()
                ->whereDate('created_at', today());

            if ($clinicId !== null) {
                $query->where('clinic_id', $clinicId);
            }

            return $query->count() + 1;
        });
    }

    public function callNext(?string $clinicId = null): ?QueueEntry
    {
        $entry = QueueEntry::query()
            ->whereDate('created_at', today())
            ->where('status', QueueStatus::Waiting->value)
            ->when($clinicId !== null, fn ($q) => $q->where('clinic_id', $clinicId))
            ->orderBy('token_number', 'asc')
            ->first();

        if ($entry === null) {
            return null;
        }

        $entry->status = QueueStatus::InProgress;
        $entry->called_at = now();
        $entry->save();

        return $entry;
    }
}
