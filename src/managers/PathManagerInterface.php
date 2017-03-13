<?php
namespace recyger\codeception\unit\utils\managers;

interface PathManagerInterface
{
    /**
     * Получение пути к исходному коду
     *
     * @return string
     */
    public function getSourcePath(): string;
    
    /**
     * Получение пути к тестам
     *
     * @return string
     */
    public function getTestPath(): string;
    
    /**
     * Получение пространства имен исходного кода
     *
     * @return string
     */
    public function getSourceNamespace(): string;
    
    /**
     * Получение простраства имен тестов
     *
     * @return string
     */
    public function getTestNamespace(): string;
}