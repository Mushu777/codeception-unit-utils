<?php
namespace recyger\codeception\unit\utils\filters;

use recyger\codeception\unit\utils\helpers\BitmaskHelper;

class FilterPattern implements FilterPatternInterface
{
    public static $delimiter = '/';
    
    /**
     * Модификаторы регулярных выражений
     *
     * @var array
     */
    private static $modifiers = [
        'i' => self::PCRE_CASELESS,
        'm' => self::PCRE_MULTILINE,
        's' => self::PCRE_DOTALL,
        'x' => self::PCRE_EXTENDED,
        'A' => self::PCRE_ANCHORED,
        'D' => self::PCRE_DOLLAR_ENDONLY,
        'U' => self::PCRE_UNGREEDY,
        'X' => self::PCRE_EXTRA,
        'J' => self::PCRE_INFO_JCHANGED,
        'u' => self::PCRE_UTF8,
    ];
    
    /**
     * Шаблон для сравнения
     *
     * @var string|null
     */
    private $pattern = null;
    
    /**
     * Флаги для настройки шаблона
     *
     * @var int
     */
    private $flags = 0;
    
    public function __construct(string $pattern, int $flags = 0)
    {
        $this->pattern = $pattern;
        $this->flags   = $flags;
    }
    
    /**
     * @inheritdoc
     */
    public static function createFromString(string $pattern, int $flags = 0): FilterPatternInterface
    {
        list($pattern, $modifiers) = self::parseStringPattern($pattern);
        $flags |= $modifiers;
        
        return new static($pattern, $flags);
    }
    
    /**
     * Разбираем строку шаблона на шаблон и модификаторы
     *
     * @param string $pattern
     *
     * @return array
     * @throws \ErrorException
     */
    private static function parseStringPattern(string $pattern)
    {
        if (empty($pattern) === true) {
            throw new \ErrorException('The pattern can not be empty!');
        }
        
        $modifiers = 0;
        $matches   = [];
        
        if (preg_match(
                '#^(?P<separator>[\\\/\#\%\+\~\@\$])?(?P<pattern>.+?)(?P=separator)(?P<modifiers>[imsxADUXJu]*)$#',
                $pattern,
                $matches
            ) > 0
        ) {
            $pattern = $matches['pattern'];
            
            if (empty($matches['modifiers']) === false) {
                foreach (str_split($matches['modifiers']) as $modifier) {
                    if (isset(self::$modifiers[$modifier]) === false) {
                        throw new \ErrorException(sprintf(
                            'Unknown modifier \'%s\'',
                            $modifier
                        ));
                    }
                    
                    $modifiers |= self::$modifiers[$modifier];
                }
            }
        }
        
        if (strpos($pattern, '!') === 0) {
            $pattern = substr($pattern, 1);
            $modifiers |= self::NEGATIVE;
        }
        
        $matches = [];
        
        if (preg_match('#^[\\\/\^]+(?P<pattern>.*)$#', $pattern, $matches) > 0) {
            $pattern = $matches['pattern'];
            $modifiers |= self::FORM_START;
        }
        
        if (preg_match('#^(?P<pattern>.*)\$$#', $pattern, $matches) > 0) {
            $pattern = $matches['pattern'];
            $modifiers |= self::TO_END;
        }
        
        if (empty($pattern) === true) {
            throw new \ErrorException('The pattern can not be empty!');
        }
        
        $pattern = preg_replace(
            [
                '/([\+\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\-\.])/',
                '/(?:\\\\(?![\+\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\-\.])|\/)+/',
                '/\*/',
            ],
            [
                "\\\\$1",
                self::getDirectoryDelimiter(),
                '.*?',
            ],
            $pattern
        );
        
        if (self::checkPattern($pattern, $modifiers) === false) {
            throw new \ErrorException(sprintf(
                'Made is not valid regular expression: \'%s\'!',
                $pattern
            ));
        }
        
        return [$pattern, $modifiers];
    }
    
    /**
     * Проверка регулярного выражения на корректность
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return bool
     */
    private static function checkPattern(string $pattern, int $flags): bool
    {
        return @preg_match(self::preparePattern($pattern, $flags), null) !== false;
    }
    
    /**
     * Подготовка регулярного выражения
     *
     * @param string $pattern
     * @param int    $flags
     *
     * @return string
     */
    private static function preparePattern(string $pattern, int $flags): string
    {
        if (BitmaskHelper::isSet($flags, self::NO_STRICT_PATH_MATH) === true
            && preg_match_all(
                '/\[[\\\\\/]+\]/',
                $pattern,
                $matches,
                PREG_OFFSET_CAPTURE | PREG_SET_ORDER
            ) > 0
        ) {
            $offset = 0;
            
            foreach ($matches as $match) {
                $position = $match[0][1] + $offset;
                $pattern  = substr($pattern, 0, $position) . '(?:' . substr($pattern, $position) . ')?';
                $offset += 3;
            }
        }
        
        if (BitmaskHelper::isSet($flags, self::FORM_START) === true) {
            $pattern = '^' . $pattern;
        }
        
        if (BitmaskHelper::isSet($flags, self::TO_END) === true) {
            $pattern = $pattern . '$';
        }
        
        $pattern = self::$delimiter . $pattern . self::$delimiter;
        
        if ($flags > 0) {
            foreach (self::$modifiers as $modifier => $flag) {
                if (BitmaskHelper::isSet($flags, $flag) === true) {
                    $pattern .= $modifier;
                }
            }
        }
        
        return $pattern;
    }
    
    /**
     * Получение разделителя для директорий
     *
     * @return string
     */
    private static function getDirectoryDelimiter()
    {
        return '[' . preg_quote("\\\\/", self::$delimiter) . ']';
    }
    
    /**
     * @inheritdoc
     */
    public function match(string $subject): bool
    {
        $pattern = $this->getPattern();
        
        $result = preg_match($pattern, $subject);
        
        if ($result === false) {
            throw new \ErrorException(sprintf(
                'Error while parsing expression \'%s\'',
                $pattern
            ));
        }
        
        return $result === 1;
    }
    
    /**
     * @inheritdoc
     */
    public function isSetFlag(int $flag): bool
    {
        return BitmaskHelper::isSet($this->flags, $flag) === true;
    }
    
    /**
     * @inheritdoc
     */
    public function setFlag(int $flag): FilterPatternInterface
    {
        BitmaskHelper::set($this->flags, $flag);
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function unsetFlag(int $flag): FilterPatternInterface
    {
        BitmaskHelper::unset($this->flags, $flag);
        
        return $this;
    }
    
    /**
     * Подготовка шаблона к сравнению
     *
     * @return string
     */
    public function getPattern(): string
    {
        return self::preparePattern($this->pattern, $this->flags);
    }
    
    public function tetetet()
    {
    }
}