<?php
// app/core/I18n.php
declare(strict_types=1);

final class I18n
{
    private static string $lang = 'en';
    private static array $map = [];
    private static array $whitelist = ['en','si','ta'];

    public static function boot(string $basePath, string $lang): void
    {
        self::$lang = in_array($lang, self::$whitelist, true) ? $lang : 'en';

        $en  = require $basePath . '/app/lang/en.php';
        $loc = [];
        $locFile = $basePath . '/app/lang/' . self::$lang . '.php';
        if (is_file($locFile)) $loc = require $locFile;

        // Build replacement map (English -> Localized)
        $map = [];
        foreach ($en as $k => $v) if (is_string($k) && is_string($v) && $k !== '') $map[$k] = $v;
        foreach ($loc as $k => $v) if (is_string($k) && is_string($v) && $k !== '') $map[$k] = $v;

        self::$map = $map;
    }

    public static function getLang(): string { return self::$lang; }

    // Output buffer that replaces only visible text nodes between tags
    public static function bufferStart(): void
    {
        ob_start(function (string $buffer): string {
            if (self::$lang === 'en' || empty(self::$map)) return $buffer;

            $out = preg_replace_callback('/>([^<]+)</u', function ($m) {
                $txt = $m[1];

                $lead = $trail = '';
                if (preg_match('/^\s+/u', $txt, $lm)) $lead = $lm[0];
                if (preg_match('/\s+$/u', $txt, $tm)) $trail = $tm[0];
                $core = trim($txt);
                if ($core === '') return '><';

                if (isset(self::$map[$core])) {
                    return '>' . $lead . self::$map[$core] . $trail . '<';
                }

                $translated = $core;
                foreach (self::$map as $en => $loc) {
                    if ($en !== '' && $en !== $loc && mb_strpos($translated, $en) !== false) {
                        $translated = str_replace($en, $loc, $translated);
                    }
                }
                return '>' . $lead . $translated . $trail . '<';
            }, $buffer);

            return $out ?? $buffer;
        });
    }
}
