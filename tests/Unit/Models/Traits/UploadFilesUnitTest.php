<?php

namespace Tests\Unit\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Stubs\Models\Traits\UploadFileStub;
use Tests\TestCase;

class UploadFilesUnitTest extends TestCase
{

    private $obj;

    protected function setUp(): void{
        parent::setUp();
        $this->obj = new UploadFileStub();
    }

    protected function teardown(): void{
        parent::teardown();

    }

    public function testUploadfile(){
        Storage::fake("");
        $file = UploadedFile::fake()->create("video.mp4");
        $this->obj->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadfiles(){
        Storage::fake("");
        $file = UploadedFile::fake()->create("video.mp4");
        $file2 = UploadedFile::fake()->create("video2.mp4");
        $this->obj->uploadFiles([$file,$file2]);
        Storage::assertExists("1/{$file->hashName()}");
        Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteFile(){
        Storage::fake();
        $file = UploadedFile::fake()->create("video.mp4");
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file->hashName());
        Storage::assertMissing("1/{$file->hashName()}");

        $file = UploadedFile::fake()->create("video.mp4");
        $this->obj->uploadFile($file);
        $this->obj->deleteFile($file);
        Storage::assertMissing("1/{$file->hashName()}");

    }

    public function testDeletefiles(){
        Storage::fake("");
        $file = UploadedFile::fake()->create("video.mp4");
        $file2 = UploadedFile::fake()->create("video2.mp4");
        $files=[$file,$file2];
        $this->obj->uploadFiles($files);
        $this->obj->deleteFiles([$file->hashName(),$file2]);

        Storage::assertMissing("1/{$file->hashName()}");
        Storage::assertMissing("1/{$file2->hashName()}");
    }

    public function testExtractFiles(){
        $attributes = [];
        $files = UploadFileStub::extracFiles($attributes);

        $this->assertCount(0,$attributes);
        $this->assertCount(0,$files);

        $attributes = ['file1'=>'test'];
        $files = UploadFileStub::extracFiles($attributes);

        $this->assertCount(1,$attributes);
        $this->assertEquals($attributes,['file1'=>'test']);
        $this->assertCount(0,$files);

        $attributes = ['file1'=>'test','file2'=>'test'];
        $files = UploadFileStub::extracFiles($attributes);

        $this->assertCount(2,$attributes);
        $this->assertEquals($attributes,['file1'=>'test','file2'=>'test']);
        $this->assertCount(0,$files);

        $file1 = UploadedFile::fake()->create("video.mp4");

        $attributes = ['file1'=>$file1,'file2'=>'test'];
        $files = UploadFileStub::extracFiles($attributes);

        $this->assertCount(2,$attributes);
        $this->assertEquals($attributes,['file1'=>$file1->hashName(),'file2'=>'test']);

        $this->assertCount(1,$files);
        $this->assertEquals($files,[$file1]);

        $file1 = UploadedFile::fake()->create("video.mp4");
        $file2 = UploadedFile::fake()->create("video2.mp4");
        $attributes = ['file1'=>$file1,'file2'=>$file2,'other'=>'test'];
        $files = UploadFileStub::extracFiles($attributes);

        $this->assertCount(3,$attributes);
        $this->assertEquals($attributes,['file1'=>$file1->hashName(),'file2'=>$file2->hashName(),'other'=>'test']);

        $this->assertCount(2,$files);
        $this->assertEquals($files,[$file1,$file2]);
    }




}
