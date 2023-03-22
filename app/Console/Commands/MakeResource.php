<?php

namespace App\Console\Commands;

use App\Traits\CommandTrait;
use Illuminate\Foundation\Console\ResourceMakeCommand;

class MakeResource extends ResourceMakeCommand
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
