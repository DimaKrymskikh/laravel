<?php

namespace Tests\Unit\Services\Quiz\Enums;

use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Enums\QuizStatuses\ApprovedStatus;
use App\Services\Quiz\Enums\QuizStatuses\AtWorkStatus;
use App\Services\Quiz\Enums\QuizStatuses\ReadyStatus;
use App\Services\Quiz\Enums\QuizStatuses\RemovedStatus;
use PHPUnit\Framework\TestCase;

class QuizStatusTest extends TestCase
{
    public function test_success_getInfo(): void
    {
        $this->assertInstanceOf(ApprovedStatus::class, QuizStatus::Approved->getInfo());
        $this->assertInstanceOf(AtWorkStatus::class, QuizStatus::AtWork->getInfo());
        $this->assertInstanceOf(ReadyStatus::class, QuizStatus::Ready->getInfo());
        $this->assertInstanceOf(RemovedStatus::class, QuizStatus::Removed->getInfo());
    }
}
