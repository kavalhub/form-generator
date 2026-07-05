<?php
declare(strict_types=1);

namespace Kavalhub\FormGenerator\Blade;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;

final class BladeViewFactory
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

        $filesystem = new Filesystem();
        $cache = $this->cachePath ?? sys_get_temp_dir() . '/form-generator-blade';
        if (!is_dir($cache) && !mkdir($cache, 0777, true) && !is_dir($cache)) {
            throw new \RuntimeException('Cannot create Blade cache directory: ' . $cache);
        }

        $compiler = new BladeCompiler($filesystem, $cache);
        $engine = new CompilerEngine($compiler, $filesystem);

        return $engine->get($path, $data);
    }
}
