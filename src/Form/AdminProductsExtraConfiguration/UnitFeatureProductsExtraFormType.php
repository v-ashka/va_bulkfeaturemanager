<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Va_bulkfeaturemanager\Service\BulkFeatureManagerService;

class UnitFeatureProductsExtraFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminProductsExtraFeatureConfiguration';
    private $service;

    public function __construct(TranslatorInterface $translator, array $locales, BulkFeatureManagerService $service)
    {
        parent::__construct($translator, $locales);
        $this->service = $service;
    }

    /**
     * Build form with specified fields
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
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
                'choices' => $this->service->getFeatures(),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->trans('This value cannot be blank.', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                ],
            ])
            ->add('feature_id_val', ChoiceType::class, [
                'label' => $this->trans('Select feature value','Admin.Va_BulkFeatureManager'),
                'required' => true,
                'choices' => $this->service->getFeatureValues(),
                'choice_attr' => $this->service->getFeatureValues(true),
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $this->trans('This value cannot be blank.', 'Modules.Va_bulkfeaturemanager.Feature')
                    ]),
                ],

            ])
        ;
    }


}
