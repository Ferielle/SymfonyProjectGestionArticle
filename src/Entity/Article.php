<?php

namespace App\Entity;
use Symfony\Component\HttpFoundation\File\File;
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
     * @ORM\Column(type="blob", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=THEME::class, fetch="EAGER", mappedBy="article", cascade={"persist"})
     */
    private $theme_articles;

   
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
    

    public function __construct()
    {
        $this->theme_articles = new ArrayCollection();
    }

    
   
   /**
 * @return Collection<int, THEME>
 */
public function getThemeArticles(): Collection
{
    return $this->theme_articles;
}

        public function getId(): ?int
    {
        return $this->id;
    }

    public function addThemeArticle(THEME $themeArticle): self
    {
        if (!$this->theme_articles->contains($themeArticle)) {
            $this->theme_articles[] = $themeArticle;
            $themeArticle->setArticle($this);
        }

        return $this;
    }

    public function removeThemeArticle(THEME $themeArticle): self
    {
        if ($this->theme_articles->removeElement($themeArticle)) {
            // set the owning side to null (unless already changed)
            if ($themeArticle->getArticle() === $this) {
                $themeArticle->setArticle(null);
            }
        }

        return $this;
    }
    private $imageFile;


   
    public function setBase64EncodedImage($base64EncodedImage): self
    {
        $this->image = $base64EncodedImage;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return null; 
    }
}
