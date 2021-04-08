<?php

namespace TanmayMishu\LaravelFunnel\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FilterCommand extends GeneratorCommand
{
    /**
     * Command should halt when attempting to create a filter
     * that includes any of the names from this array.
     *
     * @var string[]
     */
    protected $reservedKeywords = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'funnel:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new filter';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('clause') == 'orderBy') {
            return __DIR__.'/Stubs/filter-order-by.stub';
        } elseif ($this->option('clause') == 'groupBy') {
            return __DIR__.'/Stubs/filter-group-by.stub';
        } else {
            return __DIR__.'/Stubs/filter-where.stub';
        }
    }

    public function handle()
    {
        $this->reservedKeywords[] = config()->has('funnel')
            ? config('funnel.eager_key')
            : 'with';

        if (in_array(strtolower($this->argument('name')), $this->reservedKeywords)) {
            $this->error('Reserved name. Please provide a different name.');

            return;
        }

        if (in_array($this->option('parameter'), $this->reservedKeywords)) {
            $this->error('Reserved parameter. Please provide a different parameter.');

            return;
        }

        return parent::handle();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Filters';
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
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceOptions($stub);

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyFilter', $class, $stub);
    }

    private function replaceOptions($stub)
    {
        $stub = $this->option('attribute')
            ? str_replace('DummyAttribute', $this->option('attribute'), $stub)
            : $this->getDefaultAttribute($stub);

        $stub = $this->option('parameter')
            ? str_replace('DummyParameter', $this->option('parameter'), $stub)
            : $this->getDefaultParameter($stub);

        $stub = $this->option('operator')
            ? str_replace('DummyOperator', $this->option('operator'), $stub)
            : $this->getDefaultOperator($stub);

        return $stub;
    }

    private function getDefaultAttribute($stub)
    {
        return str_replace('DummyAttribute', Str::snake($this->argument('name')), $stub);
    }

    private function getDefaultParameter($stub)
    {
        return str_replace('DummyParameter', Str::snake($this->argument('name')), $stub);
    }

    private function getDefaultOperator($stub)
    {
        return str_replace('DummyOperator', '=', $stub);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the filter class.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['attribute', 'a', InputOption::VALUE_OPTIONAL, 'The attribute name of the model (e.g. is_active). Default: Snake cased filter_class'],
            ['parameter', 'p', InputOption::VALUE_OPTIONAL, 'The name of the request query parameter (e.g. active). Default: Snake cased filter_class'],
            ['operator', 'o', InputOption::VALUE_OPTIONAL, 'The operator for the WHERE clause (e.g. >, like, =, <). Default: ='],
            ['clause', 'c', InputOption::VALUE_OPTIONAL, 'The clause for the query (e.g. where, orderBy, groupBy). Default: where'],
        ];
    }
}
