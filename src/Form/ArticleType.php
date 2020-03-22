<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, ["label" => "Titre de l'article"])
            ->add('content',TextareaType::class, ["label" => "Contenu de l'article"])
            ->add('media', FileType::class, [
                'label' => "Image de l'article",
                'mapped'=>false,
                "required"=> false
                ])
            ->add('categories', EntityType::class, [
                'by_reference' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                "expanded"=> true,
            ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
