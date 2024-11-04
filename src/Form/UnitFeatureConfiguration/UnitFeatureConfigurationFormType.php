<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UnitFeatureConfigurationFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminFeatureConfiguration';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit_feature_name', TextType::class, [
              'label' => $this->trans('Unit feature name', self::domainTranslation),
              'help' => $this->trans('Enter unit feature name like (Weight, Capacity etc.)', self::domainTranslation),
            ])
            ->add('unit_feature_shortcut', TextType::class, [
                'label' => $this->trans('Unit feature shortcut', self::domainTranslation),
                'help' => $this->trans('Enter unit feature shortcut (l - for liters, kg - for kilograms, etc.)', self::domainTranslation),
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'match' => true,
                        'message' => $this->trans('Do not use digits or special characters in this field. (Min value length should be greater than 1 character)', 'Modules.Va_bulkfeaturemanager.Feature')
                    ])

                ],
            ])
        ;
    }

}
