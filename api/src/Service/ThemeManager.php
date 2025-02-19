<?php

namespace App\Service;

use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ThemeManager
{
    public function __construct(
        private readonly ThemeRepository $themeRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function handleThemeCreation(Theme $theme): Theme
    {
        $defaultTheme = $this->themeRepository->findDefault();

        if (!$defaultTheme && !$theme->isDefault()) {
            $theme->setIsDefault(true);
        }

        if ($theme->isDefault()) {
            $this->themeRepository->unsetAllDefault();
        }

        $this->entityManager->persist($theme);
        $this->entityManager->flush();

        return $theme;
    }

    public function handleThemeUpdate(Theme $theme, Theme $existingTheme): Theme
    {
        if ($existingTheme->isDefault() && !$theme->isDefault()) {
            throw new BadRequestHttpException('Cannot unset default status of the default theme.');
        }

        if ($theme->isDefault()) {
            $this->themeRepository->unsetAllDefault();
        }

        $existingTheme->setName($theme->getName());
        $existingTheme->setColors($theme->getColors());
        $existingTheme->setIsDefault($theme->isDefault());

        $this->entityManager->flush();

        return $existingTheme;
    }

    public function handleThemeDelete(Theme $theme): void
    {
        if ($theme->isDefault()) {
            throw new BadRequestHttpException('Cannot delete the default theme.');
        }

        $this->entityManager->remove($theme);
        $this->entityManager->flush();
    }
}