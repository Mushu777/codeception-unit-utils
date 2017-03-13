<?php
namespace recyger\codeception\unit\utils\source;

interface SourceWithNameInterface
{
    public function setName(string $name): SourceWithNameInterface;
    
    public function getName();
}