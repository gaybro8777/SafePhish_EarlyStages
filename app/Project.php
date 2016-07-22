<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/20/2016
 * Time: 12:39 PM
 */

namespace app;


class Project
{
    private $id;
    private $name;
    private $complexity;
    private $target;
    private $assignee;
    private $startDate;
    private $lastActiveDate;
    private $status;
    private $totalParticipants;
    private $emailViewCount;
    private $websiteViewCount;
    private $totalReportsCount;

    public function __construct($project)
    {
        $this->id = $project['PRJ_ProjectId'];
        $this->name = $project['PRJ_ProjectName'];
        $this->complexity = $project['PRJ_ComplexityType'];
        $this->target = $project['PRJ_TargetType'];
        $this->assignee = $project['PRJ_ProjectAssignee'];
        $this->startDate = $project['PRJ_ProjectStart'];
        $this->lastActiveDate = $project['PRJ_ProjectLastActive'];
        $this->status = $project['PRJ_ProjectStatus'];
        $this->totalParticipants = $project['PRJ_ProjectTotalUsers'];
        $this->emailViewCount = $project['PRJ_EmailViews'];
        $this->websiteViewCount = $project['PRJ_WebsiteViews'];
        $this->totalReportsCount = $project['PRJ_ProjectTotalReports'];
    }

    public function getTemplateComplexityType() {
        return $this->complexity;
    }

    public function getTemplateTargetType() {
        return $this->target;
    }
}