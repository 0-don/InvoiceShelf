<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// The Modules/HelloWorld integration test needs the module enabled at the
// nwidart level, which is read from storage/app/modules_statuses.json when the
// app boots. That file is gitignored (created locally by `module:make`), so it
// is absent in CI and fresh clones — leaving HelloWorld disabled and the
// integration test failing with 404s. Provision it (only if missing) before any
// test boots the application, so CI matches a local dev environment.
$modulesStatuses = __DIR__.'/../storage/app/modules_statuses.json';
if (! is_file($modulesStatuses)) {
    @mkdir(dirname($modulesStatuses), 0755, true);
    file_put_contents($modulesStatuses, json_encode(['HelloWorld' => true], JSON_PRETTY_PRINT).PHP_EOL);
}

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');
