<?php
namespace recyger\codeception\unit\utils\source;

interface SourceWithVisibilityInterface
{
    const VISIBILITY_PUBLIC    = 1;
    const VISIBILITY_PROTECTED = 2;
    const VISIBILITY_PRIVATE   = 4;
    
    public function setVisibility(int $value): SourceWithVisibilityInterface;
    
    public function getVisibility(): int;
}