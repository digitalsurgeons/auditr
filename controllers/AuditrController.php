<?php

namespace Craft;

class AuditrController extends BaseController
{
    protected $allowAnonymous = [];

    public function actionConvert()
    {
        $html = $this->renderTemplate('auditr/export', array(), true);
        $this->_generatePDF($html);
        $this->redirect('auditr');
    }

    public function actionDownload()
    {
        $file = IOHelper::getFileContents(__DIR__ . '/../resources/Auditr.pdf');
        HeaderHelper::setHeader([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename=Auditr.pdf'
        ]);
        echo $file;
    }

    private function _generatePDF($html)
    {
        craft()->tasks->createTask('Auditr','Generate PDF', ['html' => $html]);
        craft()->userSession->setNotice(Craft::t('Auditr: PDF generation started.'));
    }
}
