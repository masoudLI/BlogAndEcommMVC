<?php

namespace Tests\Framework;

use Framework\UploadImage;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadImageTest extends TestCase
{
    private $upload;

    public function setUp(): void
    {
        parent::setUp();
        $this->upload = new UploadImage('tests');
    }


    public function tearDown(): void
    {
        if (file_exists('tests/demo.jpg')) {
            unlink('tests/demo.jpg');
        }
    }


    public function testUpload()
    {

        $uploadFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');

        $uploadFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests\demo.jpg'));
            
        $this->assertEquals('demo.jpg', $this->upload->upload($uploadFile));
    }

    public function testUploadWithExsistingFile()
    {

        $uploadFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');

        touch('tests/demo.jpg');

        $uploadFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('tests\demo_copy.jpg'));

        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadFile));
    }
}
