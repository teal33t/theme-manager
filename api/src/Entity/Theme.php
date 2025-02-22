<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ThemeRepository;
use App\State\ThemeProcessor;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use App\Controller\DefaultThemeController;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: ThemeProcessor::class),
        new Put(processor: ThemeProcessor::class),
        new Delete(processor: ThemeDeleteProcessor::class),
        new Get(
            uriTemplate: '/themes/default',
            controller: DefaultThemeController::class,
            name: 'get_default_theme'
        ),
    ],
    normalizationContext: ['groups' => ['theme:read']],
    denormalizationContext: ['groups' => ['theme:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(BooleanFilter::class, properties: ['isDefault'])]
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['theme:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['theme:read', 'theme:write'])]
    private string $name;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['theme:read', 'theme:write'])]
    private ?array $colors = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['theme:read', 'theme:write'])]
    private bool $isDefault = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(?array $colors): self
    {
        $this->colors = $colors;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }
}