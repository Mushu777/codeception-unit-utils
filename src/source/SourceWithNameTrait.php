<?php
namespace recyger\codeception\unit\utils\source;

trait SourceWithNameTrait
{
    /**
     * @var string|null
     */
    private $name = null;
    
    public function setName(string $name): SourceWithNameInterface
    {
        $this->name = $name;
    
        /** @var SourceWithNameInterface $this */
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
}