<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorageImageUrlTest extends TestCase
{
    #[Test]
    public function it_returns_the_public_url_when_the_image_exists_on_the_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/test.jpg', 'test-image');

        $this->assertSame(
            Storage::disk('public')->url('products/test.jpg'),
            storage_image_url('products/test.jpg')
        );
    }

    #[Test]
    public function it_copies_images_from_the_local_disk_to_the_public_disk_when_needed(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('products/local-test.jpg', 'test-image');

        $url = storage_image_url('products/local-test.jpg');

        $this->assertNotNull($url);
        $this->assertStringContainsString('storage/products/local-test.jpg', $url);
        $this->assertTrue(Storage::disk('public')->exists('products/local-test.jpg'));
    }
}
