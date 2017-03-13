<?php
namespace recyger\codeception\unit\utils\source;

trait SourceWithStaticTrait
{
    private $staticFlag = false;
    
    public function isStatic(): bool
    {
        return $this->staticFlag;
    }
    
    public function setStaticFlag(bool $value): SourceWithStaticInterface
    {
        $this->staticFlag = $value;
        
        return $this;
    }
}