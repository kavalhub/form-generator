<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigViewFactory
{
    public function __construct(
        private readonly ?string $cachePath = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function renderFile(string $path, array $data): string
    {
        $resolved = realpath($path);
        if ($resolved !== false) {
            $path = $resolved;
        }

        $loader = new FilesystemLoader(dirname($path));
        $twig = new Environment($loader, [
            'cache' => $this->resolveCache($this->cachePath ?? sys_get_temp_dir() . '/form-generator-twig'),
            'auto_reload' => true,
        ]);

        return $twig->load(basename($path))->render($data);
    }

    /**
     * @return string|false
     */
    private function resolveCache(string $cache): string|false
    {
        if ($this->ensureWritableCacheDirectory($cache)) {
            return $cache;
        }

        return false;
    }

    private function ensureWritableCacheDirectory(string $cache): bool
    {
        if (!is_dir($cache) && !@mkdir($cache, 0777, true) && !is_dir($cache)) {
            return false;
        }

        @chmod($cache, 0777);

        return is_dir($cache) && is_writable($cache);
    }
}
