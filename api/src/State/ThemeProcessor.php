<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Theme;
use App\Service\ThemeManager;

class ThemeProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ThemeManager $themeManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Theme
    {
        if (!$data instanceof Theme) {
            throw new \InvalidArgumentException('Data is not an instance of Theme');
        }

        if ($context['previous_data'] ?? null) {
            return $this->themeManager->handleThemeUpdate($data, $context['previous_data']);
        }

        return $this->themeManager->handleThemeCreation($data);
    }
}