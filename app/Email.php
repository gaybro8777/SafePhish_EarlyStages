<?php namespace App;

use App\Exceptions\QueryException;
use App\Models\Sent_Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use PhpSpec\Exception\Example\FailureException;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\EmailConfiguration;
use App\TemplateConfiguration;
use App\Exceptions\EmailException;

class Email {

    private static $templateConfig;
    private static $emailConfig;

    /**
     * executeEmail
     * Public-facing method to send an email to a database of users if they are a valid recipient.
     *
     * @param   EmailConfiguration          $emailConfig            Email Configuration object containing required information to send an email
     * @param   TemplateConfiguration       $templateConfig         Template Configuration object containing required information to build a template
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
                self::logSentEmail($user);
            }
        } catch(Exception $e) {
            throw new EmailException(__CLASS__ . ' Exception',0,$e);
        }
    }

    /**
     * logSentEmail
     * Updates the user with the newest project and rotates the old projects down one.
     *
     * @param   array           $recipient           Mailing_List_User array
     */
    private static function logSentEmail($recipient) {
        $sent_mail = Sent_Mail::create(
            ['SML_UserId'=>$recipient['MGL_Id'],
            'SML_ProjectId'=>self::$templateConfig->getProjectId(),
            'SML_Timestamp'=>Carbon::now()]
        );
    }

    /**
     * sendEmail
     * Sends them an email to the specified user.
     *
     * @param   User_test           $user           User object
     * @throws  FailureException
     */
    private static function sendEmail($user) {
        $templateData = array(
            'companyName'=>self::$templateConfig->getCompanyName(),
            'projectName'=>self::$templateConfig->getProjectName(),
            'projectId'=>self::$templateConfig->getProjectId(),
            'lastName'=>$user['MGL_LastName'],
            'username'=>$user['MGL_Username'],
            'urlId'=>$user['MGL_UniqueURLId']
        );
        $subject = self::$emailConfig->getSubject();
        $from = self::$emailConfig->getFromEmail();
        $to = $user['MGL_Email'];
        $mailResult = Mail::send(
            ['html' => 'emails.phishing.' . self::$templateConfig->getTemplate()],
            $templateData,
            function($m) use ($from, $to, $subject) {
                $m->from($from);
                $m->to($to);
                $m->subject($subject);
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
