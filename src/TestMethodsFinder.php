<?php
namespace recyger\codeception\unit\utils;

use recyger\codeception\unit\utils\filters\PathFilterInterface;
use recyger\codeception\unit\utils\managers\PathManagerInterface;
use ReflectionClass;
use ReflectionMethod;

class TestMethodsFinder
{
    public function __construct(PathManagerInterface $manager, PathFilterInterface $filter = null)
    {
        $this->pathManager = $manager;
        $this->pathFilter  = $filter;
    }
    
    public function getNotExists(): array
    {
        $result = [];
        
        foreach ($this->getSourceMethod() as $sourceClass => $sourceMethods) {
            $testClass          = $this->formatTestClassFromSourceClass($sourceClass);
            $result[$testClass] = ['path' => $this->convertTestClassToPath($testClass), 'methods' => []];
            $test               = null;
            
            if (class_exists($testClass) === true) {
                $test = new ReflectionClass($testClass);
            }
            
            /** @var \ReflectionMethod[] $sourceMethods */
            foreach ($sourceMethods as $sourceMethodName => $sourceMethod) {
                foreach ($this->formatTestMethodFromSourceMethod($sourceMethod) as $testMethodName) {
                    if (is_null($test) === true || $test->hasMethod($testMethodName) === false) {
                        $result[$testClass]['methods'][$testMethodName] = [
                            'originName' => $sourceMethodName,
                            'static'     => $sourceMethod->isStatic(),
                        ];
                    }
                }
            }
            
            if (empty($result[$testClass]['methods']) === true) {
                unset($result[$testClass]);
            }
        }
        
        return $result;
    }
    
    /**
     * Получение возможных методов для тестирования
     *
     * @return array
     *
     */
    private function getSourceMethod(): array
    {
        $result                     = [];
        $filter                     = $this->pathFilter;
        $recursiveDirectoryIterator = new \RecursiveDirectoryIterator(
            $this->pathManager->getSourcePath(),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $recursiveIterator          = new \RecursiveIteratorIterator(
            $recursiveDirectoryIterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($recursiveIterator as $item) {
            /** @var \RecursiveDirectoryIterator $item */
            $path = $item->getRealPath();
            
            if (is_file($path) === true
                && (
                    is_null($filter) === true
                    || $filter->accept($this->cutRootFromPath($path, $this->pathManager->getSourcePath())) === true
                )
            ) {
                $className = $this->convertSourcePathToClass($path);
                
                if (class_exists($className) === true) {
                    $class = new ReflectionClass($className);
                    
                    if ($class->isAbstract() === false && $class->isTrait() === false
                        && $class->isInterface()
                           === false
                    ) {
                        $result[$class->getName()] =
                            $this->filterMethods($class->getMethods(ReflectionMethod::IS_PUBLIC));
                        
                        if (empty($result[$class->getName()]) === true) {
                            unset($result[$class->getName()]);
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    private function cutRootFromPath(string $path, string $rootPath): string
    {
        return trim(substr(substr(str_replace('/', '\\', $path), 0, -4), mb_strlen($rootPath)), '\\');
    }
    
    private function convertSourcePathToClass($path): string
    {
        return '\\'
               . $this->pathManager->getSourceNamespace()
               . '\\'
               . $this->cutRootFromPath($path, $this->pathManager->getSourcePath());
    }
    
    private function convertTestClassToPath($className): string
    {
        $baseName = $this->cutRootNamespaceFromClass(trim($className, '\\'), $this->pathManager->getTestNamespace());
        
        return $this->pathManager->getTestPath()
               . DIRECTORY_SEPARATOR
               . str_replace('\\', DIRECTORY_SEPARATOR, $baseName)
               . '.php';
    }
    
    private function formatTestClassFromSourceClass(string $sourceClassName): string
    {
        $baseName = $this->cutRootNamespaceFromClass($sourceClassName, $this->pathManager->getSourceNamespace());
        
        return '\\'
               . $this->pathManager->getTestNamespace()
               . '\\'
               . 'unit'
               . '\\'
               . $baseName
               . 'Test';
    }
    
    private function filterMethods(array $methods): array
    {
        $result = [];
        
        /** @var \ReflectionMethod[] $methods */
        foreach ($methods as $method) {
            $name = $method->getName();
            if (strpos($name, '__') === false) {
                $result[$name] = $method;
            }
        }
        
        return $result;
    }
    
    private function formatTestMethodFromSourceMethod(ReflectionMethod $sourceMethod): array
    {
        $sourceMethodName = ucfirst($sourceMethod->getName());
        
        return [
            'testPositive' . $sourceMethodName,
            'testNegative' . $sourceMethodName,
        ];
    }
    
    private function cutRootNamespaceFromClass(string $className, string $rootNamespace): string
    {
        return trim(substr($className, mb_strlen($rootNamespace)), '\\');
    }
}