<?php

namespace App\Dto;

class FooDto
{
    public function __construct(
        public readonly string $identifier,
    ) {
    }
}
