<?php

namespace AppBundle\Form;

use AppBundle\Model\StatisticModel;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\StatisticType as StatisticTypeEntity;

/**
 * Class StatisticType
 * @package AppBundle\Form
 */
class StatisticType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'statistics.form.start_time',
                'required' => false
            ])
            ->add('endTime', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'statistics.form.end_time',
                'required' => false
            ])
            ->add('statisticType', EntityType::class, [
                'class' => StatisticTypeEntity::class,
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('statistic_type')
                        ->orderBy('statistic_type.type');
                },
                'choice_label' => 'type',
                'label' => 'statistics.form.type'
            ])
            ->add('bot', ChoiceType::class, [
                'choices' => [
                    'statistics.form.bot.options.no' => false,
                    'statistics.form.bot.options.yes' => true
                ],
                'required' => true,
                'expanded' => true,
                'data' => false,
                'label' => 'statistics.form.bot'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'generic.form.submit',
                'attr' => [
                    'class' => 'button-submit'
                ]
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StatisticModel::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_statistic';
    }
}
