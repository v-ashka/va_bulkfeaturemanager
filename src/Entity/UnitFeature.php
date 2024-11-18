<?php

namespace Va_bulkfeaturemanager\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass=UnitFeatureRepository::class)
 */
class UnitFeature
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_unit_feature", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="unit_feature_name", type="string", length=64)
     */
    private $unitFeatureName;


    /**
     * @var string
     * @ORM\Column(name="unit_feature_shortcut", type="string", length=64)
     */
    private $unitFeatureShortcut;

    /**
     * @var int
     * @ORM\Column(name="unit_feature_base_value", type="integer", length=128 )
     */
    private $unitFeatureBaseValue;

    /**
     * @ORM\OneToMany(targetEntity=UnitFeatureValue::class, mappedBy="unitFeature")
     */
    private $unitFeatureValues;

    /**
     * @ORM\OneToMany(targetEntity=UnitFeatureProduct::class, mappedBy="unitFeature")
     */
    private $unitFeatureProducts;

    public function __construct()
    {
        $this->unitFeatureValues = new ArrayCollection();
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

    public function getUnitFeatureName(): string
    {
        return $this->unitFeatureName;
    }

    public function setUnitFeatureName(string $unitFeatureName): void
    {
        $this->unitFeatureName = $unitFeatureName;
    }

    public function getUnitFeatureShortcut(): string
    {
        return $this->unitFeatureShortcut;
    }

    public function setUnitFeatureShortcut(string $unitFeatureShortcut): void
    {
        $this->unitFeatureShortcut = $unitFeatureShortcut;
    }

    public function getUnitFeatureBaseValue(): int
    {
        return $this->unitFeatureBaseValue;
    }

    public function setUnitFeatureBaseValue(int $unitFeatureBaseValue): void
    {
        $this->unitFeatureBaseValue = $unitFeatureBaseValue;
    }
}
