<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Form;

use Db;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BulkFeatureManagerFormType extends TranslatorAwareType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('feature_method', ChoiceType::class, [
                'label' => $this->trans('Select feature action', 'Admin.Va_BulkFeatureManager'),
                'choices' => [
                    'Add Feature' => 'add_feature',
                    'Remove feature' => 'remove_feature',
                    'Delete all features from product' => 'delete_all',
                ],
                'required' => true,
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
        $res = Db::getInstance()->executeS($sql);

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
        $res = Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['value'] .' '. $row['unit_feature_shortcut'] .' (id: ' . $row['id_unit_feature_value']. ')';
            $featureVals[$rowName] = $row['id_unit_feature'];
            if($insertFeatureId){
                $featureVals[$rowName] = array('data-feature_id' => $row['id_unit_feature']);
            }

        }

        return $featureVals;
    }
}
