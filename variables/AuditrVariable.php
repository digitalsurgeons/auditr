<?php
namespace Craft;

class AuditrVariable
{
    public function pdfExists()
    {
        if (IOHelper::fileExists(__DIR__ . '/../resources/Auditr.pdf')) {
            return true;
        } else {
            return false;
        }
    }

    public function getSettings()
    {
        return craft()->plugins->getPlugin('auditr')->getSettings();
    }

    public function getInfo()
    {
        $info = array(
            'siteName' => craft()->siteName,
            'siteUrl' => craft()->siteUrl
        );
        return $info;
    }

    public function getSectionsByType()
    {
        $query = craft()->db->createCommand()
            ->select('s.*, count(*) as entryCount')
            ->from('sections as s')
            ->leftJoin('entries as e', 's.id=e.sectionId')
            ->group('s.id,e.sectionId')
            ->order('s.type asc, s.name asc')
            ->queryAll();

        $sections = array();

        $sections['Channels'] = array_filter($query, function ($section) {
            return $section['type'] == 'channel';
        });

        $sections['Singles'] = array_filter($query, function ($section) {
            return $section['type'] == 'single';
        });

        $sections['Structures'] = array_filter($query, function ($section) {
            return $section['type'] == 'structure';
        });

        return $sections;
    }

    public function getPlugins()
    {
        return craft()->plugins->getPlugins();
    }

    public function getUsers()
    {
        $groups = array();

        $userGroups = craft()->db->createCommand()
            ->select('*')
            ->from('usergroups')
            ->queryAll();

        foreach($userGroups as $group) {
            $query = craft()->db->createCommand()
                ->select('*')
                ->from('usergroups_users uu')
                ->join('users as u', 'uu.userId=u.id')
                ->where(array('uu.groupId' => $group['id']))
                ->queryAll();
            $groups[$group['name']] = $query;
        }

        $groups['No Group'] = craft()->db->createCommand()
            ->select('u.*')
            ->from('users u')
            ->leftJoin('usergroups_users as uu', "u.id = uu.userId")
            ->where('uu.userId IS NULL')
            ->queryAll();
        return $groups;
    }

    public function getServerInfo()
    {
        return array(
          'os' => PHP_OS,
          'software' => $_SERVER['SERVER_SOFTWARE'],
          'phpVersion' => PHP_VERSION,
          'documentRoot' => $_SERVER['DOCUMENT_ROOT'],
          'craftVersion' => craft()->version
        );
    }

    public function getEntriesInfo()
    {
        $entriesCount = craft()->db->createCommand()
            ->select('COUNT(*) AS count')
            ->from('entries')
            ->queryRow();

        $entryCountByAuthor = craft()->db->createCommand()
            ->select('COUNT(*) as count, u.username')
            ->from('entries e')
            ->leftJoin('users u', 'u.id = e.authorId')
            ->group('u.username')
            ->order('count desc')
            ->queryAll();

        $draftsCount = craft()->db->createCommand()
            ->select('COUNT(*)')
            ->from('entrydrafts')
            ->queryRow();

        $mostRecentEntry = craft()->db->createCommand()
            ->select('dateCreated')
            ->from('entries')
            ->order('dateCreated desc')
            ->limit(1)
            ->queryRow();

        return array(
            'count' => $entriesCount['count'],
            'entryCountByAuthor' => $entryCountByAuthor,
            'draftsCount' => count($draftsCount),
            'mostRecentEntry' => $mostRecentEntry
        );
    }

    public function getHtaccess()
    {
        return file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/.htaccess");
    }

    public function getRoutes()
    {
        $query = craft()->db->createCommand()
            ->select('urlParts, urlPattern, template')
            ->from('routes')
            ->queryAll();

        return array_map(function($row) {
            return array(
                'urlParts' => (array) json_decode($row['urlParts']),
                'urlPattern' => $row['urlPattern'],
                'template' => $row['template']
            );
        }, $query);
    }

    public function getGlobals()
    {
        $globalSets = array();
        foreach(craft()->globals->getAllSets() as $globalSet) {
            $fieldLayoutFields = $globalSet->getFieldLayout()->getFields();
            $globalSets[$globalSet['name']] = array();
            foreach ($fieldLayoutFields as $fieldLayoutField) {
                $globalSets[$globalSet['name']][] = craft()->fields->getFieldById($fieldLayoutField->fieldId);
            }
        }
        return $globalSets;
    }

    public function getUpdates()
    {
        return craft()->updates->getUpdates();
    }
}
