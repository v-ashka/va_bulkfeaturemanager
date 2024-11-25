<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Db;
class UnitFeatureValueConfigurationFormType extends TranslatorAwareType
{
    const domainTranslation = 'Modules.Va_bulkfeaturemanager.AdminFeatureValueConfiguration';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unit_feature_id', ChoiceType::class, [
                'label' => $this->trans('Unit feature name', self::domainTranslation),
                'choices' => $this->getFeatures(),
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

    protected function getFeatures(){
        $features = [];
        $sql = "SELECT id_unit_feature, unit_feature_name FROM " . _DB_PREFIX_ . "unit_feature";
        $res = Db::getInstance()->executeS($sql);

        foreach($res as $row){
            $rowName = $row['unit_feature_name'] . ' (id:'.$row['id_unit_feature'] .' )';
            $features[$rowName] = $row['id_unit_feature'];
        }

        return $features;
    }

}
