<?php

declare(strict_types=1);

namespace app\usecases\site;

use app\usecases\shared\view\FieldMetadataInterface;
use Override;
use yii\base\Model;
use yii\mail\MailerInterface;

/**
 * Represents the contact form with phone validation and email sending.
 */
class ContactForm extends Model implements FieldMetadataInterface
{
    /**
     * Message body submitted by the user.
     */
    public string $body = '';
    /**
     * Sender email address.
     */
    public string $email = '';
    /**
     * Sender full name.
     */
    public string $name = '';
    /**
     * Sender phone number in `(999) 999-9999` format.
     */
    public string $phone = '';
    /**
     * Message subject.
     */
    public string $subject = '';
    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param MailerInterface $mailer Mailer component used to send the email.
     * @param string $email Recipient email address.
     * @param string $senderEmail Sender email address used in the `From` header.
     * @param string $senderName Sender display name used in the `From` header.
     *
     * @return bool Whether the email was sent successfully.
     */
    public function contact(MailerInterface $mailer, string $email, string $senderEmail, string $senderName): bool
    {
        if ($this->validate()) {
            $messageBody = "Name: {$this->name}\nEmail: {$this->email}\nPhone: {$this->phone}\n\n{$this->body}";

            return $mailer->compose()
                ->setTo($email)
                ->setFrom([$senderEmail => $senderName])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($messageBody)
                ->send();
        }

        return false;
    }

    public function getFieldConfigs(): array
    {
        return [
            'body' => ['rows' => [6]],
        ];
    }

    public function getPlaceholders(): array
    {
        return [
            'phone' => '(999) 999-9999',
        ];
    }

    /**
     * Returns the validation rules for the form attributes.
     */
    #[Override]
    public function rules(): array
    {
        return [
            [
                ['name', 'email', 'phone', 'subject', 'body'],
                'required',
            ],
            [
                'email',
                'email',
            ],
            [
                'phone',
                'match',
                'pattern' => '/^\(\d{3}\) \d{3}-\d{4}$/',
                'message' => 'Phone number must match (999) 999-9999 format.',
            ],
        ];
    }
}
