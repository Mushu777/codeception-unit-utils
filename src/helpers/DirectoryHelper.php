<?php
namespace recyger\codeception\unit\utils\helpers;

class DirectoryHelper
{
    const DIRECTORY_PERMISSION = 0774;
    
    const PACKAGE_DIRECTORY_NAME = 'projects';
    
    public static function formatPathFromName(string $name) : string
    {
        $name = implode(DIRECTORY_SEPARATOR, explode('/', $name));
        
        return ConfigurationHelper::getUserHome()
        . DIRECTORY_SEPARATOR
        . self::PACKAGE_DIRECTORY_NAME
        . DIRECTORY_SEPARATOR
        . $name;
    }
    
    public static function deleteRecursive(string $path) : bool
    {
        $recursiveDirectoryIterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $recursiveIteratorIterator  = new \RecursiveIteratorIterator(
            $recursiveDirectoryIterator,
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($recursiveIteratorIterator as $fileInfo) {
            $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileInfo->getRealPath());
        }
        
        return rmdir($path);
    }
    
    /**
     * @param string|array $path
     * @param int          $permission
     *
     * @return bool
     */
    public static function createRecursive($path, int $permission = self::DIRECTORY_PERMISSION) : bool
    {
        if (is_array($path)) {
            $path = self::arrayToString($path);
        }
        
        if (!is_string($path)) {
            ComposerHelper::fatalError('The "$path" must be a string');
        }
        
        if (file_exists($path) === true) {
            ComposerHelper::fatalError(sprintf('The path "%s" already exists!', $path));
        }
        
        return @mkdir($path, $permission, true);
    }
    
    /**
     * @param array $path
     *
     * @return string
     */
    private static function arrayToString(array $path): string
    {
        return implode(DIRECTORY_SEPARATOR, $path);
    }
}
