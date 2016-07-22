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

    private function checkURLId($projectId) {
        if(is_null($this->uniqueURLId)) {
            $db = new DBManager();
            $this->uniqueURLId = RandomObjectGeneration::random_str(15) . $projectId;
            $sql = "UPDATE gaig_users.users SET USR_UniqueURLId=? WHERE USR_UserId=?;";
            $bindings = array($this->uniqueURLId,$this->id);
            $db->query($sql,$bindings);
        }
    }

    public function pushUser($validUsers, $periodInWeeks, TemplateConfiguration $templateConfig) {
        try {
            $this->checkURLId($templateConfig->getProjectId());
            if($this->isValid($periodInWeeks,$templateConfig)) {
                $validUsers[] = $this;
            }
        } catch(Exception $e) {

        }
    }

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
            throw new Exception();
        }
    }

    private function checkPeriod($date,$timestamp) {
        return $timestamp <= $date;
    }

    private function checkMRP(Project $mrp, $complexity, $target) {
        return $complexity == $mrp->getTemplateComplexityType() &&
            $target == $mrp->getTemplateTargetType();
    }

    private function checkPP(Project $mrp, Project $pp, $complexity) {
        return !is_null($pp) &&
            $complexity == $mrp->getTemplateComplexityType() &&
            $complexity == $pp->getTemplateComplexityType();
    }

    private function checkLP(Project $mrp, Project $pp, Project $lp, $target) {
        return !is_null($lp) &&
            !is_null($pp) &&
            $target == $mrp->getTemplateTargetType() &&
            $target == $pp->getTemplateTargetType() &&
            $target == $lp->getTemplateTargetType();
    }
}