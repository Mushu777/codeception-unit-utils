<?php
namespace recyger\codeception\unit\utils\source;

interface SourceFileInterface extends SourceInterface, SourceWithNameInterface, SourceWithNamespaceInterface
{
    public function setPath(string $value): SourceFileInterface;
    
    public function getPath();
    
    public function addObject(SourceObjectInterface $object): SourceFileInterface;
    
    public function save(): bool;
}