<?php

namespace Tests\Unit\Services\Quiz\Enums\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatuses\AtWorkStatus;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class AtWorkStatusTest extends TestCase
{
    public function test_success_getNextStatuses(): void
    {
        $this->assertInstanceOf(Collection::class, (new AtWorkStatus())->getNextStatuses());
    }
}
