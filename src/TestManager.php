<?php
namespace recyger\codeception\unit\utils;

use recyger\codeception\unit\utils\source\SourceFile;
use recyger\codeception\unit\utils\source\SourceFileInterface;
use recyger\codeception\unit\utils\source\SourceLine;
use recyger\codeception\unit\utils\source\SourceMethod;
use recyger\codeception\unit\utils\source\SourceMethodInterface;
use recyger\codeception\unit\utils\source\SourceObject;
use recyger\codeception\unit\utils\source\SourceObjectInterface;

class TestManager
{
    /**
     * @var \recyger\codeception\unit\utils\source\SourceFileInterface[]
     */
    private $files = [];
    
    public $fileSuffix = '.gen';
    
    public function createTestMethods(array $classes): bool
    {
        foreach ($classes as $className => $classProperties) {
            $class = $this->getClass($className, $classProperties);
            
            foreach ($classProperties['methods'] as $methodName => $methodProperties) {
                $method = $this->getMethod($methodName, $methodProperties);
                
                $class->addMethod($method);
            }
        }
        
        foreach ($this->files as $file) {
            $file->save();
        }
        
        return true;
    }
    
    private function getClass($className, $properties, bool $create = true): SourceObjectInterface
    {
        $class = new SourceObject();
        $class->setObjectName($className);
        $class->addExtends((new SourceObject())->setObjectName('Codeception\Test\Unit'));
        $this->getFile($properties['path'], $create)->setNamespace($class->getNamespace())->addObject($class);
        
        return $class;
    }
    
    private function getFile(string $path, bool $create = true): SourceFileInterface
    {
        $file = new SourceFile();
        $file->setPath($path . $this->fileSuffix);
        $this->files[] = $file;
        
        return $file;
    }
    
    private function getMethod(string $methodName, array $methodProperties): SourceMethodInterface
    {
        $method = new SourceMethod();
        $method->setName($methodName);
        $method->getDockBlock()
            ->setDescription('Test for ' . $methodProperties['originName']);
        $method->addLine(
            (new SourceLine())
                ->setValue('$this->markTestIncomplete(\'This test has not been implemented yet.\');')
        );
        
        return $method;
    }
}