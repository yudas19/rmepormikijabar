<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate an SVG QR code string.
     *
     * @param  string  $url  The content of the QR code.
     * @param  int  $size  The size of the QR code (default 100).
     * @return string Raw SVG markup.
     */
    public static function generateSvg(string $url, int $size = 100): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd
        );
        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }
}
