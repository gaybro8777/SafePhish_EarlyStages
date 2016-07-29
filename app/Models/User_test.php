<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/19/2016
 * Time: 2:34 PM
 */

namespace app\Models;


use app\Libraries\RandomObjectGeneration;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\OutOfBoundsException;

class User_test
{

    private $id;
    private $username;
    private $email;
    private $firstName;
    private $lastName;
    private $password;

    private $date;

    /**
     * User_test constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->validateSettingsKeys($user);
        $this->validateSettingsValues($user);
        $this->setVariables($user);
    }

    /**
     * setVariables
     * Sets the variables based on an associative array from a PDOStatement.
     *
     * @param   array       $user           Associative array of user data
     */
    private function setVariables($user) {
        $this->id = $user['USR_Id'];
        $this->username = $user['USR_Username'];
        $this->email = $user['USR_Email'];
        $this->firstName = $user['USR_FirstName'];
        $this->lastName = $user['USR_LastName'];
        $this->password = $user['USR_Password'];
    }

    /**
     * validateSettingsValues
     * Checks the values of valid keys to make sure their values are valid.
     *
     * @param   array           $user               User associative array
     * @throws  InvalidArgumentException
     */
    private function validateSettingsValues($user) {
        $message = '';
        if(!is_numeric($user['USR_UserId'])) {
            $message .= 'USR_UserId is not a valid integer. Value provided: ' . var_export($user['USR_UserId'],true) . PHP_EOL;
        }
        if($this->alphanumericValidation($user['USR_Username'])) {
            $message .= 'USR_Username is not a valid alphanumeric value. Value provided: ' . var_export($user['USR_Username']) . PHP_EOL;
        }
        if(!filter_var($user['USR_Email'],FILTER_VALIDATE_EMAIL)) {
            $message .= 'USR_Email is not a valid email address. Value provided: ' . var_export($user['USR_Email'],true) . PHP_EOL;
        }
        if($this->alphabeticalValidation($user['USR_FirstName'])) {
            $message .= 'USR_FirstName is not a valid alphabetical value. Value provided: ' . var_export($user['USR_FirstName'],true) . PHP_EOL;
        }
        if($this->alphabeticalValidation($user['USR_LastName'])) {
            $message .= 'USR_LastName is not a valid alphabetical value. Value provided: ' . var_export($user['USR_LastName'],true) . PHP_EOL;
        }
        if(!is_null($user['USR_UniqueURLId'])) {
            if($this->alphanumericValidation($user['USR_UniqueURLId'])) {
                $message .= 'USR_UniqueURLId is not a valid alphanumeric value. Value provided: ' . var_export($user['USR_UniqueUrlId'],true) . PHP_EOL;
            }
        }
        if(!empty($message)) {
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * validateSettingsKeys
     * Checks the keys of the user associative array to make sure that they have been set.
     *
     * @param   array           $user               User associative array
     * @throws  InvalidArgumentException
     */
    private function validateSettingsKeys($user) {
        $message = '';
        if(is_null($user['USR_UserId'])) {
            $message .= 'USR_UserId value cannot be null.' . PHP_EOL;
        }
        if(is_null($user['USR_Username'])) {
            $message .= 'USR_Username value cannot be null.' . PHP_EOL;
        }
        if(is_null($user['USR_Email'])) {
            $message .= 'USR_Email value cannot be null.' . PHP_EOL;
        }
        if(is_null($user['USR_FirstName'])) {
            $message .= 'USR_FirstName value cannot be null.' . PHP_EOL;
        }
        if(is_null($user['USR_LastName'])) {
            $message .= 'USR_LastName value cannot be null.' . PHP_EOL;
        }
        if(is_null($user['USR_Password'])) {
            $message .= 'Subject value cannot be null.' . PHP_EOL;
        }
        if(!empty($message)) {
            throw new OutOfBoundsException($message);
        }
    }

    /**
     * alphanumericValidation
     * Regex validator to check for alphanumeric expression.
     *
     * @param   string          $string         String to check against regex pattern
     * @return  int
     */
    private function alphanumericValidation($string) {
        return preg_match('/[^a-z_\-0-9]/i',$string);
    }

    /**
     * alphabeticalValidation
     * Regex validator to check for alphabetical expression.
     *
     * @param   string          $string         String to check against regex pattern
     * @return  int
     */
    private function alphabeticalValidation($string) {
        return preg_match('/[^a-z\-]/i',$string);
    }

    /**
     * checkURLId
     * Checks if UniqueURLId is null and sets it if it is.
     *
     * @param   int         $projectId          Integer ID referencing specific project to be concatenated onto the URLId
     */
    private function checkURLId($projectId) {
        if(is_null($this->uniqueURLId)) {
            $db = new DBManager();
            $this->uniqueURLId = RandomObjectGeneration::random_str(15) . $projectId;
            $sql = "UPDATE gaig_users.users SET USR_UniqueURLId=? WHERE USR_UserId=?;";
            $bindings = array($this->uniqueURLId,$this->id);
            $db->query($sql,$bindings);
        }
    }

    /**
     * pushUser
     * Pushes $this onto the provided array if it is valid.
     *
     * @param   array                   $validUsers             Array of User_test objects
     * @param   int                     $periodInWeeks          Period to check in validation of user
     * @param   TemplateConfiguration   $templateConfig         Template Configuration for validation
     */
    public function pushUser($validUsers, $periodInWeeks, TemplateConfiguration $templateConfig) {
        try {
            $this->checkURLId($templateConfig->getProjectId());
            if($this->isValid($periodInWeeks,$templateConfig)) {
                $validUsers[] = $this;
            }
        } catch(Exception $e) {
            //implementation to come
        }
    }

    /**
     * isValid
     * Verifies the user is valid according to the verification algorithm defined in the check... functions.
     *
     * @param   int                     $periodInWeeks          Period to check in validation of user
     * @param   TemplateConfiguration   $templateConfig         Template Configuration for validation
     * @return  bool
     */
    private function isValid($periodInWeeks, TemplateConfiguration $templateConfig) {
        try {
            $db = new DBManager();
            $sql = "
                SELECT MAX(SML_SentTimestamp) AS 'timestamp_check' 
                FROM gaig_users.sent_email 
                WHERE SML_UserId = ? AND SML_ProjectName = ?;";
            $bindings = array($this->id,$this->mostRecentProject);
            $data = $db->query($sql,$bindings);
            if($data->rowCount() > 0) {
                $result = $data->fetch();
                $this->date = date('Y-m-d',strtotime('-' . $periodInWeeks . ' weeks')) . ' 00:00:00';
                if($this->checkPeriod($this->date,$result['timestamp_check'])) {
                    return true;
                }
                $sql = "SELECT * FROM gaig_users.projects WHERE PRJ_ProjectId = ?;";
                $data = $db->query($sql,array($this->mostRecentProject));
                $mostRecentProj = new Project($data->fetch());
                $newComplexity = $templateConfig->getTemplateComplexityType();
                $newTarget = $templateConfig->getTemplateTargetType();
                if($this->checkMRP($mostRecentProj,$newComplexity,$newTarget)) {
                    return false;
                }
                $data = $db->query($sql,array($this->previousProject));
                $previousProj = new Project($data->fetch());
                if($this->checkPP($mostRecentProj,$previousProj,$newComplexity)) {
                    return false;
                }
                $data = $db->query($sql,array($this->lastProject));
                $lastProj = new Project($data->fetch());
                if($this->checkLP($mostRecentProj,$previousProj,$lastProj,$newTarget)) {
                    return false;
                }
            }
            return true;
        } catch(Exception $e) {
            //unsure how to manage any exceptions thrown yet, if at all. further design to come
        }
    }

    /**
     * checkPeriod - Verification Algorithm
     * Verifies if the period is outside of periodInWeeks zone.
     *
     * @param   string          $date               Date in format 'Y-m-d h:i:s'
     * @param   string          $timestamp          Date retrieved from PDOStatement
     * @return  bool
     */
    private function checkPeriod($date,$timestamp) {
        return $timestamp <= $date;
    }

    /**
     * checkMRP - Verification Algorithm
     * Checks the Most Recent Project to see if identical.
     *
     * @param   Project         $mrp            Project object representing the Most Recent Project
     * @param   string          $complexity     Complexity type of requested template
     * @param   string          $target         Target type of requested template
     * @return  bool
     */
    private function checkMRP(Project $mrp, $complexity, $target) {
        return $complexity == $mrp->getTemplateComplexityType() &&
            $target == $mrp->getTemplateTargetType();
    }

    /**
     * checkPP - Verification Algorithm
     * Checks the Previous Project and Most Recent Project for identical complexity type.
     *
     * @param   Project         $mrp            Project object representing the Most Recent Project
     * @param   Project         $pp             Project object representing the Previous Project
     * @param   string          $complexity     Complexity type of requested template
     * @return  bool
     */
    private function checkPP(Project $mrp, Project $pp, $complexity) {
        return !is_null($pp) &&
            $complexity == $mrp->getTemplateComplexityType() &&
            $complexity == $pp->getTemplateComplexityType();
    }

    /**
     * checkLP - Verification Algorithm
     * Checks the Last Project, Previous Project, and Most Recent Project for identical target type.
     *
     * @param   Project         $mrp            Project object representing the Most Recent Project
     * @param   Project         $pp             Project object representing the Previous Project
     * @param   Project         $lp             Project object representing the Last Project
     * @param   string          $target         Target type of requested template
     * @return  bool
     */
    private function checkLP(Project $mrp, Project $pp, Project $lp, $target) {
        return !is_null($lp) &&
            !is_null($pp) &&
            $target == $mrp->getTemplateTargetType() &&
            $target == $pp->getTemplateTargetType() &&
            $target == $lp->getTemplateTargetType();
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }
}