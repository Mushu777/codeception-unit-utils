<?php
namespace recyger\codeception\unit\utils\source;

interface SourceWithStaticInterface
{
    public function isStatic(): bool;
    
    public function setStaticFlag(bool $value): SourceWithStaticInterface;
}