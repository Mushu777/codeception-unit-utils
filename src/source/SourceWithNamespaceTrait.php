<?php
namespace recyger\codeception\unit\utils\source;

trait SourceWithNamespaceTrait
{
    /**
     * @var string|null
     */
    private $namespace = null;
    
    public function setNamespace(string $value): SourceWithNamespaceInterface
    {
        $this->namespace = $value;
    
        /** @var SourceWithNamespaceInterface $this */
        return $this;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }
}