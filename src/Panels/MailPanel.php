<?php

namespace Yiisoft\Yii\Debug\Panels;

use Psr\Http\Message\RequestInterface;
use Yiisoft\Files\FileHelper;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Panel;

/**
 * Debugger panel that collects and displays the generated emails.
 */
class MailPanel extends Panel
{
    /**
     * @var string path where all emails will be saved. should be an alias.
     */
    public $mailPath = '@runtime/debug/mail';

    /**
     * @var array current request sent messages
     */
    private $_messages = [];

    private RequestInterface $request;
    public function __construct(RequestInterface $request, View $view)
    {
        $this->request = $request;
        parent::__construct($view);

        Event::on(BaseMailer::class, SendEvent::AFTER, function ($event) {
            /* @var $message MessageInterface */
            $message = $event->message;
            $messageData = [
                'isSuccessful' => $event->isSuccessful,
                'from' => $this->convertParams($message->getFrom()),
                'to' => $this->convertParams($message->getTo()),
                'reply' => $this->convertParams($message->getReplyTo()),
                'cc' => $this->convertParams($message->getCc()),
                'bcc' => $this->convertParams($message->getBcc()),
                'subject' => $message->getSubject(),
                'charset' => $message->getCharset(),
            ];

            // add more information when message is a SwiftMailer message
            if ($message instanceof \Yiisoft\Yii\SwiftMailer\Message) {
                /* @var $swiftMessage \Swift_Message */
                $swiftMessage = $message->getSwiftMessage();

                $body = $swiftMessage->getBody();
                if (empty($body)) {
                    $parts = $swiftMessage->getChildren();
                    foreach ($parts as $part) {
                        if (!($part instanceof \Swift_Mime_Attachment)) {
                            /* @var $part \Swift_Mime_MimePart */
                            if ($part->getContentType() === 'text/plain') {
                                $messageData['charset'] = $part->getCharset();
                                $body = $part->getBody();
                                break;
                            }
                        }
                    }
                }

                $messageData['body'] = $body;
                $messageData['time'] = $swiftMessage->getDate();
                $messageData['headers'] = $swiftMessage->getHeaders();
            }

            // store message as file
            $fileName = $event->sender->generateMessageFileName();
            $mailPath = Yii::getAlias($this->mailPath);
            FileHelper::createDirectory($mailPath);
            file_put_contents($mailPath . '/' . $fileName, $message->toString());
            $messageData['file'] = $fileName;

            $this->_messages[] = $messageData;
        });
    }
    public function getName(): string
    {
        return 'Mail';
    }
    public function getSummary(): string
    {
        return $this->render('panels/mail/summary', [
            'panel' => $this,
            'mailCount' => is_array($this->data) ? count($this->data) : '⚠️',
        ]);
    }
    public function getDetail(): string
    {
        return $this->render('panels/mail/detail', [
            'panel' => $this,
        ]);
    }

    /**
     * Save info about messages of current request. Each element is array holding
     * message info, such as: time, reply, bc, cc, from, to and other.
     * @return array messages
     */
    public function save()
    {
        return $this->_messages;
    }

    /**
     * Return array of created email files
     * @return array
     */
    public function getMessagesFileName()
    {
        $names = [];
        foreach ($this->_messages as $message) {
            $names[] = $message['file'];
        }

        return $names;
    }

    /**
     * @param mixed $attr
     * @return string
     */
    private function convertParams($attr)
    {
        if (is_array($attr)) {
            $attr = implode(', ', array_keys($attr));
        }

        return $attr;
    }
}
