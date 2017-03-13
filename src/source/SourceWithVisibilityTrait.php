<?php
namespace recyger\codeception\unit\utils\source;

trait SourceWithVisibilityTrait
{
    private $visibility = SourceWithVisibilityInterface::VISIBILITY_PUBLIC;
    
    public function setVisibility(int $value): SourceWithVisibilityInterface
    {
        $this->visibility = $value;
    
        /** @var \recyger\codeception\unit\utils\source\SourceWithVisibilityInterface $this */
        return $this;
    }
    
    public function getVisibility(): int
    {
        return $this->visibility;
    }
}