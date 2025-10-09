<?php

namespace Rez1pro\UserAccess\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'permission:create')]
class PermissionCreateCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Permission Enums';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = "stubs/Enums/createPermission.stub";

        return file_exists($customPath = __DIR__ . "/../../../" . trim($stub, '/'))
            ? $customPath
            : __DIR__ . "/../../../" . $stub;
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return match (true) {
            default => "\\App\\Enums\\Permissions",
        };
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        return str_replace(
            ['{{ enum }}'],
            $this->argument('name'),
            parent::buildClass($this->argument('name'))
        );
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->didReceiveOptions($input)) {
            return;
        }

        // $type = select('Which type of enum would you like?', [
        //     'pure' => 'Pure enum',
        //     'string' => 'Backed enum (String)',
        //     'int' => 'Backed enum (Integer)',
        // ]);

        // if ($type !== 'pure') {
        //     $input->setOption($type, true);
        // }
    }
}