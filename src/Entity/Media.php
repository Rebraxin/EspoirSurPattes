<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgLink;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="media")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->imgLink;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgLink(): ?string
    {
        return $this->imgLink;
    }

    public function setImgLink(?string $imgLink): self
    {
        $this->imgLink = $imgLink;

        return $this;
    }

    /**
     * @return Collection|article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setMedia($this);
        }

        return $this;
    }

    public function removeArticle(article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getMedia() === $this) {
                $article->setMedia(null);
            }
        }

        return $this;
    }
}
