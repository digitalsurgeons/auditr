<?php

namespace Craft;

class AuditrTask extends BaseTask
{
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
        if ($step == 0) {
            try {
                $html2pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'en');
                $html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first page');
                $html2pdf->output(__DIR__ . '/../../../storage/runtime/temp/Auditr.pdf', 'F');
            } catch (Exception $e) {
                IOHelper::writeToFile("/Users/dylan/AuditrError.txt", print_r($e));
                exit;
            }
            return true;
        } else {
            return true;
        }
    }
}
