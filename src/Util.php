<?php

declare(strict_types=1);

namespace Nicojust\Aoc;

use Generator;
use SplFileObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class Util
{
    public final const ENV = 'env';
    public final const DEFAULT_FILE = 'prod.txt';
    public final const DEFAULT_FILE_PATH_PART = '/input/';

    public final static function getFilePath(InputInterface $input, string $path): string
    {
        $env = $input->getArgument(self::ENV);

        $filePath = $path . self::DEFAULT_FILE_PATH_PART . self::DEFAULT_FILE;
        if ($env) {
            $filePath = sprintf('%s.txt', $path . self::DEFAULT_FILE_PATH_PART . $env);
        }

        return $filePath;
    }

    public final static function fileExists(InputInterface $input, OutputInterface $output, string $path): bool
    {
        $filesystem = new Filesystem();
        $filePath = self::getFilePath($input, $path);

        $exists = $filesystem->exists($filePath);
        if (!$exists) {
            $output->writeln(sprintf('<error>File not found at "%s"</error>', $filePath));
        }

        return $exists;
    }

    public final static function readLine(string $filePath): Generator
    {
        $fileObject = new SplFileObject($filePath, 'r');
        while (!$fileObject->eof()) {
            yield $fileObject->fgets();
        }

        $fileObject = null;
    }
}
