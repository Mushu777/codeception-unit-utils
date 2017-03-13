<?php
namespace recyger\codeception\unit\utils\source;

interface SourceWithNamespaceInterface
{
    public function setNamespace(string $value): SourceWithNamespaceInterface;
    
    public function getNamespace();
    
    public function getUsedNamespace(): array;
}