<?php

namespace Craft;

class AuditrTask extends BaseTask
{
    public function __construct()
    {
        require __DIR__ . '/../vendor/autoload.php';
    }

    public function getDescription()
    {
        return Craft::t('Generating PDF');
    }

    public function getTotalSteps()
    {
        return 1;
    }

    public function runStep($step)
    {
        $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
        $html2pdf->writeHTML($this->settings->html);
        $html2pdf->output(__DIR__ . "/../resources/Auditr.pdf", "F");
        return true;
    }

    protected function defineSettings()
    {
        return [
            'html' => AttributeType::String
        ];
    }
}
