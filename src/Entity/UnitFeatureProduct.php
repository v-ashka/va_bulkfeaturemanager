<?php

namespace Va_bulkfeaturemanager\Entity;
use Doctrine\ORM\Mapping as ORM;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductRepository;

/**
 *@ORM\Table()
 *@ORM\Entity(repositoryClass=UnitFeatureProductRepository::class)
 */
class UnitFeatureProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=UnitFeature::class, inversedBy="unitFeatureProducts")
     * @ORM\JoinColumn(name="id_unit_feature", referencedColumnName="id_unit_feature", nullable=false)
     */
    private $unitFeature;

    /**
     * @ORM\ManyToOne(targetEntity=UnitFeatureValue::class, inversedBy="unitFeatureProducts")
     * @ORM\JoinColumn(name="id_unit_feature_value", referencedColumnName="id_unit_feature_value", nullable=false)
     */
    private $unitFeatureValue;

    /**
     * @var int
     * @ORM\Column(name="id_product", type="integer", nullable=false)
     */
    private $idProductAttribute;

    /**
     * @var int|null
     * @ORM\Column(name="id_attribute_group", type="integer", nullable=true)
     */
    private $idAttributeGroup;


    public function getIdAttributeGroup(): ?int
    {
        return $this->idAttributeGroup;
    }

    public function setIdAttributeGroup(?int $idAttributeGroup): void
    {
        $this->idAttributeGroup = $idAttributeGroup;
    }

    /**
     * @return mixed
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUnitFeatureValue()
    {
        return $this->unitFeatureValue;
    }

    /**
     * @param mixed $unitFeatureValue
     */
    public function setUnitFeatureValue($unitFeatureValue): void
    {
        $this->unitFeatureValue = $unitFeatureValue;
    }

    /**
     * @return mixed
     */
    public function getUnitFeature()
    {
        return $this->unitFeature;
    }

    /**
     * @param mixed $unitFeature
     */
    public function setUnitFeature($unitFeature): void
    {
        $this->unitFeature = $unitFeature;
    }

    public function getIdProductAttribute(): int
    {
        return $this->idProductAttribute;
    }

    public function setIdProductAttribute(int $idProductAttribute): void
    {
        $this->idProductAttribute = $idProductAttribute;
    }

}
