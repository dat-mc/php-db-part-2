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

function apiGetPosts(): string
{
    $config = getConfig();
    $db = @dbConnect($config);

    $result = @pg_query($db, "SELECT id, title, preview from public.\"Posts\";");

    if (!$result) {
        return json_encode("Ошибка запроса "  . pg_last_error($db));
    }

    return json_encode([
        'message' => 'Посты успешно получены',
        'data' => pg_fetch_all($result),
    ]);
}

function apiGetPostById(): string
{
    $config = getConfig();
    $db = @dbConnect($config);

    $id = (int) strip_tags($_GET['id']);

    $result = @pg_prepare($db, "select", "SELECT id, title, preview from public.\"Posts\" where id=$1;");

    $result = @pg_execute($db, "select", [$id]);

    if (!$result) {
        return json_encode("Ошибка запроса "  . pg_last_error($db));
    }

    return json_encode([
        'message' => 'Пост успешно получен',
        'data' => pg_fetch_all($result)[0],
    ]);
}

function apiCreatePost()
{
    $config = getConfig();
    $db = @dbConnect($config);

    $title = strip_tags($_POST['title']);
    $text = strip_tags($_POST['text']);
    $preview = strip_tags($_POST['preview']);
    $userId = strip_tags($_POST['userId']);
    $categoryId = strip_tags($_POST['categoryId']);

    $result = @pg_prepare($db, "insert", "
        INSERT INTO public.\"Posts\" (title, \"text\", preview, user_id, category_id)
        VALUES ($1, $2, $3, $4, $5)
    ");

    $result = @pg_execute($db, "insert", [$title, $text, $preview, $userId, $categoryId]);

    if (!$result) {
        return json_encode("Ошибка запроса "  . pg_last_error($db));
    }

    return json_encode([
        'message' => 'Создан новый пост',
        'data' => [
            'title' => $title,
            'text' => $text,
            'preview' => $preview,
            'userId' => $userId,
            'categoryId' => $categoryId,
        ]
    ]);
}

function apiUpdatePost()
{
    $config = getConfig();
    $db = @dbConnect($config);

    $id = (int) strip_tags($_POST['id']);
    $title = strip_tags($_POST['title']);
    $text = strip_tags($_POST['text']);
    $preview = strip_tags($_POST['preview']);
    $userId = (int) strip_tags($_POST['userId']);
    $categoryId = (int) strip_tags($_POST['categoryId']);

    $result = @pg_prepare($db, "update", "
        UPDATE public.\"Posts\"
        SET title=$2,
            category_id=$6,
            user_id=$5,
            \"text\"=$3,
            preview=$4
        WHERE id=$1
    ");

    $result = @pg_execute($db, "update", [$id, $title, $text, $preview, $userId, $categoryId]);

    if (!$result) {
        return json_encode("Ошибка запроса "  . pg_last_error($db));
    }

    return json_encode([
        'message' => 'Обновлен пост',
        'data' => [
            'id' => $id,
            'title' => $title,
            'text' => $text,
            'preview' => $preview,
            'userId' => $userId,
            'categoryId' => $categoryId,
        ]
    ]);
}

function apiDeletePost(): string
{
    $config = getConfig();
    $db = @dbConnect($config);

    $id = (int) strip_tags($_GET['id']);

    $result = @pg_prepare($db, "delete", "DELETE FROM public.\"Posts\" where id=$1;");

    $result = @pg_execute($db, "delete", [$id]);

    if (!$result) {
        return json_encode("Ошибка запроса "  . pg_last_error($db));
    }

    return json_encode([
        'message' => 'Пост успешно удален',
    ]);
}
