<?php


namespace App\Form;

use App\Entity\Publication;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class GoogleUserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('_username', TextType::class, array(
                'constraints' => array(
                    new Length(array('min' => 2, 'minMessage' => 'Pas assez de caractères, 2 mini', 'max' => 15, 'maxMessage' => "Max. 15 caractères autorisés !")),
                    new Regex(array('pattern' => '/\./', 'match' => false, 'message' => 'Votre nom ne peut pas contenir de point')),
                ),
                'attr' => array(
                    'value' => '',
                ),
            ))
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
