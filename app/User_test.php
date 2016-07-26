<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/19/2016
 * Time: 2:34 PM
 */

namespace app;


use app\Libraries\RandomObjectGeneration;
use Symfony\Component\Config\Definition\Exception\Exception;
use MongoDB\BSON\Timestamp;

class User_test
{

    private $id;
    private $username;
    private $email;
    private $firstName;
    private $lastName;
    private $uniqueURLId;
    private $password;
    private $mostRecentProject;
    private $previousProject;
    private $lastProject;

    private $date;

    /**
     * User_test constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->id = $user['USR_UserId']; //required
        $this->username = $user['USR_Username']; //required
        $this->email = $user['USR_Email']; //required
        $this->firstName = $user['USR_FirstName']; //required
        $this->lastName = $user['USR_LastName']; //required
        $this->uniqueURLId = $user['USR_UniqueURLId'];
        $this->password = $user['USR_Password']; //required
        $this->mostRecentProject = $user['USR_ProjectMostRecent'];
        $this->previousProject = $user['USR_ProjectPrevious'];
        $this->lastProject = $user['USR_ProjectLast'];
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

    public function getUniqueURLId() {
        return $this->uniqueURLId;
    }

    public function getEmail() {
        return $this->email;
    }
}