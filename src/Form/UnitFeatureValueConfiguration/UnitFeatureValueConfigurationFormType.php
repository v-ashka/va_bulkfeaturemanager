<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UnitFeatureValueConfigurationFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminFeatureValueConfiguration';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit_feature_id', TextType::class, [
                'label' => $this->trans('Unit feature name', self::domainTranslation),
            ])
            ->add('unit_feature_value', TextType::class, [
                'label' => $this->trans('Unit feature value', self::domainTranslation),
                'help' => $this->trans('Enter unit feature value (ex. 100, 200, 300 etc.)', self::domainTranslation),
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9]+$/',
                        'match' => true,
                        'message' => $this->trans('Do not use digits or special characters in this field. (Min value length should be greater than 1 character)', 'Modules.Va_bulkfeaturemanager.Feature')
                    ])

                ],
            ])
        ;
    }

}
