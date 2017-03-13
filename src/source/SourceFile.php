<?php
namespace recyger\codeception\unit\utils\source;

use recyger\codeception\unit\utils\helpers\DirectoryHelper;

class SourceFile extends Source implements SourceFileInterface
{
    use SourceWithNameTrait, SourceWithNamespaceTrait;
    /**
     * @var \recyger\codeception\unit\utils\source\SourceObjectInterface[]
     */
    private $objects = [];
    
    /**
     * @var string
     */
    private $path = null;
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setPath(string $value): SourceFileInterface
    {
        $this->path = $value;
        
        return $this;
    }
    
    public function addObject(SourceObjectInterface $object): SourceFileInterface
    {
        $this->objects[] = $object;
        
        return $this;
    }
    
    public function save(): bool
    {
        $this->prepareDirectory();
        
        return (bool)file_put_contents($this->getPath(), $this->render());
    }
    
    public function render(SourceFormatterInterface $formatter = null): string
    {
        $result    = "<?php\n";
        $namespace = null;
        
        if (is_string($this->getNamespace()) === true) {
            $namespace = $this->formatNamespace();
            $result .= 'namespace ' . $namespace . ";\n";
        }
        
        $listNamespaces = [];
        
        $objects = '';
        
        foreach ($this->objects as $object) {
            $usedNamespace = $object->getUsedNamespace();
            
            if (empty($usedNamespace) === false) {
                $listNamespaces = array_merge($listNamespaces, $usedNamespace);
            }
            
            $objects .= $object->render();
        }
        
        $listNamespaces = array_unique($listNamespaces);
        
        if (is_null($namespace) === false) {
            array_filter($listNamespaces, function ($value) use ($namespace) {
                return $value !== $namespace;
            });
        }
        
        if (empty($listNamespaces) === false) {
            $result .= "\n";
    
            foreach ($listNamespaces as $namespace) {
                $result .= 'use ' . $namespace . ";\n";
            }
            
            $result .= "\n";
        }
        
        $result .= $objects;
        
        return $result;
    }
    
    private function formatNamespace(): string
    {
        return trim($this->getNamespace(), '\\');
    }
    
    /**
     * Подготваливаем директорию для записи файла
     */
    private function prepareDirectory()
    {
        $directoryPath = dirname($this->getPath());
        
        if (file_exists($directoryPath) === false) {
            DirectoryHelper::createRecursive($directoryPath);
        }
    }
    
    public function getUsedNamespace(): array
    {
        return [];
    }
}