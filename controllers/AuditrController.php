<?php

namespace Craft;


class AuditrController extends BaseController
{
    protected $allowAnonymous = [];

    public function __construct()
    {
        require __DIR__ . '/../vendor/autoload.php';
    }    

    public function actionDownload()
    {
        $html = $this->renderTemplate('auditr/export', array(), true);
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('Auditr.pdf');
    }
}
