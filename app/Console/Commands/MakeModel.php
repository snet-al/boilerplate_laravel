<?php

namespace App\Console\Commands;

use App\Traits\CommandTrait;
use Illuminate\Foundation\Console\ModelMakeCommand;

class MakeModel extends ModelMakeCommand
{
    use CommandTrait;

    public function getOptions()
    {
        return $this->addAppOptionToCommand();
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Models')) ? $rootNamespace.'\\Models\\' . $this->appNamespace() : $rootNamespace;
    }
}
