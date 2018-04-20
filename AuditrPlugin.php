<?php
/**
 * Auditr plugin for Craft CMS
 *
 * Audit your Craft CMS installation
 *
 * @author    Digital Surgeons
 * @copyright Copyright (c) 2017 Digital Surgeons
 * @link      https://www.digitalsurgeons.com/
 * @package   Auditr
 * @since     0.0.1
 */

namespace Craft;

class AuditrPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Auditr');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Audit your Craft CMS installation');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/digitalsurgeons/auditr/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/digitalsurgeons/auditr/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '0.1.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '0.0.1';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Digital Surgeons';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://www.digitalsurgeons.com/';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function defineSettings()
    {
        return [
            'googleApiKey' => AttributeType::String,
            'siteUrl'     => AttributeType::String
        ];
    }

    /**
     * @return string
     */
    public function getSettingsUrl()
    {
       return 'auditr/settings';
    }

    /**
     * @return array
     */
    public function registerCpRoutes()
    {
        return [
            'auditr/convert' => ['action' => 'auditr/convert'],
            'auditr/download' => ['action' => 'auditr/download']
        ];
    }
}
