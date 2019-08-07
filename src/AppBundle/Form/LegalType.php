<?php

namespace AppBundle\Form;

use AppBundle\Entity\Legal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LegalType
 * @package AppBundle\Form
 */
class LegalType extends AbstractType
{
    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * LegalType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'legal.content.label',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button-submit'
                ],
                'label' => 'generic.form.submit'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                if (empty($event->getData()->getContent())) {
                    $event->getForm()->addError(
                        new FormError($this->translator->trans('generic.form.invalid'))
                    );
                }
            });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Legal::class,
            'edit' => false
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_legal';
    }
}
