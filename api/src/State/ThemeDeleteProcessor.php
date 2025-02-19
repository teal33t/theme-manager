<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Theme;
use App\Service\ThemeManager;

class ThemeDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ThemeManager $themeManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof Theme) {
            throw new \InvalidArgumentException('Data is not an instance of Theme');
        }

        $this->themeManager->handleThemeDelete($data);
    }
}