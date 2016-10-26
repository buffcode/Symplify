<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Runner;

use Symplify\CodingStandard\Contract\Runner\RunnerInterface;
use Symplify\CodingStandard\Process\PhpCbfProcessBuilder;
use Symplify\CodingStandard\Process\PhpCsProcessBuilder;

final class SymplifyRunner implements RunnerInterface
{
    /**
     * @var string
     */
    private $extensions;

    /**
     * @var bool
     */
    private $hasErrors = false;

    public function __construct(string $extensions = 'php')
    {
        $this->extensions = $extensions;
    }

    public function runForDirectory(string $directory) : string
    {
        $builder = new PhpCsProcessBuilder($directory);
        $builder->setExtensions($this->extensions);
        $builder->setStandard($this->getRuleset());

        $process = $builder->getProcess();
        $process->run();

        $this->detectErrorsInOutput($process->getOutput());

        return $process->getOutput();
    }

    public function hasErrors() : bool
    {
        return $this->hasErrors;
    }

    public function fixDirectory(string $directory) : string
    {
        $builder = new PhpCbfProcessBuilder($directory);
        $builder->setStandard($this->getRuleset());
        $builder->setExtensions($this->extensions);

        $process = $builder->getProcess();
        $process->run();

        return $process->getOutput();
    }

    private function detectErrorsInOutput(string $output)
    {
        if (strpos($output, 'ERROR') !== false) {
            $this->hasErrors = true;
        }
    }

    private function getRuleset() : string
    {
        if (file_exists($path = 'src/SymplifyCodingStandard/ruleset.xml')) {
            return $path;
        }

        return 'vendor/symplify/coding-standard/src/SymplifyCodingStandard/ruleset.xml';
    }
}