<?php
namespace recyger\codeception\unit\utils\source;

/**
 * Interface SourceFormatterInterface
 *
 * @package recyger\codeception\unit\utils\source
 *
 *          TODO: переосмыслить реализацию форматора
 */
interface SourceFormatterInterface
{
    public static function create(SourceFormatterInterface $parent = null): SourceFormatterInterface;
    
    public function getSpacing(): string;
    
    public function setSpacing(string $value): SourceFormatterInterface;
    
    public function addSpacingPrefix(string $value): SourceFormatterInterface;
    
    public function addSpacingSuffix(string $value): SourceFormatterInterface;
}