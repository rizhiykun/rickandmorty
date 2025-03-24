<?php

namespace App\Tests\Unit\Services;

use App\Services\SentimentAnalysisService;
use PHPUnit\Framework\TestCase;
use Sentiment\Analyzer;

/**
 * @covers \App\Services\SentimentAnalysisService
 */
class SentimentAnalysisServiceTest extends TestCase
{
    private SentimentAnalysisService $sentimentAnalysisService;
    private $analyzerMock;

    protected function setUp(): void
    {
        // Мокаем Sentiment\Analyzer
        $this->analyzerMock = $this->createMock(Analyzer::class);

        // Создаем экземпляр нашего сервиса, передавая мок вместо реального Analyzer
        $this->sentimentAnalysisService = new SentimentAnalysisService($this->analyzerMock);
    }

    /**
     * @covers \App\Services\SentimentAnalysisService::analyze
     */
    public function testAnalyzePositiveText(): void
    {
        $text = "I absolutely love this! It's fantastic!";

        // Подменяем возвращаемое значение метода getSentiment
        $this->analyzerMock->method('getSentiment')->willReturn(['compound' => 0.8]);

        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertGreaterThan(0, $score, "The sentiment score should be positive.");
    }

    /**
     * @covers \App\Services\SentimentAnalysisService::analyze
     */
    public function testAnalyzeNegativeText(): void
    {
        $text = "I really hate this. It's the worst thing ever.";

        // Подменяем возвращаемое значение метода getSentiment
        $this->analyzerMock->method('getSentiment')->willReturn(['compound' => -0.8]);

        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertLessThan(0, $score, "The sentiment score should be negative.");
    }

    /**
     * @covers \App\Services\SentimentAnalysisService::analyze
     */
    public function testAnalyzeNeutralText(): void
    {
        $text = "This is a simple statement with no strong feelings.";

        // Подменяем возвращаемое значение метода getSentiment
        $this->analyzerMock->method('getSentiment')->willReturn(['compound' => 0.2]);

        $score = $this->sentimentAnalysisService->analyze($text);

        $this->assertEqualsWithDelta(0.2, $score, 0.3, "The sentiment score should be close to neutral.");
    }
}
