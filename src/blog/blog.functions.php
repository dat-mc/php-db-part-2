<?php

function addRandomPost()
{
    $faker = Faker\Factory::create();

    echo $faker->realtext(100);


    return "";
}

function getPost(): string
{
    $config = getConfig();

    $db = @dbConnect($config);

    if (!isset($_SERVER['argv'][2])) {
        return handleError('Не передан ID поста');
    }

    if (!is_numeric($_SERVER['argv'][2])) {
        return handleError('ID поста должен быть числом');
    }

    $id = (int)$_SERVER['argv'][2];

    $result = @pg_prepare($db, "select", "select id, title, preview from public.\"Posts\" where id = $1;");

    $result = @pg_execute($db, "select", [$id]);

    if (!$result) {
        return handleError("Ошибка запроса "  . pg_last_error($db));
    }

    $row = pg_fetch_assoc($result);

    if (!$row) {
        return handleError('Пост не найден');
    }

    $table = "+------+-------------------------------+-------------------------------+\n";
    $table .= sprintf("|%5.5s |%-30.30s |%-30.30s |\n", 'ID', 'title', 'preview');
    $table .= "+------+-------------------------------+-------------------------------+\n";

    $table .= sprintf("|%5.5s |%-30.30s |%-30.30s |\n", $row['id'], $row['title'], $row['preview']);

    $table .= "+------+-------------------------------+-------------------------------+\n";

    return $table;
}

function getPosts(): string
{
    $config = getConfig();
    $db = @dbConnect($config);

    $result = @pg_query($db, "select id, title, preview from public.\"Posts\";");

    if (!$result) {
        return handleError("Ошибка запроса "  . pg_last_error($db));
    }

    $rows = pg_fetch_all($result);

    $table = "+------+-------------------------------+-------------------------------+\n";
    $table .= sprintf("|%5.5s |%-30.30s |%-30.30s |\n", 'ID', 'title', 'preview');
    $table .= "+------+-------------------------------+-------------------------------+\n";

    foreach ($rows as $row) {
        $table .= sprintf("|%5.5s |%-30.30s |%-30.30s |\n", ...array_values($row));
    }

    $table .= "+------+-------------------------------+-------------------------------+\n";

    return $table;
}
