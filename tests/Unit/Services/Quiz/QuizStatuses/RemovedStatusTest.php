<?php

namespace Tests\Unit\Services\Quiz\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatuses\RemovedStatus;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class RemovedStatusTest extends TestCase
{
    public function test_success_getNextStatuses(): void
    {
        $this->assertInstanceOf(Collection::class, (new RemovedStatus())->getNextStatuses());
    }
}
