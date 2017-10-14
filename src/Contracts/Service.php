<?php

namespace Shield\Shield\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface Service
{
    public function verify(Request $request, Collection $config): bool;

    public function headers(): array;
}
