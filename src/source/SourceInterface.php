<?php
namespace recyger\codeception\unit\utils\source;

interface SourceInterface
{
    public function render(SourceFormatterInterface $formatter = null): string;
    
    public function getSpacing(SourceFormatterInterface $formatter = null): string;
}