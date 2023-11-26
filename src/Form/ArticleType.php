<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\THEME;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class)
        ->add('description', TextareaType::class)
        ->add('image', FileType::class, ['mapped' => false]) // Assuming you're using VichUploaderBundle or similar for file uploads
        ->add('date', DateType::class)
        ->add('theme_article', EntityType::class, [
            'class' => THEME::class,
            'choice_label' => 'name', // Adjust this based on your Theme entity properties
            'multiple' => true, // Change to false if it's a ManyToOne relationship
            'expanded' => true, // Set to false if you want a dropdown
        ])
    ;
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
