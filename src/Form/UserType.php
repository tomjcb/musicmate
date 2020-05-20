<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('_username', TextType::class, array(
                'constraints' => array(
                    new Length(array('min' => 4, 'minMessage' => 'Pas assez de caractères, 4 mini')),
                ),
            ))
            ->add('_password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Les mot de passe doivent correspondre.',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('mail', TextType::class, array(
                'constraints' => array(
                    new Email(array('message' => "L'e-mail fourni est non valide.")),
                ),
            ))
            /*
            ->add('roles', ChoiceType::class,[
                'choices'=> [
                    'utilisateur' => 'ROLE_USER',
                    'admin' => 'ROLE_ADMIN'
                ]
            ])
            */
            ->add('nom', TextType::class, array(
                'constraints' => array(
                    new Length(array('min' => 2, 'minMessage' => 'Pas assez de caractères, 2 mini')),
                ),
            ))
            ->add('prenom', TextType::class, array(
                'constraints' => array(
                    new Length(array('min' => 2, 'minMessage' => 'Pas assez de caractères, 2 mini')),
                ),
            ))
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}