<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                    new Assert\NotBlank([
                        'message' => $this->trans('This value cannot be blank.', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                    new Assert\Length([
                        'max' => 64,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 64]
                        ),
                    ]),
                ],
            ])
            ->add('unit_feature_base_value', TextType::class, [
                'label' => $this->trans('Unit feature base value', self::domainTranslation),
                'help' => $this->trans('Enter unit feature base value for calculating price per feature', self::domainTranslation),
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(.\d+)?$/',
                        'match' => true,
                        'message' => $this->trans('This field accept digits only', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                    new Assert\NotBlank([
                        'message' => $this->trans('This value cannot be blank.', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                    new Assert\Length([
                        'max' => 64,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 64]
                        ),
                    ]),
                ],
            ])
        ;
    }

}
