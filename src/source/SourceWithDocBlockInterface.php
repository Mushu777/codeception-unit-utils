<?php
namespace recyger\codeception\unit\utils\source;

interface SourceWithDocBlockInterface extends SourceInterface
{
    public function getDockBlock(): SourceDocBlockInterface;
}