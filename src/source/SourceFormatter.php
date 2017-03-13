<?php
namespace recyger\codeception\unit\utils\source;

class SourceFormatter implements SourceFormatterInterface
{
    /**
     * @var string|null
     */
    private $spacing = null;
    
    /**
     * @var \recyger\codeception\unit\utils\source\SourceFormatterInterface
     */
    private $parent = null;
    
    public function __construct(SourceFormatterInterface $parent = null)
    {
        $this->parent = $parent;
    }
    
    public static function create(SourceFormatterInterface $parent = null): SourceFormatterInterface
    {
        return new static($parent);
    }
    
    public function getSpacing(): string
    {
        $result = '';
        
        if (is_null($this->parent) === false){
            $result = $this->parent->getSpacing();
        }
        
        return $result . (string) $this->spacing;
    }
    
    public function setSpacing(string $value): SourceFormatterInterface
    {
        $this->spacing = $value;
        
        return $this;
    }
    
    public function addSpacingPrefix(string $value): SourceFormatterInterface
    {
        $this->spacing = $value . (string) $this->spacing;
    
        return $this;
    }
    
    public function addSpacingSuffix(string $value): SourceFormatterInterface
    {
        $this->spacing = (string) $this->spacing . $value;
        
        return $this;
    }
}