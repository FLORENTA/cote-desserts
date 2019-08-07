<?php

namespace AppBundle\Form;

use AppBundle\Model\LoginModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoginType
 * @package AppBundle\Form
 */
class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'login.form.username.label',
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'login.form.username.placeholder'
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'login.form.password.label',
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'login.form.password.placeholder'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'button-submit'
                ],
                'label' => 'generic.form.submit'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoginModel::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_login';
    }
}
