<?php

namespace Enhavo\Bundle\AppBundle\Tests\Mailer;

use Enhavo\Bundle\AppBundle\Exception\MailNotFoundException;
use Enhavo\Bundle\AppBundle\Mailer\Attachment;
use Enhavo\Bundle\AppBundle\Mailer\MailerManager;
use Enhavo\Bundle\AppBundle\Mailer\Message;
use Enhavo\Bundle\AppBundle\Template\TemplateManager;
use Enhavo\Bundle\AppBundle\Tests\Mock\TranslatorMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class MailerManagerTest extends TestCase
{
    private function createDependencies()
    {
        $dependencies = new MailerManagerTestDependencies();
        $dependencies->templateManager = $this->getMockBuilder(TemplateManager::class)->disableOriginalConstructor()->getMock();
        $dependencies->templateManager->method('getTemplate')->willReturnCallback(function ($tpl) {
            return $tpl;
        });
        $dependencies->environment = new Environment(new FilesystemLoader([
            'Fixtures/Mail/MailManager',
        ], __DIR__ . '/../'));
        $dependencies->mailer = $this->getMockBuilder(MailerInterface::class)->disableOriginalConstructor()->getMock();
        $dependencies->mailsConfig = [];
        $dependencies->translator = new TranslatorMock();
        $dependencies->defaultConfig = [
            'from' => 'from@enhavo.com',
            'name' => 'enhavo',
            'to' => 'to@enhavo.com',
        ];
        $dependencies->model = Message::class;
        return $dependencies;
    }

    private function createInstance(MailerManagerTestDependencies $dependencies)
    {
        return new MailerManager(
            $dependencies->mailer,
            $dependencies->templateManager,
            $dependencies->environment,
            $dependencies->defaultConfig,
            $dependencies->mailsConfig,
            $dependencies->model,
            $dependencies->translator,
        );
    }

    public function testSendMailMultipart()
    {
        $dependencies = $this->createDependencies();
        $dependencies->mailer->method('send');

        $dependencies->defaultConfig = [
            'from' => '{{ resource.from }}',
            'name' => '{{ resource.name }}',
            'to' => '{{ resource.to }}',
        ];

        $dependencies->mailsConfig = [
            'default' => [
                'from' => null,
                'name' => null,
                'to' => null,
                'subject' => '{{ resource.subject }}',
                'template' => 'multipart-mail.html.twig',
                'content_type' => Message::CONTENT_TYPE_MIXED,
                'translation_domain' => null,
            ]
        ];

        $manager = $this->createInstance($dependencies);

        $dependencies->mailer->expects($this->once())->method('send')->willReturnCallback(function (Email $email) {
            $this->assertEquals('__subject__', $email->getSubject());

            $this->assertEquals('from@enhavo.com', $email->getFrom()[0]->getAddress());
            $this->assertEquals('__name__', $email->getFrom()[0]->getName());

            $this->assertEquals('to@enhavo.com', $email->getTo()[0]->getAddress());
            $this->assertEquals('', $email->getTo()[0]->getName());

            $this->assertEquals('__text__', $email->getTextBody());
        });

        $manager->sendMail('default', $this->createDefaultResource(), [
            new Attachment(__DIR__ . '/../Fixtures/Mail/MailManager/dummy-attachment.txt')
        ]);
    }

    public function testSendMailSimple()
    {
        $dependencies = $this->createDependencies();
        $dependencies->mailer->method('send');
        $dependencies->translator->setPostFix('trans');

        $dependencies->mailsConfig = [
            'default' => [
                'from' => '{{ resource.from }}',
                'name' => '{{ resource.name }}',
                'to' => '{{ resource.to }}',
                'subject' => '{{ resource.subject }}',
                'template' => 'simple-mail.html.twig',
                'content_type' => Message::CONTENT_TYPE_PLAIN,
                'translation_domain' => null,
            ]
        ];

        $manager = $this->createInstance($dependencies);

        $dependencies->mailer->expects($this->once())->method('send')->willReturnCallback(function (Email $email) {
            $this->assertEquals('__text__', $email->getHtmlBody());
            $this->assertEquals('__subject__trans', $email->getSubject());
        });

        $manager->sendMail('default', $this->createDefaultResource());
    }

    private function createDefaultResource()
    {
        return [
            'text' => '__text__',
            'name' => '__name__',
            'subject' => '__subject__',
            'resource' => '__RESOURCE__',
            'from' => 'from@enhavo.com',
            'to' => 'to@enhavo.com',
            'translation_domain' => null,
        ];
    }

    public function testNoKeyExists()
    {
        $dependencies = $this->createDependencies();
        $manager = $this->createInstance($dependencies);

        $this->expectException(MailNotFoundException::class);

        $manager->sendMail('uknown', null);
    }
}

class MailerManagerTestDependencies
{
    public TemplateManager|MockObject $templateManager;
    public Environment|MockObject $environment;
    public MailerInterface|MockObject $mailer;
    public array $defaultConfig = [];
    public array $mailsConfig = [];
    public string $model;
    public TranslatorInterface|TranslatorMock $translator;
}
