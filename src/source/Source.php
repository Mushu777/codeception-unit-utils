<?php
namespace recyger\codeception\unit\utils\source;

abstract class Source implements SourceInterface
{
    public function getSpacing(SourceFormatterInterface $formatter = null): string
    {
        return is_null($formatter) === true ? '' : $formatter->getSpacing();
    }
}