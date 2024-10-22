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

    public function getFeatures(): array{
        $features = [];
        $sql = "SELECT pf.id_feature, pf_lang.name from ps_feature pf INNER JOIN  ps_feature_lang pf_lang ON pf_lang.id_feature = pf.id_feature";
        $res = Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['name']  . ' (id feature: ' . $row['id_feature']. ')';
            $features[$rowName] = $row['id_feature'];
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
        $sql = "SELECT pf.id_feature, pf_val.id_feature_value, pf_val_lang.value from ps_feature pf INNER JOIN  ps_feature_lang pf_lang ON pf_lang.id_feature = pf.id_feature INNER JOIN ps_feature_value pf_val ON pf_val.id_feature = pf.id_feature INNER JOIN ps_feature_value_lang pf_val_lang ON pf_val_lang.id_feature_value = pf_val.id_feature_value";
        $res = Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['value'] . ' (id: ' . $row['id_feature_value']. ')';
            $featureVals[$rowName] = $row['id_feature_value'];
            if($insertFeatureId){
                $featureVals[$rowName] = array('data-feature_id' => $row['id_feature']);
            }

        }

        return $featureVals;
    }
}
//SELECT pf.id_feature, pf_lang.name, pf_val.id_feature_value, pf_val_lang.value from ps_feature pf INNER JOIN  ps_feature_lang pf_lang ON pf_lang.id_feature = pf.id_feature INNER JOIN ps_feature_value pf_val ON pf_val.id_feature = pf.id_feature INNER JOIN ps_feature_value_lang pf_val_lang ON pf_val_lang.id_feature_value = pf_val.id_feature_value
