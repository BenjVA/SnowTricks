<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TricksRepository::class)]
class Tricks
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $groupTricks = null;

    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Comments::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $users = null;

    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Images::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Videos::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $videos;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGroupTricks(): ?string
    {
        return $this->groupTricks;
    }

    public function setGroupTricks(string $groupTricks): static
    {
        $this->groupTricks = $groupTricks;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTricks($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTricks() === $this) {
                $comment->setTricks(null);
            }
        }

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setTricks($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getTricks() === $this) {
                $image->setTricks(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Videos>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function setVideos(?Collection $videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    public function addVideos(Videos $videos): static
    {
        if (!$this->videos->contains($videos)) {
            $this->videos->add($videos);
            $videos->setTricks($this);
        }

        return $this;
    }

    public function removeVideo(Videos $videos): static
    {
        if ($this->videos->removeElement($videos)) {
            // set the owning side to null (unless already changed)
            if ($videos->getTricks() === $this) {
                $videos->setTricks(null);
            }
        }

        return $this;
    }
}
