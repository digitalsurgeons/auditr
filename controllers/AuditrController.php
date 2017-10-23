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

    private function _generatePDF($html)
    {
        craft()->tasks->createTask('Auditr','Generate PDF', ['html' => $html]);
        craft()->userSession->setNotice(Craft::t('Auditr: PDF generation started.'));
    }
}
