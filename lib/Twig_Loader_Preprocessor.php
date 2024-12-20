<?php

use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Twig Preprocessor loader that allows adding custom text filters for template strings.
 *
 * For instance, you can make Twig produce more readable output by stripping leading
 * spaces in lines with single control structure or comment:
 *
 * $loader = new Twig_Loader_Preprocessor($realLoader,
 *     function ($template) {
 *         return preg_replace('/^[ \t]*(\{([#%])[^}]*(?2)\})$/m', '$1', $template);
 *     }
 * );
 *
 * See also twig issue #1005: https://github.com/fabpot/Twig/issues/1005
 *
 * @author Igor Tarasov <tarasov.igor@gmail.com>
 */
class Twig_Loader_Preprocessor implements LoaderInterface
{
    private $realLoader;
    private $callback;

    /**
     * Constructor
     *
     * Callback should accept template string as the only argument and return the result
     *
     * @param Twig_LoaderInterface $loader A loader that does real loading of templates
     * @param callable $callback The processing callback
     */
    public function __construct(LoaderInterface $loader, $callback)
    {
        $this->realLoader = $loader;
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name): Source
    {
        $realSource = $this->realLoader->getSourceContext($name);

        return new Source(
            call_user_func($this->callback, $realSource->getCode()), $realSource->getName(), $realSource->getPath()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->realLoader->exists((string)$name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name): string
    {
        return $this->realLoader->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(string $name, int $time): bool
    {
        return $this->realLoader->isFresh($name, $time);
    }
}
