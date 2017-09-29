<?php declare(strict_types=1);

namespace Shopware\Media\Extension;

use Shopware\Media\Struct\MediaBasicStruct;
use Shopware\Media\Struct\ThumbnailStruct;
use Shopware\Media\UrlGeneratorInterface;
use Shopware\ProductMedia\Event\ProductMediaBasicLoadedEvent;
use Shopware\ProductMedia\Extension\ProductMediaExtension as CoreProductMediaExtension;
use Shopware\ProductMedia\Struct\ProductMediaBasicStruct;

class ProductMediaThumbnailExtension extends CoreProductMediaExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function productMediaBasicLoaded(ProductMediaBasicLoadedEvent $event): void
    {
        /** @var ProductMediaBasicStruct $productMedia */
        foreach ($event->getProductMedias() as $productMedia) {
            $media = $productMedia->getMedia();

            if (!$media) {
                continue;
            }

            $this->addThumbnails($media);
        }
    }

    public function createThumbnailStruct(string $filename, int $width, int $height, bool $isHighDpi = false): ThumbnailStruct
    {
        $pathinfo = pathinfo($filename);
        $basename = $pathinfo['filename'];
        $extension = $pathinfo['extension'];

        $filename = $basename . '_' . $width . 'x' . $height;

        if ($isHighDpi) {
            $filename .= '@2x';
        }

        $thumbnail = new ThumbnailStruct();
        $thumbnail->setFileName($filename . '.' . $extension);
        $thumbnail->setWidth($width);
        $thumbnail->setHeight($height);
        $thumbnail->setHighDpi($isHighDpi);
        $thumbnail->setUrl(
            $this->urlGenerator->getUrl($thumbnail->getFileName())
        );

        return $thumbnail;
    }

    private function addThumbnails(MediaBasicStruct $media): void
    {
        if ($media->getAlbum()->getCreateThumbnails() === false) {
            return;
        }

        $thumbnailSizes = explode(';', $media->getAlbum()->getThumbnailSize());
        $thumbnails = [];

        foreach ($thumbnailSizes as $size) {
            list($width, $height) = explode('x', $size);

            $width = (int) $width;
            $height = (int) $height;

            $thumbnails[] = $this->createThumbnailStruct($media->getFileName(), $width, $height);

            if ($media->getAlbum()->getThumbnailHighDpi()) {
                $thumbnails[] = $this->createThumbnailStruct($media->getFileName(), $width, $height, true);
            }
        }

        $media->setThumbnails($thumbnails);
    }
}