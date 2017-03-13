<?php
namespace recyger\codeception\unit\utils\source;

class SourceMethod extends Source implements SourceMethodInterface
{
    use SourceWithNameTrait, SourceWithDocBlockTrait, SourceWithVisibilityTrait, SourceWithStaticTrait;
    
    /**
     * @var \recyger\codeception\unit\utils\source\SourceLineInterface[]
     */
    private $lines;
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        $spacing      = $this->getSpacing($formatter);
        $childrenFormatter = SourceFormatter::create($formatter)->addSpacingPrefix('    ');
        
        $result = $spacing . "\n";
        $result .= $this->renderDockBlock($formatter);
        $result .= $spacing . "\n";
        $result .= $spacing . $this->formatName();
        $result .= $this->formatParameters();
        $result .= "\n";
        $result .= $spacing . "{\n";
        
        foreach ($this->lines as $line) {
            $result .= $line->render($childrenFormatter) . "\n";
        }
        
        $result .= $spacing . "}\n";
        
        return $result;
    }
    
    public function addLine(SourceLineInterface $value): SourceMethodInterface
    {
        $this->lines[] = $value;
        
        return $this;
    }
    
    private function formatName()
    {
        $result = '';
        
        switch ($this->getVisibility()) {
            case self::VISIBILITY_PUBLIC:
                $result .= 'public ';
                break;
            case self::VISIBILITY_PROTECTED:
                $result .= 'protected ';
                break;
            case self::VISIBILITY_PRIVATE:
                $result .= 'private ';
                break;
        }
        
        if ($this->isStatic() === true) {
            $result .= 'static ';
        }
        
        $result .= 'function ' . $this->getName();
        
        return $result;
    }
    
    private function formatParameters()
    {
        return '()';
    }
}