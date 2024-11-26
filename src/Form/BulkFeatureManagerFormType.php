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
use Symfony\Contracts\Translation\TranslatorInterface;
use Va_bulkfeaturemanager\Service\BulkFeatureManagerService;

class BulkFeatureManagerFormType extends TranslatorAwareType{

    private $service;
    public function __construct(TranslatorInterface $translator, array $locales, BulkFeatureManagerService $service)
    {
        parent::__construct($translator, $locales);
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('feature_method', ChoiceType::class, [
                'label' => $this->trans('Select feature action', 'Admin.Va_BulkFeatureManager'),
                'choices' => [
                    'Add Feature' => 'add_feature',
                    'Remove feature' => 'remove_feature',
                    'Delete all features from selected products' => 'delete_all',
                ],
                'required' => true,
            ])
            ->add('feature_id', ChoiceType::class, [
           'label' => $this->trans('Select feature type', 'Admin.Va_BulkFeatureManager'),
            'required' => true,
            'choices' => $this->service->getFeatures()
        ])
            ->add('feature_id_val', ChoiceType::class, [
               'label' => $this->trans('Select feature value','Admin.Va_BulkFeatureManager'),
               'required' => true,
               'choices' => $this->service->getFeatureValues(),
                'choice_attr' => $this->service->getFeatureValues(true)
           ])

        ;
    }
}
