<?php

namespace App\Library\File;

use Exception;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeLibrary
{
    // font-family
    public const FONT_FAMILY_KOZGOPROMEDIUM = 'kozgopromedium';
    // font-size
    public const FONT_SIZE_10 = 10;

    /**
     * サンプルPDFの出力(ファイルリソース)
     *
     * @return string
     * @throws Exception
     */
    public static function getSampleQrCode(): string
    {
        $options = new QROptions(
            [
              'eccLevel' => QRCode::ECC_L,
              'outputType' => QRCode::OUTPUT_MARKUP_SVG,
              'version' => 5,
            ]
        );

        $qrcode = (new QRCode($options))->render('http://localhost');

        $html = <<< EOF
        <style>
        body {
            color: #212121;
        }
        </style>
        <img src='$qrcode' alt='QR Code' width='800' height='800'>
        EOF;

        return $html;
    }
}
