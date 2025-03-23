<?php

namespace App\Tests\Unit\Services;

use App\Services\SentimentAnalysisService;
use PHPUnit\Framework\TestCase;

class SentimentAnalysisServiceTest extends TestCase
{
    private SentimentAnalysisService $sentimentAnalysisService;

    protected function setUp(): void
    {
        $this->sentimentAnalysisService = new SentimentAnalysisService();
    }

    public function testAnalyzePositiveText(): void
    {
        $text = "I absolutely love this! It's fantastic!";
        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertGreaterThan(0, $score, "The sentiment score should be positive.");
    }

    public function testAnalyzeNegativeText(): void
    {
        $text = "I really hate this. It's the worst thing ever.";
        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertLessThan(0, $score, "The sentiment score should be negative.");
    }

    public function testAnalyzeNeutralText(): void
    {
        $text = "This is a simple statement with no strong feelings.";
        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertEqualsWithDelta(0.2, $score, 0.3, "The sentiment score should be close to neutral.");
    }
}
