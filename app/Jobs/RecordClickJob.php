<?php

namespace App\Jobs;

use App\Actions\RecordClickAction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordClickJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $linkId,
        public string $ipAddress,
        public ?string $userAgent,
        public string $clickedAt,
    ) {}

    public function handle(RecordClickAction $recordClick): void
    {
        $recordClick->execute(
            $this->linkId,
            $this->ipAddress,
            $this->userAgent,
            Carbon::parse($this->clickedAt),
        );
    }
}
