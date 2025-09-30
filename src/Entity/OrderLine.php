<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderLineRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['OrderLine:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['OrderLine:read', 'OrderLine:write'])]
    #[Assert\NotBlank]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    private ?CustomerOrder $orderRef = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrderRef(): ?CustomerOrder
    {
        return $this->orderRef;
    }

    public function setOrderRef(?CustomerOrder $orderRef): static
    {
        $this->orderRef = $orderRef;

        return $this;
    }
}
