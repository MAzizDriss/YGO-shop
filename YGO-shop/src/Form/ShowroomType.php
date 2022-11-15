<?php

namespace App\Form;

use App\Entity\Showroom;
use App\Repository\CardRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShowroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dump($options);
        $showroom = $options['data'] ?? null;
        $member = $showroom->getOwner();

        $builder
            ->add('sr_name')
            ->add('description')
            ->add('published')
            ->add('owner', null, [
                'disabled'   => true,
            ])
            ->add('cards', null, [
                'query_builder' => function (CardRepository $er) use ($member) {
                        return $er->createQueryBuilder('g')
                        ->leftJoin('g.deck', 'i')
                        ->andWhere('i.member = :member')
                        ->setParameter('member', $member)
                        ;
                    }
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Showroom::class,
        ]);
    }
}
