<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
class UnitFeatureProductsExtraFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminProductsExtraFeatureConfiguration';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_product', HiddenType::class, [
                'disabled' => true,
                'label' => $this->trans('Product id', self::domainTranslation),
            ])
            ->add('feature_id', ChoiceType::class, [
                'label' => $this->trans('Select feature type', 'Admin.Va_BulkFeatureManager'),
                'required' => true,
                'choices' => $this->getFeatures()
            ])
            ->add('feature_id_val', ChoiceType::class, [
                'label' => $this->trans('Select feature value','Admin.Va_BulkFeatureManager'),
                'required' => true,
                'choices' => $this->getFeatureValues(),
                'choice_attr' => $this->getFeatureValues(true)
            ])
        ;
    }

    protected function getFeatures(){
        $features = [];
        $sql = "SELECT id_unit_feature, unit_feature_name FROM " . _DB_PREFIX_ . "unit_feature";
        $res = \Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['unit_feature_name'] . ' (id:'.$row['id_unit_feature'] .' )';
            $features[$rowName] = $row['id_unit_feature'];
        }

        return $features;
    }

    /**
     * get Feature Values list
     * @param bool $insertFeatureId decide if you want to provide featureId as attribute. Default is set to false
     * @return array
     */
    private function getFeatureValues(bool $insertFeatureId = false): array
    {
        $featureVals = [];
        $sql = "SELECT ufv.id_unit_feature_value, ufv.id_unit_feature, ufv.value, uf.unit_feature_shortcut FROM " . _DB_PREFIX_. "unit_feature_value ufv INNER JOIN "._DB_PREFIX_. "unit_feature uf ON uf.id_unit_feature = ufv.id_unit_feature" ;
        $res = \Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['value'] .' (id: ' . $row['id_unit_feature_value']. ' | '. $row['unit_feature_shortcut'] .' )';
            $featureVals[$rowName] = $row['id_unit_feature_value'];
            if($insertFeatureId){
                $featureVals[$rowName] = array('data-feature_id' => $row['id_unit_feature']);
            }

        }

        return $featureVals;
    }
}
