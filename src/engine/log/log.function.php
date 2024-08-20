<?php
//функция для примера, осуществляющая вывод текстовой информации в файл

function _log(mixed $data): void
{
    if (is_array($data) || is_object($data)) $data = print_r($data, 1);

    $fileHandler = fopen(getcwd() . '/storage/logs.txt', 'a');

    fputs($fileHandler, $data . PHP_EOL);

    fclose($fileHandler);
}
