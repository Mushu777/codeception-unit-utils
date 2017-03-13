<?php
namespace recyger\codeception\unit\utils\filters;

interface PathFilterInterface
{
    /**
     * Проверем что путь проходит фильтрацию
     *
     * @param string $path
     *
     * @return bool
     */
    public function accept(string $path): bool;
    
    public function addStringPatterns(string ...$patterns): PathFilterInterface;
}