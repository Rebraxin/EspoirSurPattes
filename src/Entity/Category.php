<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Article", inversedBy="categories")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        $this->articles[] = $article;
        $article->addCategory($this);
        return $this;
    }

    public function removeArticle(Article $article)
    {
        $this->articles->removeElement($article);
        $article->removeCategory($this);
    }
    // public function addArticle(Article $article): self
    // {
    //     if (!$this->articles->contains($article)) {
    //         $this->articles[] = $article;
    //     }

    //     return $this;
    // }

    // public function removeArticle(Article $article): self
    // {
    //     if ($this->articles->contains($article)) {
    //         $this->articles->removeElement($article);
    //     }

    //     return $this;
    // }
}
