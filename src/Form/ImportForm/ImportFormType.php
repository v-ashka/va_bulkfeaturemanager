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
            ->add('feature_file', FileType::class, [
                'label' => $this->trans('Feature file', self::domainTranslator),
            ])
            ->add('feature_values_file', FileType::class, [
                'label' => $this->trans('Feature values file', self::domainTranslator),
            ])
            ->add('feature_products_file', FileType::class, [
                'label' => $this->trans('Feature values products file', self::domainTranslator),
            ])
            ->add('skip', SwitchType::class, [
                'label' => $this->trans('Skip first row if head', 'Modules.Bulkpriceupdater.Admin'),
                'required' => true,
                'empty_data' => 1,
            ])
            ->add('separator', TextType::class, [
                'label' => $this->trans('Csv file separator', 'Modules.Bulkpriceupdater.Admin'),
                'required' => true,
                'empty_data' => ImportSettings::DEFAULT_SEPARATOR,
            ])
            ->add('entity', HiddenType::class)
            ->add('iso_lang', HiddenType::class)
            ->add('multiple_value_separator', HiddenType::class)
            ->add('truncate', HiddenType::class)
            ->add('regenerate', HiddenType::class)
            ->add('match_ref', HiddenType::class)
            ->add('forceIDs', HiddenType::class)
            ->add('sendemail', HiddenType::class)
            ->add('csv', HiddenType::class)
            ;
    }

}
