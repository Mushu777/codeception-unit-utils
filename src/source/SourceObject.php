<?php
namespace recyger\codeception\unit\utils\source;

class SourceObject extends Source implements SourceObjectInterface
{
    use SourceWithNameTrait, SourceWithNamespaceTrait, SourceWithDocBlockTrait;
    
    /**
     * @var \recyger\codeception\unit\utils\source\SourceMethodInterface[]
     */
    private $methods = [];
    
    /**
     * @var int
     */
    private $type = self::TYPE_CLASS;
    
    /**
     * @var SourceObjectInterface[]
     */
    private $extends = [];
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        $spacing = $this->getSpacing();
        $result = '';
        $docBlock = $this->renderDockBlock($formatter);
        
        if (empty($docBlock) === false) {
            $result .= $spacing. "\n";
            $result .= $docBlock;
            $result .= $spacing . "\n";
        }
        
        $result .= $spacing . $this->formatName();
        
        if (empty($this->extends) === false) {
            $result .= ' extends ';
            foreach ($this->extends as $extend) {
                if ($this->getName() !== $extend->getName()) {
                    $result .= $extend->getName() . ', ';
                }
            }
            
            $result = substr($result, 0, -2);
        }
        
        $result .= $spacing ."\n{\n";
    
        $subFormatter = SourceFormatter::create($formatter)->addSpacingSuffix('    ');
        
        foreach ($this->methods as $method) {
            $result .= $method->render($subFormatter);
        }
        
        $result .= $spacing . "\n}\n";
        
        return $result;
    }
    
    public function addMethod(SourceMethodInterface $method): SourceObjectInterface
    {
        $this->methods[] = $method;
        
        return $this;
    }
    
    public function getObjectName(): string
    {
        $result = '';
        
        if (empty($this->getName()) === false) {
            $result .= $this->getName();
        }
    
    
        if (empty($result) === false && empty($this->getNamespace()) === false) {
            $result = '\\' . $this->getNamespace() . '\\' . $result;
        }
        
        return $result;
    }
    
    public function setObjectName(string $value): SourceObjectInterface
    {
        $position = strrpos($value, '\\');
        $name = substr($value, $position + 1);
        $namespace = substr($value, 0, $position);
        
        if (empty($name) === false) {
            $this->setName($name);
        }
        
        if (empty($namespace) === false) {
            $this->setNamespace($namespace);
        }
        
        return $this;
    }
    
    public function setType(int $value): SourceObjectInterface
    {
        $this->type = $value;
        
        return $this;
    }
    
    public function getType(): int
    {
        return $this->type;
    }
    
    private function formatName()
    {
        $result = '';
        
        switch ($this->type) {
            case self::TYPE_CLASS:
                $result .= 'class';
                break;
            case self::TYPE_INTERFACE:
                $result .= 'interface';
                break;
            case self::TYPE_TRAIT:
                $result .= 'trait';
                break;
        }
        
        $result .= ' ' . $this->getName();
        
        return $result;
    }
    
    public function addExtends(SourceObjectInterface ...$objects): SourceObjectInterface
    {
        $this->extends = array_merge($this->extends, $objects);
        
        return $this;
    }
    
    public function getUsedNamespace(): array
    {
        $result = [];
        
        foreach ($this->extends as $extend) {
            if ($this->getName() !== $extend->getName()) {
                $result[] = $extend->getObjectName();
            }
        }
        
        return $result;
    }
}