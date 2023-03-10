<?php

namespace App\Form;

use App\Entity\MediaFile;
use Doctrine\DBAL\Types\SmallIntType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MediaFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* ->add('type', TypeTextType::class) */
            ->add('file_url', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                    ],
            ])
            /* ->add('file_likes', IntegerType::class)
            ->add('file_owner', IntegerType::class)
            ->add('save', SubmitType::class) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaFile::class,
        ]);
    }
}
