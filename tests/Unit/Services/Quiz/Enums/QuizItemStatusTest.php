<?php

namespace Tests\Unit\Services\Quiz\Enums;

use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\AtWorkStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\ReadyStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\RemovedStatus;
use PHPUnit\Framework\TestCase;

class QuizItemStatusTest extends TestCase
{
    public function test_success_getInfo(): void
    {
        $this->assertInstanceOf(AtWorkStatus::class, QuizItemStatus::AtWork->getInfo());
        $this->assertInstanceOf(ReadyStatus::class, QuizItemStatus::Ready->getInfo());
        $this->assertInstanceOf(RemovedStatus::class, QuizItemStatus::Removed->getInfo());
    }
}
