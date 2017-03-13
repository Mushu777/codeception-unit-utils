<?php
namespace recyger\codeception\unit\utils\source;

interface SourceObjectInterface extends SourceInterface,
    SourceWithNameInterface,
    SourceWithNamespaceInterface,
    SourceWithDocBlockInterface
{
    const TYPE_CLASS     = 1;
    const TYPE_INTERFACE = 2;
    const TYPE_TRAIT     = 3;
    
    public function addMethod(SourceMethodInterface $method): SourceObjectInterface;
    
    public function getObjectName(): string;
    
    public function setObjectName(string $value): SourceObjectInterface;
    
    public function setType(int $value): SourceObjectInterface;
    
    public function getType(): int;
    
    public function addExtends(SourceObjectInterface ...$objects): SourceObjectInterface;
}