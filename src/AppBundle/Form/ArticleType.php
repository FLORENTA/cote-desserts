<?php

namespace AppBundle\Form;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ArticleType
 * @package AppBundle\Form
 */
class ArticleType extends AbstractType
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * ArticleType constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'admin.article.form.title'
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                #Force setters to be called, otherwise, not association done ! https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                'by_reference' => false
            ])
            ->add('categories', CollectionType::class, [
                'label' => false,
                'entry_type' => CategoryCollectionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('newsletter', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.article.form.newsletter.label'
            ])
            ->add('file', FileType::class, [
                'label' => 'admin.article.form.pdf.label',
                'required' => false,
                'attr' => [
                    'accept' => 'application/pdf'
                ],
                // Avoid conflict with string and instance of Uploaded File for edition
                'data_class' => null
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
            'data_class' => Article::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'appbundle_article';
    }
}
