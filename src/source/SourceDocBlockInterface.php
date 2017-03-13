<?php
namespace recyger\codeception\unit\utils\source;

interface SourceDocBlockInterface extends SourceInterface
{
    public function setDescription(string $value): SourceDocBlockInterface;
    
    public function getDescription();
    
    public function addTag(SourceDocTagInterface $tag): SourceDocBlockInterface;
}