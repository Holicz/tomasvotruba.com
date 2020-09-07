<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2018\Exceptions;

use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use SplFileInfo as NativeSplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class RelativePathTest extends TestCase implements PostTestInterface
{
    public function testSplFileInfo(): void
    {
        // on purpose
        $splFileInfo = new NativeSplFileInfo('packages/blog/tests/Posts/Year2018/Exceptions/Source/some_file.txt');

        // is relative
        $this->assertSame(
            'packages/blog/tests/Posts/Year2018/Exceptions/Source/some_file.txt',
            $splFileInfo->getPathname()
        );

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $splFileInfo->getRealPath());

        // is relative
        /** @var string $realPath */
        $realPath = $splFileInfo->getRealPath();
        $relativePath = Strings::substring($realPath, strlen(getcwd()) + 1);
        $this->assertSame('packages/blog/tests/Posts/Year2018/Exceptions/Source/some_file.txt', $relativePath);
    }

    public function testSymfonyFinder(): void
    {
        $finder = Finder::create()->files()
            ->in(__DIR__ . '/Source');

        $files = iterator_to_array($finder->getIterator());
        $this->assertCount(1, $files);

        /** @var SplFileInfo $file */
        $file = array_pop($files);

        // is relative to directory finder looked into
        // @see Symfony: https://github.com/symfony/symfony/blob/00e5cd9a1c237e579e6327e9a66c512bf76f292a/src/Symfony/Component/Finder/Iterator/RecursiveDirectoryIterator.php#L73
        $this->assertSame('some_file.txt', $file->getRelativePathname());

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $file->getPathname());

        // is absolute
        $this->assertSame(__DIR__ . '/Source/some_file.txt', $file->getRealPath());

        // is relative
        $relativePath = Strings::substring($file->getRealPath(), strlen(getcwd()) + 1);

        $this->assertSame('packages/blog/tests/Posts/Year2018/Exceptions/Source/some_file.txt', $relativePath);
    }

    public function getPostId(): int
    {
        return 141;
    }
}
