<?php

namespace App\Console\Commands;

use Illuminate\Console\Command as ConsoleCommand;

/**
 * Class Command
 * @package App\Console\Commands
 */
class Command extends ConsoleCommand
{
    /**
     * @param string $string
     * @param null $verbosity
     */
    public function info($string, $verbosity = null)
    {
        return parent::info($this->logWithTime($string), $verbosity);
    }

    /**
     * @param string $string
     * @param null $verbosity
     */
    public function error($string, $verbosity = null)
    {
        return parent::error($this->logWithTime($string), $verbosity);
    }

    /**
     * @param string $string
     * @param null $verbosity
     */
    public function warn($string, $verbosity = null)
    {

        return parent::warn($this->logWithTime($string), $verbosity);
    }

    /**
     * @param $string
     * @return string
     */
    private function logWithTime($string)
    {
        return date('[Y-m-d H:i:s] ') . $string;
    }
}
