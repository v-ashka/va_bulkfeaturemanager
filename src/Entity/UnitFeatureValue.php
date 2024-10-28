<?php

namespace Va_bulkfeaturemanager\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass=UnitFeatureValueRepository::class)
 */
class UnitFeatureValue
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_unit_feature_value", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UnitFeature
     * @ORM\ManyToOne(targetEntity=UnitFeature::class, inversedBy="unitFeatureValues")
     * @ORM\JoinColumn(name="id_unit_feature", referencedColumnName="id_unit_feature", nullable=false)
     */
    private $unitFeature;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=64)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity=UnitFeatureProduct::class, mappedBy="unitFeatureValue")
     */
    private $unitFeatureProducts;

    public function __construct()
    {
        $this->unitFeatureProducts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getUnitFeature(): UnitFeature
    {
        return $this->unitFeature;
    }

    public function setUnitFeature(UnitFeature $unitFeature): void
    {
        $this->unitFeature = $unitFeature;
    }
}
