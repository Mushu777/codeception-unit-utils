<?php
namespace recyger\codeception\unit\utils\helpers;

class BitmaskHelper
{
    /**
     * Устанавливаем флаги
     *
     * @param int    $flags
     * @param int[] ...$flagBits
     *
     * @return int
     */
    public static function set(int &$flags, int ...$flagBits): int
    {
        foreach ($flagBits as $flagBit) {
            $flags |= $flagBit;
        }
        
        return $flags;
    }
    
    /**
     * Удаляем флаги
     *
     * @param int    $flags
     * @param int[] ...$flagBits
     *
     * @return int
     */
    public static function unset(int &$flags, int ...$flagBits): int
    {
        foreach ($flagBits as $flagBit) {
            if (self::isSet($flags, $flagBit) === true) {
                $flags &= ~$flagBit;
            }
        }
        
        return $flags;
    }
    
    /**
     * Проверяем на наличие всех флагов
     *
     * @param int    $flags
     * @param int[] ...$flagBits
     *
     * @return bool|null
     */
    public static function isSet(int $flags, int ...$flagBits)
    {
        $flagBits = self::compileFlagBits($flagBits);
        
        return ($flags & $flagBits) === $flagBits;
    }
    
    /**
     * Проверяем на наличие хотя бы одного флагов
     *
     * @param int    $flags
     * @param int[] ...$flagBits
     *
     * @return bool|null
     */
    public static function isSetAny(int $flags, int ...$flagBits)
    {
        $flagBits = self::compileFlagBits($flagBits);
        
        return ($flags & $flagBits) > 0;
    }
    
    private static function compileFlagBits(array $flagBits): int
    {
        return array_reduce($flagBits, function($flags, $flagBit){ return $flags | $flagBit; }, 0);
    }
}