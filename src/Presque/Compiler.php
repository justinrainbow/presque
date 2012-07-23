<?php

/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presque;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles Presque into a phar.
 *
 * Based _heavily_ on the the Composer compiler class, just customized to
 * be used with Presque.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * @link https://github.com/composer/composer/blob/master/src/Composer/Compiler.php
 */
class Compiler
{
    /**
     * Compiles satis into a single phar file
     *
     * @param string $pharFile The full path to the file to create
     *
     * @throws \RuntimeException
     */
    public function compile($pharFile = 'presque.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, basename($pharFile));
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finders = array();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*')
            ->notName('Compiler.php')
            ->in(__DIR__)
        ;
        $finders[] = $finder;

        $vendorDir = __DIR__.'/../../vendor/';

        $finders[] = Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('*.xsd')
            ->notName('*Test.php')
            ->in(array(
                $vendorDir.'symfony',
                $vendorDir.'predis/predis/lib',
                $vendorDir.'monolog/monolog/src',
                $vendorDir.'composer',
            ));

        $this->addFile($phar, $vendorDir.'autoload.php');

        foreach ($finders as $finder) {
            foreach ($finder as $file) {
                $this->addFile($phar, $file);
            }
        }

        $this->addConsole($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        $phar->compressFiles(\Phar::GZ);

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../LICENSE'), false);

        unset($phar);
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', is_string($file) ? realpath($file) : $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $phar->addFromString($path, $content);
    }

    private function addConsole($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/presque');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $phar->addFromString('bin/presque', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of the Presque package.
 *
 * (c) Justin Rainbow <justin.rainbow@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('presque.phar');

require 'phar://presque.phar/bin/presque';

__HALT_COMPILER();
EOF;
    }
}