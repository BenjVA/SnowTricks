<?php

namespace App\Service;

use PHPUnit\Util\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function add(UploadedFile $image, ?string $directory = ''): string
    {
        $file = md5(uniqid(rand(), true)) . '.jpeg';

        $imageInfos = getimagesize($image);

        if ($imageInfos === false) {
            throw new Exception('Format d\'image incorrect');
        }

        match ($imageInfos['mime']) {
            'image/png' => imagecreatefrompng($image),
            'image/jpeg' => imagecreatefromjpeg($image),
            'image/webp' => imagecreatefromwebp($image),
            default => throw new Exception('Format d\'image incorrect, vous devez utiliser des fichiers en .jpeg, .png ou .webp'),
        };

        $path = $this->parameters->get('images_directory') . $directory;

        if (!file_exists($path . '/tricks/')) {
            mkdir($path . '/tricks/', 0755, true);
        }

        $image->move($path . '/tricks', $file);

        return $file;
    }
}
