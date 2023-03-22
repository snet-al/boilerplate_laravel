<?php

namespace App\Console\Commands;

use App\Traits\CommandTrait;
use Illuminate\Routing\Console\ControllerMakeCommand;

class MakeController extends ControllerMakeCommand
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
