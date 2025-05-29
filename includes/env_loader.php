<?php

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception("Ficheiro .env não encontrado no caminho: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Ignora comentários e linhas vazias
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Só processa se tiver '='
        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // remove espaços e aspas

        // Define variáveis de ambiente
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}