<?php

namespace App\Library\File;

use Exception;
use TCPDF;

class PdfLibrary
{
    /**
     * サンプルPDFの出力(ファイルリソース)
     *
     * @return string
     * @throws Exception
     */
    public static function getSamplePDF(): string
    {
        $tcpdf = new TCPDF();
        $tcpdf->AddPage();
        $tcpdf->SetFont("kozgopromedium", "", 10);
        $html = <<< EOF
        <style>
        body {
            color: #212121;
        }
        </style>
        <h1>header</h1>
        <p>
        sample text.
        </p>
        <p>
        contents.
        </p>
        EOF;

        $tcpdf->writeHTML($html);
        return $tcpdf->Output('sample.pdf', 'I');
    }
}
