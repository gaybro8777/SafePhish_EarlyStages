<?php namespace App;

use app\Exceptions\QueryException;
use app\PDOIterator;
use Illuminate\Support\Facades\Mail;
use PhpSpec\Exception\Example\FailureException;
use Psy\Exception\FatalErrorException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Exception\OutOfBoundsException;
use app\EmailConfiguration;
use app\TemplateConfiguration;
use app\Exceptions\EmailException;

class Email {

    private static $templateConfig;
    private static $emailConfig;

    /**
     * executeEmail
     * Public-facing method to send an email to a database of users if they are a valid recipient.
     *
     * @param   EmailConfiguration          $emailConfig            Email Configuration object containing required information to send an email
     * @param   TemplateConfiguration       $templateConfig         Template Configuration object containing required information to build a template
     * @param   int                         $periodInWeeks          Number of weeks to check back for recipient validation
     * @throws  EmailException                                      Custom Exception to embody any exceptions thrown in this class
     */
    public static function executeEmail(
        EmailConfiguration $emailConfig,
        TemplateConfiguration $templateConfig)
    {
        self::setTemplateConfig($templateConfig);
        self::setEmailConfig($emailConfig);

        try {
            foreach($emailConfig->getUsers() as $user) {
                self::sendEmail($user);
                self::updateUserProjects($user);
            }
        } catch(Exception $e) {
            throw new EmailException(__CLASS__ . ' Exception',0,$e);
        }
    }

    /**
     * updateUserProjects
     * Updates the user with the newest project and rotates the old projects down one.
     *
     * @param   array           $user           User array extracted from PDOStatement
     */
    private function updateUserProjects($user) {
        $db = new DBManager();
        $sql = "UPDATE gaig_users.users SET USR_ProjectMostRecent=?, USR_ProjectPrevious=?, 
                    USR_ProjectLast=? WHERE USR_Username=?;";
        $bindings = array($this->templateConfig->getProjectName(),$user['USR_ProjectMostRecent'],$user['USR_ProjectPrevious'],$user['USR_Username']);
        $db->query($sql,$bindings);
    }

    /**
     * sendEmail
     * Iterates through the PDO Result Set of users. Calls validRecipientAlgo to validate user. Sends email if
     *      valid and updates user if valid.
     *
     * @param   array           $user           User array extracted from PDOStatement
     * @throws  FailureException
     */
    private static function sendEmail($user) {
        $templateData = array(
            'companyName'=>self::$templateConfig->getCompanyName(),
            'projectName'=>self::$templateConfig->getProjectName(),
            'projectId'=>self::$templateConfig->getProjectId(),
            'lastName'=>$user['USR_LastName'],
            'username'=>$user['USR_Username'],
            'urlId'=>$user['USR_UniqueURLId']
        );
        $subject = self::$emailConfig->getSubject();
        $from = self::$emailConfig->getFromEmail();
        $to = $user['USR_Email'];
        $mailResult = Mail::send(
            ['html' => self::$templateConfig->getTemplate()],
            $templateData,
            function($m) use ($from, $to, $subject) {
                $m->from($from);
                $m->to($to)
                    ->subject($subject);
            }
        );
        if(!$mailResult) {
            throw new FailureException('Email failed to send to ' . $to . ', from ' . $from);
        }
    }

    private static function setTemplateConfig(TemplateConfiguration $templateConfig) {
        self::$templateConfig = $templateConfig;
    }

    private static function setEmailConfig(EmailConfiguration $emailConfig) {
        self::$emailConfig = $emailConfig;
    }
}
