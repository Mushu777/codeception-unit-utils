<?php
namespace recyger\codeception\unit\utils\source;

trait SourceWithDocBlockTrait
{
    public $docBlockClass = SourceDocBlock::class;
    
    private $docBlock = null;
    
    public function __construct()
    {
        $docBlockClass = $this->docBlockClass;
        $this->docBlock = new $docBlockClass();
    }
    
    public function getDockBlock(): SourceDocBlockInterface
    {
        return $this->docBlock;
    }
    
    public function renderDockBlock(SourceFormatterInterface $formatter = null): string
    {
        return $this->getDockBlock()->render($formatter);
    }
}