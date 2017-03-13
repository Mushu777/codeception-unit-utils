<?php
namespace recyger\codeception\unit\utils\source;

interface SourceLineInterface extends SourceInterface
{
    public function setValue(string $value): SourceLineInterface;
}