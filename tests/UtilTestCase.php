<?php

namespace App\Test;

use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

/**
 * Class UtilTestCase.
 */
class UtilTestCase extends BaseTestCase
{
    /**
     * Test translation method.
     *
     * @dataProvider messageDataProvider
     *
     * @param string $locale
     * @param string $message
     * @param string $expected
     *
     * @return void
     */
    public function testTranslateMethod(string $locale, string $message, string $expected)
    {
        $this->prepareTranslator($locale);
        $this->assertSame(strtolower($expected), strtolower(__($message)));
    }

    /**
     * Test translation method with context.
     *
     * @dataProvider messageWithContextDataProvider
     *
     * @param string $locale
     * @param string $message
     * @param string $context
     * @param string $expected
     *
     * @return void
     */
    public function testTranslateMethodWithContext(string $locale, string $message, string $context, string $expected)
    {
        $this->prepareTranslator($locale);
        $this->assertSame(strtolower($expected), strtolower(__($message, $context)));
    }

    /**
     * Data Provider for translation test.
     *
     * @return array
     */
    public function messageDataProvider()
    {
        return [
            'Test german "Email oder Benutzernamen eingeben"' => [
                'de_CH',
                'Enter email or username',
                'Email oder Benutzernamen eingeben',
            ],
            'Test english "Enter email or username"' => [
                'en_GB',
                'Enter email or username',
                'Enter email or username',
            ],
        ];
    }

    /**
     * Data Provider for translation with context test.
     *
     * @return array
     */
    public function messageWithContextDataProvider()
    {
        return [
            'Test german "Zähler: %s"' => [
                'de_CH',
                'Counter: %s',
                5,
                'Zähler: 5',
            ],
            'Test english "Counter: %s"' => [
                'en_GB',
                'Counter: %s',
                5,
                'Counter: 5',
            ],
        ];
    }

    /**
     * @param string $locale
     *
     * @return void
     */
    protected function prepareTranslator(string $locale): void
    {
        $translator = new Translator($locale, /* @scrutinizer ignore-type */
            new MessageSelector());
        $translator->addLoader('mo', new MoFileLoader());
        $resource = __DIR__ . '/../resources/locale/' . $locale . '_messages.mo';
        $translator->setLocale($locale);
        $translator->addResource('mo', $resource, $locale);

        __($translator);
    }
}
