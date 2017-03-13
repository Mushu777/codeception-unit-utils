<?php
namespace recyger\codeception\unit\utils\managers;

class ComposerManager implements PathManagerInterface
{
    /**
     * Рабочая директория
     *
     * @var string
     */
    private $workDirectory = null;
    
    /**
     * Пространство имен исходного кода
     *
     * @var string
     */
    private $sourceNamespace = null;
    
    /**
     * Пространство имен тестов
     *
     * @var string
     */
    private $testsNamespace = null;
    
    /**
     * Путь до исходного кода
     *
     * @var string
     */
    private $sourcePath = null;
    
    /**
     * Путь до тестов
     *
     * @var string
     */
    private $testsPath = null;
    
    public function __construct(string $workDirectory)
    {
        $this->workDirectory = $workDirectory;
        $this->includeAutoload($this->process());
    }
    
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }
    
    public function getTestPath(): string
    {
        return $this->testsPath;;
    }
    
    public function getSourceNamespace(): string
    {
        return $this->sourceNamespace;
    }
    
    public function getTestNamespace(): string
    {
        return $this->testsNamespace;
    }
    
    private function process(): array
    {
        $options = [];
        $composerJsonPath = $this->workDirectory . DIRECTORY_SEPARATOR . 'composer.json';
        
        if (file_exists($composerJsonPath) === false) {
            $this->fileNoFoundException($composerJsonPath);
        }
        
        $composerJson = json_decode(file_get_contents($composerJsonPath), true);
        
        if (is_null($composerJson) === true) {
            throw new \ErrorException(sprintf(
                'The data from the file \'%s\' could not be read!',
                $composerJsonPath
            ));
        }
        
        if (empty($composerJson['autoload']) === true || is_array($composerJson['autoload']) === false) {
            $this->unexpectedValueException('autoload', $composerJsonPath);
            
        }
        
        if (empty($composerJson['autoload']['psr-4']) === true || is_array($composerJson['autoload']['psr-4']) === false) {
            $this->unexpectedValueException('autoload.psr-4', $composerJsonPath);
            
        }
        
        reset($composerJson['autoload']['psr-4']);
        $this->sourceNamespace = trim(trim(key($composerJson['autoload']['psr-4'])), '\\');
        $this->sourcePath      = realpath(
            $this->workDirectory
            . DIRECTORY_SEPARATOR
            . current($composerJson['autoload']['psr-4'])
        );
        
        if (is_string($this->sourceNamespace) === false || empty($this->sourceNamespace) === true) {
            $this->unexpectedValueException('autoload.psr-4', $composerJsonPath);
        }
        
        if (is_string($this->sourcePath) === false || empty($this->sourcePath) === true) {
            $this->unexpectedValueException('autoload.psr-4', $composerJsonPath);
        }
        
        if (empty($composerJson['autoload-dev']) === true || is_array($composerJson['autoload-dev']) === false) {
            $this->unexpectedValueException('autoload-dev', $composerJsonPath);
        }
        
        if (empty($composerJson['autoload-dev']['psr-4']) === true || is_array($composerJson['autoload-dev']['psr-4']) === false) {
            $this->unexpectedValueException('autoload-dev.psr-4', $composerJsonPath);
        }
        
        reset($composerJson['autoload-dev']['psr-4']);
        $this->testsNamespace = trim(trim(key($composerJson['autoload-dev']['psr-4'])), '\\');
        $this->testsPath      = realpath(
            $this->workDirectory
            . DIRECTORY_SEPARATOR
            . current($composerJson['autoload-dev']['psr-4'])
        );
        
        if (is_string($this->testsNamespace) === false || empty($this->testsNamespace) === true) {
            $this->unexpectedValueException('autoload-dev.psr-4', $composerJsonPath);
        }
        
        if (is_string($this->testsPath) === false || empty($this->testsPath) === true) {
            $this->unexpectedValueException('autoload-dev.psr-4', $composerJsonPath);
        }
        
        if (empty($composerJson['require']['yiisoft/yii2']) === false) {
            $options[] = 'yiisoft/yii2';
        }
        
        return $options;
    }
    
    private function unexpectedValueException(string $item, string $filePath)
    {
        throw new \ErrorException(sprintf(
            'Item \'%s\' from file \'%s\' has unexpected value!',
            $item,
            $filePath
        ));
    }
    
    private function fileNoFoundException(string $filePath)
    {
        throw new \ErrorException(sprintf(
            'File \'%s\' not found!',
            $filePath
        ));
    }
    
    private function includeAutoload(array $options)
    {
        $autoloadPath = $this->workDirectory . DIRECTORY_SEPARATOR . 'vendor' .DIRECTORY_SEPARATOR . 'autoload.php';
        
        if (file_exists($autoloadPath) === false) {
            $this->fileNoFoundException($autoloadPath);
        }
        
        $autoload = require_once $autoloadPath;
        //TODO: сделать автоматический поиск не коректных фреймворков
        foreach ($options as $option) {
            switch ($option){
                case 'yiisoft/yii2':
                    $prefixesPsr4 = $autoload->getPrefixesPsr4();
                    
                    if (empty($prefixesPsr4['yii\\'][0]) === false) {
                        require_once $prefixesPsr4['yii\\'][0] . DIRECTORY_SEPARATOR . 'Yii.php';
                    }
                    break;
            }
        }
    }
}