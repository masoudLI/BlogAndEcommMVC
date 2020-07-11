<?php

namespace Framework\Twig;

use DateTime;
use PHPUnit\Framework\TestCase;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;

class TextExtensionTest extends TestCase
{
    private $textExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->textExtension = new TextExtension();
        $this->timeExtension = new TimeExtension();
    }

    public function testShortTest()
    {
        $text = 'salut';
        $this->assertEquals('salut', $this->textExtension->excerpt($text, 10));
    }

    public function testLongTest()
    {
        $text = 'salut les gens';
        $this->assertEquals('salut...', $this->textExtension->excerpt($text, 5));
        $this->assertEquals('salut les...', $this->textExtension->excerpt($text, 12));
    }

    public function testDateFormat()
    {
        $date = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<span class="timeago" datetime="' .
        $date->format(\DateTime::ISO8601) .
            '">' .
            $date->format($format) .
            '</span>';
        $this->assertEquals($result, $this->timeExtension->ago($date));
    }
}
