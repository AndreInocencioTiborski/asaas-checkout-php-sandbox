<?php
final class Env {

    private static array $vars = [];

    public static function load(string $path):void {

        foreach(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            
            $line = trim($line);

            if($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$chave, $valor] = explode('=', $line, 2);
            self::$vars[trim($chave)] = trim(trim($valor), "'\"");
        }
    }

    public static function get(string $key, ?string $default = null): ?string {
        return self::$vars[$key] ?? $default;
    }
}