<?php

namespace App\Entity;

use App\Repository\THEMERepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=THEMERepository::class)
 */
class THEME
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $labeltheme;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="theme_article")
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabeltheme(): ?string
    {
        return $this->labeltheme;
    }

    public function setLabeltheme(string $labeltheme): self
    {
        $this->labeltheme = $labeltheme;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }
}
