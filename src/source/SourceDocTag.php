<?php
namespace recyger\codeception\unit\utils\source;

class SourceDocTag extends Source implements SourceDocTagInterface
{
    /**
     * @var
     */
    private $value;
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        return $this->getSpacing($formatter) . $this->value;
    }
}