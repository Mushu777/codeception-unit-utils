<?php
namespace recyger\codeception\unit\utils\source;

class SourceDocBlock extends Source implements SourceDocBlockInterface
{
    /**
     * @var string|null
     */
    private $description = null;
    
    /**
     * @var \recyger\codeception\unit\utils\source\SourceDocTagInterface[]
     */
    private $tags = [];
    
    public function setDescription(string $value): SourceDocBlockInterface
    {
        $this->description = $value;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function addTag(SourceDocTagInterface $tag): SourceDocBlockInterface
    {
        $this->tags[] = $tag;
        
        return $this;
    }
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        $result = '';
        
        if (is_null($this->description) === false || empty($this->tags) === false) {
            $spacing = $this->getSpacing($formatter);
            $result = $spacing . "/**\n";
            $childrenFormatter = SourceFormatter::create($formatter)->addSpacingSuffix(' * ');
            
            if (is_null($this->description) === false) {
                $result .= $childrenFormatter->getSpacing() . $this->description;
            }
            
            if (empty($this->tags) === false) {
                $result .= $childrenFormatter->getSpacing() . "\n";
                
                foreach ($this->tags as $tag) {
                    $result .= $tag->render($childrenFormatter);
                }
            }
            
            $result .= "\n" . $spacing . ' */';
        }
        
        return $result;
    }
}