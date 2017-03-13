<?php
namespace recyger\codeception\unit\utils\source;

class SourceLine extends Source implements SourceLineInterface
{
    /**
     * @var string|null
     */
    private $content = null;
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        return $this->getSpacing($formatter) . $this->content;
    }
    
    public function setValue(string $value): SourceLineInterface
    {
        $this->content = $value;
        
        return $this;
    }
}