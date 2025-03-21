<?php

namespace App\Services;

use Sentiment\Analyzer;

class SentimentAnalysisService
{
    private Analyzer $analyzer;

    public function __construct(
    ) {
        $this->analyzer = new Analyzer();
    }

    public function analyze(string $text): float
    {
        $result = $this->analyzer->getSentiment($text);
        return $result['compound'];
    }

}