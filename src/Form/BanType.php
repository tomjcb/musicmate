<?php


namespace App\Form;

use App\Entity\Ban;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class BanType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('motif', TextType::class, array(
                'constraints' => array(
                    new Length(array('min' => 2, 'minMessage' => 'Pas assez de caractères, 2 mini', 'max' => 100, 'maxMessage' => "Max. 100 caractères autorisés !")),
                ),
            ))
            ->add('endBan', ChoiceType::class, [
                'choices' => [
                    '1jour' => new \DateTime('+1 day'),
                    '1semaine' => new \DateTime('+1 week'),
                    '1mois' => new \DateTime('+1 month'),
                ],
                'placeholder' => '--- Choisir un délai de ban ---'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ban::class,
        ]);
    }

}
