<?php

if (! function_exists('truncateText')) {
    function truncateText(string $text, int $limit = 200, string $url = null): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $short = mb_substr($text, 0, $limit);

        $short = mb_substr($short, 0, (int)mb_strrpos($short, ' '));

        if ($url) {
            return $short . '... <a href="' . $url . '" class="text-blue-500 hover:underline">Read more</a>';
        }

        return $short . '...';
    }
}

if (! function_exists('isActiveByUrl')) {
    function isActiveByUrl(array $params, Smarty_Internal_Template $template): string
    {
        $current = $template->getTemplateVars('currentPath');
        $path = $params['path'] ?? '';
        $exact = $params['exact'] ?? false;

        $activeClass = $params['active']
            ?? 'text-black font-semibold border-b-2 border-black pb-1';

        $inactiveClass = $params['inactive']
            ?? 'text-gray-600 hover:text-black';

        if ($exact) {
            return $current === $path ? $activeClass : $inactiveClass;
        }

        return (str_starts_with($current, $path))
            ? $activeClass
            : $inactiveClass;
    }
}