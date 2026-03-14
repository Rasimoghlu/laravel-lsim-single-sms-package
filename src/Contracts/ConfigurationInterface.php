<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Contracts;

interface ConfigurationInterface
{
    public function getLogin(): string;

    public function getPassword(): string;

    public function getSender(): string;

    public function getBaseUrl(): string;

    public function getTimeout(): int;

    public function getVerifySsl(): bool;

    public function validate(): bool;
}
