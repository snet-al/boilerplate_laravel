<?php

namespace App\Traits;

use Symfony\Component\Console\Input\InputOption;

trait CommandTrait
{
    public function addAppOptionToCommand()
    {
        $actualOptions = parent::getOptions();

        $actualOptions[] = ['app', 'A', InputOption::VALUE_REQUIRED, 'Set the multi app name that creates a separate app'];

        return $actualOptions;
    }

    public function appNamespace()
    {
        $appNameArgument = $this->option('app');

        return ucfirst($appNameArgument) . 'App';
    }

    public function appFullNamespace($rootNamespace)
    {
        $appNamespace = $this->appNamespace();

        $actualNameSpace = parent::getDefaultNamespace($rootNamespace);

        return $actualNameSpace . '\\' . $appNamespace;
    }
}
