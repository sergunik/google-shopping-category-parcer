<?php
namespace GSCP\Tests;

use GSCP\GSCPService;
use PHPUnit\Framework\TestCase;

class DownloadTraitTest extends TestCase
{
    public function testDownload()
    {
        $filename = 'test_name.txt';
        if(file_exists($filename)) {
            unlink($filename);
        }

        $service = new GSCPService(['filename' => $filename]);
        $service->toArray();

        $this->assertFileExists($filename);
        unlink($filename);
    }
}
