<?php

namespace App\Services;

use Sentiment\Analyzer;

class SentimentAnalysisService
{
    private Analyzer $analyzer;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(Analyzer $analyzer = null)
    {
        $this->analyzer = $analyzer ?: new Analyzer();
    }

    public function analyze(string $text): float
    {
        $result = $this->analyzer->getSentiment($text);
        return $result['compound'];
    }
}
