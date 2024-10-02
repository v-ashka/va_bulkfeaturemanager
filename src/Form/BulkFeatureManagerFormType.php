<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
class BulkFeatureManagerFormType extends TranslatorAwareType{

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('feature_id', ChoiceType::class, [
           'label' => $this->trans('Select feature to add to elements', [], 'Admin.Va_BulkFeatureManager'),
            'required' => false,
            'choices' => $this->getFeatureValues()
        ])
        ->add('feature_id_value', ChoiceType::class, [
            'label' => $this->trans('Select feature value to add to elements', [], 'Admin.Va_BulkFeatureManager')
        ])
        ;
    }

    public function getFeatureValues(): array{
        $features = [];
        $sql = "SELECT pf.id_feature, pf_lang.name from ps_feature pf INNER JOIN  ps_feature_lang pf_lang ON pf_lang.id_feature = pf.id_feature";
        $res = Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['name'];
            $features[$rowName] = $row['id_feature'];
        }
        return $features;
    }
}
//SELECT pf.id_feature, pf_lang.name, pf_val.id_feature_value, pf_val_lang.value from ps_feature pf INNER JOIN  ps_feature_lang pf_lang ON pf_lang.id_feature = pf.id_feature INNER JOIN ps_feature_value pf_val ON pf_val.id_feature = pf.id_feature INNER JOIN ps_feature_value_lang pf_val_lang ON pf_val_lang.id_feature_value = pf_val.id_feature_value
