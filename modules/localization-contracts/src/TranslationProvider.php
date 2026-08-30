<?php

namespace Liberu\Localization\Contracts;

interface TranslationProvider
{
    public function name(): string;

    public function translate(string $text, string $targetLocale, string $sourceLocale = 'en'): string;
}
