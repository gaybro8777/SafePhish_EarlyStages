<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/14/2016
 * Time: 8:50 AM
 */

namespace app;


use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use app\Exceptions\ConfigurationException;
use Symfony\Component\Validator\Exception\OutOfBoundsException;

class TemplateConfiguration
{
    private $template;
    private $templatePrefix;
    private $companyName;
    private $projectName;
    private $projectId;

    /**
     * TemplateConfiguration constructor
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     * @throws  ConfigurationException                  Custom Exception for any exception thrown in this class
     */
    public function __construct($templateSettings) {
        try {
            $this->areSettingsValid($templateSettings);
        } catch(Exception $e) {
            throw new ConfigurationException(__CLASS__ . ' Exception',0,$e);
        }
    }

    /**
     * checkFileExist
     * Checks if the file exists one of the two valid directories. IF it does, sets private variables.
     *
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     * @throws  FileNotFoundException
     */
    private function checkFileExist($templateSettings) {
        $path = '../resources/views/emails';
        $templateName = $templateSettings['templateName'];
        if(file_exists("$path/phishing/$templateName.blade.php")) {
            $this->templatePrefix = 'emails.phishing.';
        } else if(file_exists("$path/edu/$templateName.blade.php")) {
            $this->templatePrefix = 'emails.edu.';
        } else {
            throw new FileNotFoundException("Failed to find template: $templateName");
        }
    }

    /**
     * areSettingsValid
     * Checks to be sure that the settings are valid inputs for their respective objects.
     *
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     * @throws  OutOfBoundsException
     */
    private function areSettingsValid($templateSettings) {
        $message = '';
        if(var_export($templateSettings) || empty($templateSettings)) {
            throw new OutOfBoundsException('Expected array, received ' . get_class($templateSettings) . ' Object');
        }
        $this->validateSettingsKeys($templateSettings);
        $this->validateSettingsValues($templateSettings);
        $this->checkFileExist($templateSettings);
        $this->setSettings($templateSettings);
    }

    /**
     * validateSettingsValues
     * Checks the values of valid keys to make sure their values are valid.
     *
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     * @throws  InvalidArgumentException
     */
    private function validateSettingsValues($templateSettings) {
        $message = '';
        if($this->validateRegex($templateSettings['templateName']) || $templateSettings['templateName'] == '') {
            $message .= 'Template Name is not a valid input. Value provided: ' . var_export($templateSettings['templateName'],true) . PHP_EOL;
        }
        if($this->validateRegex($templateSettings['companyName']) || $templateSettings['companyName'] == '') {
            $message .= 'Company Name is not a valid input. Value provided: ' . var_export($templateSettings['companyName'],true) . PHP_EOL;
        }
        if($this->validateRegex($templateSettings['projectName']) || $templateSettings['projectName'] == '') {
            $message .= 'Project Name is not a valid input. Value provided: ' . var_export($templateSettings['projectName'],true) . PHP_EOL;
        }
        if($this->validateRegex($templateSettings['projectId'])) {
            $message .= 'Project ID is not a valid input. Value provided: ' . var_export($templateSettings['projectId'],true) . PHP_EOL;
        }
        if(!empty($message)) {
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * validateSettingsKeys
     * Checks the keys of the template settings to make sure that they have been set.
     *
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     * @throws  InvalidArgumentException
     */
    private function validateSettingsKeys($templateSettings) {
        $message = '';
        if(is_null($templateSettings['templateName'])) {
            $message .= 'Template Name value cannot be null.' . PHP_EOL;
        }
        if(is_null($templateSettings['companyName'])) {
            $message .= 'Company Name value cannot be null.' . PHP_EOL;
        }
        if(is_null($templateSettings['projectName'])) {
            $message .= 'Project Name value cannot be null.' . PHP_EOL;
        }
        if(is_null($templateSettings['projectId'])) {
            $message .= 'Project ID value cannot be null.' . PHP_EOL;
        }
        if(!empty($message)) {
            throw new OutOfBoundsException($message);
        }
    }

    /**
     * validateRegex
     * Helper function to validate input of template setting values via RegEx.
     *
     * @param   mixed          $value              Value to check against pattern.
     * @return  mixed
     */
    private function validateRegex($value) {
        return filter_var($value,FILTER_VALIDATE_REGEXP,
            array('options'=>array('regexp'=>';(?:[a-zA-z0-9-])(?![/][\^]);')));
    }

    /**
     * setSettings
     * Helper function to set all the settings
     *
     * @param   array           $templateSettings       Template Name, Template Path Prefix, Company Name, Project Name, Project ID
     */
    private function setSettings($templateSettings) {
        $this->template = $templateSettings['templateName'];
        $this->companyName = $templateSettings['companyName'];
        $this->projectName = $templateSettings['projectName'];
        $this->projectId = $templateSettings['projectId'];
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getCompanyName() {
        return $this->companyName;
    }

    public function getProjectName() {
        return $this->projectName;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    /**
     * getTemplateTargetType
     * Returns whether the template is a Targeted or Generic phishing scam.
     *
     * @return string
     */
    public function getTemplateTargetType() {
        $db = new DBManager();
        $sql = "SELECT PRJ_TargetType FROM gaig_users.projects WHERE PRJ_ProjectId = ?;";
        $bindings = array($this->projectId);
        $data = $db->query($sql,$bindings);
        $result = $data->fetch();
        return $result['PRJ_TargetType'];
    }

    /**
     * getTemplateComplexityType
     * Returns whether the template is an Advanced or Basic phishing scam.
     *
     * @return string
     */
    public function getTemplateComplexityType() {
        $db = new DBManager();
        $sql = "SELECT PRJ_ComplexityType FROM gaig_users.projects WHERE PRJ_ProjectId = ?;";
        $bindings = array($this->projectId);
        $data = $db->query($sql,$bindings);
        $result = $data->fetch();
        return $result['PRJ_ComplexityType'];
    }

    public function getValidUsers($returnUsers, $periodInWeeks) {
        $db = new DBManager();
        $sql = "SELECT * FROM gaig_users.users;";
        $users = $db->query($sql,array(),array('\PDO::ATTR_CURSOR'),array('\PDO::CURSOR_SCROLL'));
        $usersIterator = new PDOIterator($users);
        foreach($usersIterator as $user) {
            $tempUser = new User_Test($user);
            $tempUser->pushUser($returnUsers,$periodInWeeks,$this);
        }
        return $returnUsers;
    }
}