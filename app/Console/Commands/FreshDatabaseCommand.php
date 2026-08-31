<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Database\Console\Migrations\FreshCommand as LaravelFreshCommand;
use Throwable;

final class FreshDatabaseCommand extends LaravelFreshCommand
{
    public function handle(): int
    {
        if ($this->isProhibited()) {
            return self::FAILURE;
        }

        $database = $this->input->getOption('database');

        try {
            $repositoryExists = $this->migrator->usingConnection(
                $database,
                fn (): bool => $this->migrator->repositoryExists(),
            );
        } catch (Throwable) {
            $repositoryExists = false;
        }

        if (! $repositoryExists) {
            $this->call('migrate:install', array_filter([
                '--database' => $database,
                '--force' => true,
            ]));
        }

        return parent::handle();
    }
}
