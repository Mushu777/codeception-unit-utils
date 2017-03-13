<?php
namespace recyger\codeception\unit\utils\filters;

interface FilterPatternInterface
{
    const FORM_START          = 0x1;
    const NEGATIVE            = 0x2;
    const NO_STRICT_PATH_MATH = 0x4;
    const TO_END              = 0x8;
    
    const PCRE_MULTILINE      = 0x1000;
    const PCRE_CASELESS       = 0x2000;
    const PCRE_EXTENDED       = 0x4000;
    const PCRE_EXTRA          = 0x8000;
    const PCRE_DOTALL         = 0x10000;
    const PCRE_UTF8           = 0x20000;
    const PCRE_UNGREEDY       = 0x40000;
    const PCRE_ANCHORED       = 0x80000;
    const PCRE_INFO_JCHANGED  = 0x100000;
    const PCRE_DOLLAR_ENDONLY = 0x200000;
    
    
    /**
     * Создание шаблона фильтрации для из строки
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return \recyger\codeception\unit\utils\filters\FilterPatternInterface
     */
    public static function createFromString(string $pattern, int $flags = 0): FilterPatternInterface;
    
    /**
     * Проверка на соответствие шаблону
     *
     * @param string $subject
     *
     * @return bool
     * @throws \ErrorException
     */
    public function match(string $subject): bool;
    
    /**
     * Проверяем установлен ли флаг для шаблона
     *
     * @param int $flag
     *
     * @return bool
     */
    public function isSetFlag(int $flag): bool;
    
    /**
     * Устанавливаем флаг для шаблона
     *
     * @param int $flag
     *
     * @return \recyger\codeception\unit\utils\filters\FilterPatternInterface
     */
    public function setFlag(int $flag): FilterPatternInterface;
    
    /**
     * Убираем флаг для шаблона
     *
     * @param int $flag
     *
     * @return \recyger\codeception\unit\utils\filters\FilterPatternInterface
     */
    public function unsetFlag(int $flag): FilterPatternInterface;
    
    /**
     * Подготовка шаблона к сравнению
     *
     * @return string
     */
    public function getPattern(): string;
}