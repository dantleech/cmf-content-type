<?php

declare(strict_types=1);

namespace Psi\Component\ContentType\Standard\View;

use Psi\Component\View\TypeInterface;
use Psi\Component\View\ViewFactory;
use Psi\Component\View\ViewInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeType implements TypeInterface
{
    private static $acceptedFormats = [
        'full' => \IntlDateFormatter::FULL,
        'long' => \IntlDateFormatter::LONG,
        'medium' => \IntlDateFormatter::MEDIUM,
        'short' => \IntlDateFormatter::SHORT,
        'none' => \IntlDateFormatter::NONE,
    ];

    public function createView(ViewFactory $factory, $data, array $options): ViewInterface
    {
        $dateFormat = $this->resolveFormat($options['date_format']);
        $timeFormat = $this->resolveFormat($options['time_format']);

        $formatter = new \IntlDateFormatter(
            \Locale::getDefault(),
            $dateFormat,
            $timeFormat,
            null,
            \IntlDateFormatter::GREGORIAN
        );

        return new DateTimeView($formatter->format($data), $data, $options['tag']);
    }

    public function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'tag' => null,
            'date_format' => \IntlDateFormatter::MEDIUM,
            'time_format' => \IntlDateFormatter::MEDIUM,
        ]);
    }

    private function resolveFormat($format)
    {
        if (is_int($format)) {
            if (in_array($format, self::$acceptedFormats)) {
                return $format;
            }
        }

        $format = strtolower($format);
        if (!isset(self::$acceptedFormats[$format])) {
            throw new InvalidOptionsException(sprintf(
                'Invalid format "%s". It must either be one of the \IntlDateFormatter constant values or one of the following strings: "%s"',
                $format, implode('", "', array_keys(self::$acceptedFormats))
            ));
        }

        return self::$acceptedFormats[$format];
    }
}
