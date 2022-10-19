<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie', TextType::class,[
                'label'=> 'Nom de la sortie :'
            ])
            ->add('dateDebut', DateTimeType::class,[
                'label'=>'Date et heure de la sortie :'
                ])
            ->add('duree', NumberType::class,[
                'label'=> 'Durée :'
            ])
            ->add('dateCloture', DateType::class,[
                'label'=>'Date de clôture :'
            ])
            ->add('nbInscriptionsMax', NumberType::class,[
                'label'=>'Nombre de places :'
            ])
            ->add('detailSortie', TextType::class,[
                'label'=>'Description et infos :'
            ])
            ->add('imageFile', FileType::class, [
                    'mapped' => false,
                    'label'=>'Illustration de la soirée',
                    'required' => false])
//            ->add('urlPhoto')
//            ->add('organisateur')
//            ->add('site')
            ->add('lieu', EntityType::class,[
                'class'=> Lieu::class,
                'choice_label'=>'nom_lieu',
                'expanded'=>false,
                'multiple'=>false
            ])
//            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
