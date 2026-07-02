<?php

namespace App\Form;

use App\Entity\Fichier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', FileType::class, [
                'label' => 'Brochure (Fichier PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    // Utilisation des arguments nommés (Symfony 7+)
                    new File(
                        maxSize: '1024k',
                        mimeTypes: [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        mimeTypesMessage: 'Veuillez charger un document PDF valide.'
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fichier::class,
        ]);
    }
}