<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;

trait StyledAlertTrait
{
    /**
     * Write a string in an alert box.
     *
     * @param string $string
     * @param string $style
     * @param int|string|null $verbosity
     * @return void
     */
    public function styledAlert(string $string, string $style, int|string $verbosity = null): void
    {
        $length = Str::length(strip_tags($string)) + 12;

        $line = str_repeat('*', $length);

        $this->comment("<$style>$line</$style>", $verbosity);
        $this->comment("<$style>*     $string     *</$style>", $verbosity);
        $this->comment("<$style>$line</$style>", $verbosity);

        $this->comment('', $verbosity);
    }
}
