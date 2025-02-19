<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultThemeController extends AbstractController
{
    public function __construct(
        private readonly ThemeRepository $themeRepository
    ) {
    }

    public function __invoke()
    {
        $defaultTheme = $this->themeRepository->findDefault();

        if (!$defaultTheme) {
            throw new NotFoundHttpException('No default theme found');
        }

        return $defaultTheme;
    }
}