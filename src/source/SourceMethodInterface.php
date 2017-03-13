<?php
namespace recyger\codeception\unit\utils\source;

interface SourceMethodInterface extends
    SourceInterface,
    SourceWithNameInterface,
    SourceWithDocBlockInterface,
    SourceWithVisibilityInterface,
    SourceWithStaticInterface
{
    public function addLine(SourceLineInterface $value): SourceMethodInterface;
}