<?php

namespace App\Console\Commands;

use App\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\RequestMakeCommand;

class MakeRequest extends RequestMakeCommand
{
    use CommandTrait;

    public function getOptions()
    {
        return $this->addAppOptionToCommand();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->appFullNamespace($rootNamespace);
    }
}
