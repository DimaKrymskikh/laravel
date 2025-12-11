<?php

namespace Tests\Unit\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatuses\ReadyStatus;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class ReadyStatusTest extends TestCase
{
    public function test_success_getNextStatuses(): void
    {
        $this->assertInstanceOf(Collection::class, (new ReadyStatus())->getNextStatuses());
    }
}
