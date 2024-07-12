<?php

namespace App\Entity\Communication;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\Communication\GroupRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'contains', targetEntity: self::class, cascade: ['persist'] )]
    private ?self $include = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy:'include', cascade: ['persist'])]
    private Collection $contains;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy:'groups', cascade: ['persist'])]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->contains = new ArrayCollection();
    }

    public function  __toString()
    {
        return $this->getName();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getInclude(): ?self
    {
        return $this->include;
    }

    public function setInclude(?self $include): self
    {
        $this->include = $include;

        return $this;
    }


    /**
     * @return Collection<int, Contains>
     */
    public function getContains(): Collection
    {
        return $this->contains;
    }

    public function addContain(?self $group): self
    {
        if (!$this->contains->contains($group)) {
            $this->contains->add($group);
            $group->setInclude($this);
        }

        return $this;
    }

    public function removeContain(?self $group): self
    {
        if ($this->contains->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getInclude() === $this) {
                $group->setInclude(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->addGroup($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeGroup($this);
        }

        return $this;
    }
}
