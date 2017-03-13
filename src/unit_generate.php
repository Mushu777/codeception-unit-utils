<?php
use recyger\codeception\unit\utils\filters\SourceFilter;
use recyger\codeception\unit\utils\managers\ComposerManager;
use recyger\codeception\unit\utils\TestManager;
use recyger\codeception\unit\utils\TestMethodsFinder;

/**
 * Справка по работе утилиты
 *
 * @return string
 */
function getHelp()
{
    return <<<HELP
Утилита проверяет директорию с исходным кодом на наличие тестов для этого кода в директории с кодом тестов. Возможны включение и исключение кода для проверки. Работа ведется на основе данных компосера.
\nНе обязательные аргументы:
\n-w -\tРабочая директория от которой будет производится поиск кода.
\tПо умолчанию директория запуска скрипта.
\nНе обязательные опции:
\n-s -\tСуффикс для сгенерированных тестов (что бы не затирать существующие тесты). 
\n-f -\tПути исключенные/включенный из/в проверки. 
\tПеречисляется через запятую.
\tДопустимы только относительные пути.
\tДопустимы использование регулярных выражений.
\tПо умолчанию пусто
\n-h -\tЭта справка
HELP;
}


$options = array_merge([
    'w' => null,
    'f' => null,
    'h' => null,
    's' => null,
], getopt('w::f::s::h'));

if ($options['h'] === false) {
    echo getHelp();
    exit(0);
}

require_once __DIR__ . '/../vendor/autoload.php';

$workDirectory = rtrim(realpath($options['w'] ?: getcwd()), '\\/');
$finder        = new TestMethodsFinder(new ComposerManager($workDirectory), new SourceFilter($options['f'], false));
$manager       = new TestManager();

if (is_null($options['s']) === false) {
    $manager->fileSuffix = $options['s'];
}

$methods       = $finder->getNotExists();
if ($manager->createTestMethods($methods) === true) {
    echo 'Done!';
} else {
    echo 'Fail!';
    exit(-1);
}