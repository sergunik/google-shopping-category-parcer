<?php
namespace GSCP\Tests;

use GSCP\GSCPService;
use PHPUnit\Framework\TestCase;

class SettersTraitTest extends TestCase
{

    public function testSetFilename(): void
    {
        $filename = 'test_filename.txt';
        if(file_exists($filename)) {
            unlink($filename);
        }

        $service = new GSCPService();
        $service->setFilename($filename)
            ->toArray();

        $this->assertFileExists($filename);
        unlink($filename);
    }

    public function testSetLocale(): void
    {
        $filename = 'test_locale.txt';
        if(file_exists($filename)) {
            unlink($filename);
        }

        $service = new GSCPService();
        $json = $service->setLocale('uk_UA')
            ->setFilename($filename)
            ->toJson();

        $this->assertStringContainsString('\u0456', $json); //ukrainian "і"
        unlink($filename);
    }

    public function testSetWrongLocale(): void
    {
        $this->expectException(\Exception::class);

        $service = new GSCPService();
        $service->setLocale('wrong_locale')
            ->toJson();
    }

    public function testSetColumns(): void
    {
        $filename = 'test_locale.txt';
        if(file_exists($filename)) {
            unlink($filename);
        }

        $service = new GSCPService();
        $result = $service->setFilename($filename)
            ->setColumns([
                'id',
                'name'
            ])
            ->toArray();
        $item = array_pop($result);

        $this->assertArrayHasKey('id', $item);
        $this->assertArrayNotHasKey('parentId', $item);
        unlink($filename);
    }
}
