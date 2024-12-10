<?php

namespace Va_bulkfeaturemanager\Form\ImportForm;

use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
class ImportFormType extends TranslatorAwareType
{
    const domainTranslator = 'Va_bulkfeaturemanager.Modules.Import.FormType';
    public function buildForm(FormBuilderInterface $builder, array $options){
    $builder
        ->add('import_type', ChoiceType::class, [
            'label' => $this->trans('Choose what do you want to import', self::domainTranslator),
            'choices' => [
                $this->trans('Features', self::domainTranslator) => 0,
                $this->trans('Feature values', self::domainTranslator) => 1,
                $this->trans('Products attached to features', self::domainTranslator) => 2,
            ],
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'help' => $this->trans('If you want to import new features with attached products, you should import files in correct order.', self::domainTranslator),
        ])
            ->add('feature_file', FileType::class, [
                'label' => $this->trans('Select a file to import', self::domainTranslator),
            ])
            ->add('skip', SwitchType::class, [
                'label' => $this->trans('Skip first row if head', self::domainTranslator),
                'required' => true,
                'empty_data' => 1,
            ])
            ->add('separator', TextType::class, [
                'label' => $this->trans('Csv file separator', self::domainTranslator),
                'required' => true,
                'empty_data' => ImportSettings::DEFAULT_SEPARATOR,
            ])
            ->add('entity', HiddenType::class)
            ->add('iso_lang', HiddenType::class)
            ->add('multiple_value_separator', HiddenType::class)
            ->add('truncate', HiddenType::class)
            ->add('regenerate', HiddenType::class)
            ->add('match_ref', HiddenType::class)
            ->add('forceIDs', SwitchType::class, [
                'label' => $this->trans('Force all ids', self::domainTranslator),
                'required' => true,
                'empty_data' => false,
                'help' => $this->trans('Enable this option if you want to preserve the identifiers of imported elements (their ID), otherwise PrestaShop will ignore them and continue to automatically assign sequential numbers.', self::domainTranslator),
            ])
            ->add('sendemail', HiddenType::class)
            ->add('csv', HiddenType::class)
            ;
    }

}
