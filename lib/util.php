<?php

use Symfony\Component\Translation\Translator;

/**
 * Translation function (i18n).
 *
 * @param mixed $message
 * @return string
 */
function __($message) {
    static $translator = null;
    /* @var $translator Translator */
    if ($message instanceof Translator) {
        $translator = $message;
        return '';
    }
    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }

    return $translated;
};
