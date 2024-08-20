<?php

//TODO организуйте файловую структуру src, вынесите файлы касающиеся движка в app  или engine core, game, blog

function main(): string
{
    $config = getConfig();

    if (!$config) {
        return handleError("Невозможно подключить файл настроек");
    }

    $functionName = parseCommand();

    if (function_exists($functionName)) {
        $result = $functionName();
    } else {
        $result = handleError("Вызываемая функция не существует");
    }

    return $result;
}

function parseCommand(): string
{
    $functionName = 'helpFunction';

    //TODO реализовать addPost добавить пост в интерактивном режиме, addrandompost создаст случайный пост
    if (isset($_SERVER['argv'][1])) {
        $functionName = match ($_SERVER['argv'][1]) {
            'rand' => 'randFunction',
            'posts' => 'getPosts',
            'post' => 'getPost',
            'addpost' => 'addPost',
            'addrandompost' => 'addRandomPost',
            default => 'helpFunction'
        };
    }

    return $functionName;
}

function helpFunction(): string
{
    return handleHelp();
}

function readConfig(string $configAddress): array|false
{
    return parse_ini_file($configAddress, true);
}

function getConfig(): array
{
    static $config = [];

    if (empty($config)) {
        $config = readConfig(getcwd() . '/config.ini');
    }

    return $config;
}
