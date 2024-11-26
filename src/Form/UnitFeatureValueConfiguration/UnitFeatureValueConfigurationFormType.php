<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Db;
use Symfony\Contracts\Translation\TranslatorInterface;
use Va_bulkfeaturemanager\Service\BulkFeatureManagerService;

class UnitFeatureValueConfigurationFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminFeatureValueConfiguration';
    private $service;

    public function __construct(TranslatorInterface $translator, array $locales, BulkFeatureManagerService $service)
    {
        parent::__construct($translator, $locales);
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit_feature_id', ChoiceType::class, [
                'label' => $this->trans('Unit feature name', self::domainTranslation),
                'choices' => $this->service->getFeatures(),
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 255]
                        ),
                    ]),
                    new Assert\NotBlank(),
                ]
            ])
            ->add('unit_feature_value', TextType::class, [
                'label' => $this->trans('Unit feature value', self::domainTranslation),
                'help' => $this->trans('Enter unit feature value (ex. 100, 200, 300 etc.)', self::domainTranslation),
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+(.\d+)?$/',
                        'match' => true,
                        'message' => $this->trans('This field accept numbers only (2, 100, 2.25, 0.750, etc.)', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                    new Assert\NotBlank([
                        'message' => $this->trans('This value cannot be blank.', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                    new Assert\Length([
                        'max' => 15,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 15]
                        ),
                    ]),
                ],
            ])
        ;
    }

}
