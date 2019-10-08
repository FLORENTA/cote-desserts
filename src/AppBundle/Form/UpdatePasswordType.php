<?php

namespace AppBundle\Form;

use AppBundle\Model\PasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PasswordType
 * @package AppBundle\Form
 */
class UpdatePasswordType extends AbstractType
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
                'label' => 'admin.password.form.username.label',
                'attr' => [
                    'placeholder' => 'admin.password.form.username.placeholder'
                ]
            ])
            ->add('lastPassword', PasswordType::class, [
                'required' => true,
                'label' => 'admin.password.form.last_password.label',
                'attr' => [
                    'placeholder' => 'admin.password.form.last_password.placeholder'
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'required' => true,
                'label' => 'admin.password.form.new_password.label',
                'attr' => [
                    'placeholder' => 'admin.password.form.new_password.placeholder'
                ]
            ])
            ->add('confirmNewPassword', PasswordType::class, [
                'required' => true,
                'label' => 'admin.password.form.confirm_new_password.label',
                'attr' => [
                    'placeholder' => 'admin.password.form.confirm_new_password.placeholder'
                ]
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
            'data_class' => PasswordModel::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_password';
    }
}