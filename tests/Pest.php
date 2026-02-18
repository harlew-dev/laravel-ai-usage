<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature');
uses(RefreshDatabase::class)->in('Unit');

require_once __DIR__.'/Support/helpers.php';
