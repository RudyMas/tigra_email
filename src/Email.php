<?php

namespace Tigra;

use Latte\Engine;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;

/**
 * Class Email (PHP version 7.4)
 *
 * @author      Rudy Mas <rudy.mas@rmsoft.be>
 * @copyright   2022, rmsoft.be. (https://www.rmsoft.be/)
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version     7.4.1.0
 * @package     Tigra
 */
class Email
{
    private Message $email;
    private string $from;

    private string $use_smtp;
    private string $email_host;
    private string $email_username;
    private string $email_password;
    private string $email_security;

    /**
     * Email constructor.
     * @param string $email_from
     */
    public function __construct(string $email_from = EMAIL_FROM)
    {
        $this->email = new Message();
        $this->from = $email_from;
    }

    /**
     * Use this function when you use it in your own project
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $security
     * @param bool $use_smtp
     */
    public function setup(
        string $host,
        string $username,
        string $password,
        string $security,
        bool $use_smtp = true
    ): void {
        $this->use_smtp = $use_smtp;
        $this->email_host = $host;
        $this->email_username = $username;
        $this->email_password = $password;
        $this->email_security = $security;
    }

    /**
     * Use this function when you use it in the Tigra Framework
     */
    public function config(): void
    {
        $this->use_smtp = USE_SMTP;
        $this->email_host = EMAIL_HOST;
        $this->email_username = EMAIL_USERNAME;
        $this->email_password = EMAIL_PASSWORD;
        $this->email_security = EMAIL_SECURITY;
    }

    /**
     * For setting the sender of the E-mail
     *
     * @param string $from
     */
    public function setFrom(string $from = EMAIL_FROM): void
    {
        $this->from = $from;
    }

    /**
     * Prepare a plain text E-mail
     *
     * @param array $to
     * @param string $subject
     * @param string $body
     * @param array|null $attachment
     * @param array|null $cc
     * @param array $bcc
     */
    public function setTextMessage(
        array $to,
        string $subject,
        string $body,
        array $attachment = null,
        array $cc = null,
        array $bcc = EMAIL_BCC
    ): void {
        $this->email->setFrom($this->from);
        foreach ($to as $value) {
            $this->email->addTo($value);
        }
        if (!is_null($cc)) {
            foreach ($cc as $value) {
                $this->email->addCc($value);
            }
        }
        if (!is_null($bcc)) {
            foreach ($bcc as $value) {
                $this->email->addBcc($value);
            }
        }
        if (!is_null($attachment)) {
            foreach ($attachment as $file) {
                $this->email->addAttachment($file);
            }
        }
        $this->email->setSubject($subject);
        $this->email->setBody($body);
    }

    /**
     * Prepare a HTML E-mail
     *
     * @param array $to
     * @param string $subject
     * @param string $body
     * @param array|null $attachment
     * @param array|null $cc
     * @param array|null $bcc
     */
    public function setHtmlMessage(
        array $to,
        string $subject,
        string $body,
        array $attachment = null,
        array $cc = null,
        array $bcc = EMAIL_BCC
    ): void {
        $this->email->setFrom($this->from);
        foreach ($to as $value) {
            $this->email->addTo($value);
        }
        if (!is_null($cc)) {
            foreach ($cc as $value) {
                $this->email->addCc($value);
            }
        }
        if (!is_null($bcc)) {
            foreach ($bcc as $value) {
                $this->email->addBcc($value);
            }
        }
        if (!is_null($attachment)) {
            foreach ($attachment as $file) {
                $this->email->addAttachment($file);
            }
        }
        $this->email->setSubject($subject);
        $this->email->setHtmlBody($body);
    }

    /**
     * Use this after your E-mail has been prepared
     */
    public function sendMail(): void
    {
        if ($this->use_smtp) {
            $mailer = new SmtpMailer([
                'host' => $this->email_host,
                'username' => $this->email_username,
                'password' => $this->email_password,
                'secure' => $this->email_security
            ]);
        } else {
            $mailer = new SendmailMailer();
        }
        $mailer->send($this->email);
    }

    /**
     * Use Latte for rendering HTML E-mails in the Tigra Framework
     *
     * @param string $latteFile
     * @param array $data
     * @return string
     */
    public function tigraRenderHtml(string $latteFile, array $data): string
    {
        $latte = new Engine();
        $latte->setTempDirectory(SYSTEM_ROOT . '/tmp/latte');
        return $latte->renderToString(SYSTEM_ROOT . '/private/latte/' . $latteFile, $data);
    }

    /**
     * Use Latte for rendering HTML E-mails in your own project
     *
     * @param string $latteFile
     * @param array $data
     * @param string $tempFolderLatte
     * @return string
     */
    public function renderHtml(string $latteFile, array $data, string $tempFolderLatte = '/tmp/latte'): string
    {
        $latte = new Engine();
        $latte->setTempDirectory($tempFolderLatte);
        return $latte->renderToString($latteFile, $data);
    }
}
