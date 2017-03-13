<?php
namespace recyger\codeception\unit\utils\filters;

use ErrorException;

class SourceFilter implements PathFilterInterface
{
    /**
     * @var FilterPatternInterface
     */
    public $filterPatternClass = FilterPattern::class;
    
    /**
     * Выходное значение фильрации по умолчанию
     *
     * @var bool
     */
    public $defaultResult = true;
    
    /**
     * @var array
     */
    private $patterns = null;
    
    /**
     * Filter constructor.
     *
     * @param array|string $patterns
     *
     * @param bool         $defaultResult
     *
     * @throws \ErrorException
     */
    public function __construct($patterns = null, bool $defaultResult = true)
    {
        $this->defaultResult = $defaultResult;
        $this->addStringPatterns('*.php$');
        $this->addStringPatterns('!.git');
        
        if (is_null($patterns) === false) {
            if (is_string($patterns) === true) {
                $patterns = explode(',', $patterns);
            }
            
            if (is_array($patterns) === false) {
                throw new ErrorException(
                    'The variable $list of templates has not a valid type! Can only be an array or a string.'
                );
            }
            
            $this->preparePatterns($patterns);
        }
        
    }
    
    /**
     * @inheritdoc
     */
    public function accept(string $path): bool
    {
        //По умолчанию считаем что путь проходит фильтрацию
        $result = $this->defaultResult;
    
        //Частичная оптимизация проверяем только те шаблоны которые не смогут изменить результат
        foreach ($this->patterns as $pattern) {
            //Если результат положительный и фильтр негативный проверяем на соответствие шаблону
            if ($result === true && $pattern->isSetFlag(FilterPatternInterface::NEGATIVE) === true) {
                if ($pattern->match($path) === true) {
                    $result = false;
                }
                //если результат отрицательный и фильтр положительный проверяем на соответствие шаблону
            } elseif ($result === false && $pattern->isSetFlag(FilterPatternInterface::NEGATIVE) === false) {
                if ($pattern->match($path) === true) {
                    $result = true;
                }
            }
        }
    
        return $result;
    }
    
    public function addStringPatterns(string ...$patterns): PathFilterInterface
    {
        $this->preparePatterns($patterns);
        
        return $this;
    }
    
    /**
     * Подготовка списков включенных/исключенных директорий/файлов из/в проверки
     *
     * @param array $patterns
     *
     * @throws ErrorException
     */
    private function preparePatterns(array $patterns)
    {
        foreach ($patterns as $pattern) {
            if (is_string($pattern) === true) {
                $pattern = $this->createPattern($pattern);
            }
            
            if (($pattern instanceof FilterPatternInterface) === false) {
                throw new ErrorException('Handed the wrong pattern!');
            }
            
            $this->patterns[] = $pattern;
        }
    }
    
    /**
     * Создание экземпляра шаблона
     *
     * @param string $pattern
     *
     * @return FilterPatternInterface
     */
    private function createPattern(string $pattern): FilterPatternInterface
    {
        $class = $this->filterPatternClass;
        
        return $class::createFromString($pattern);
    }
}