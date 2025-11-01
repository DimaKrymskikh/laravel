<?php

namespace Tests\Unit\Services\Quiz\QuizStatuses;

use App\Services\Quiz\Enums\QuizStatuses\ApprovedStatus;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

final class ApprovedStatusTest extends TestCase
{
    public function test_success_getNextStatuses(): void
    {
        $this->assertInstanceOf(Collection::class, (new ApprovedStatus())->getNextStatuses());
    }
}
