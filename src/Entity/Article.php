<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="blob")
     */
    private $image;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=THEME::class, mappedBy="article", cascade={"remove"})
     */
    private $theme_article;

    public function __construct()
    {
        $this->theme_article = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Theme>
     */
    public function getThemeArticle(): Collection
    {
        return $this->theme_article;
    }

    public function addThemeArticle(Theme $themeArticle): self
    {
        if (!$this->theme_article->contains($themeArticle)) {
            $this->theme_article[] = $themeArticle;
            $themeArticle->setArticle($this);
        }

        return $this;
    }

    public function removeThemeArticle(Theme $themeArticle): self
    {
        if ($this->theme_article->removeElement($themeArticle)) {
            // set the owning side to null (unless already changed)
            if ($themeArticle->getArticle() === $this) {
                $themeArticle->setArticle(null);
            }
        }

        return $this;
    }
}
