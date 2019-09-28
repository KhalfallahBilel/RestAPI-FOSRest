<?php
namespace App\Admin;

use App\Entity\City;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class InformationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('number', TextType::class);
        $formMapper->add('street', TextType::class);
        $formMapper->add('city', EntityType::class, [
            'class' => City::class,
            'choice_label' => 'name',
        ]);
        $formMapper->add('address_complement', TextType::class);
        $formMapper->add('phone', TextType::class);
        $formMapper->add('mobile', TextType::class);
        $formMapper->add('notes', TextType::class);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('number');
        $datagridMapper->add('street');
        $datagridMapper->add('city');
        $datagridMapper->add('address_complement');
        $datagridMapper->add('phone');
        $datagridMapper->add('mobile');
        $datagridMapper->add('notes');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('number');
        $listMapper->addIdentifier('street');
        $listMapper->addIdentifier('city');
        $listMapper->addIdentifier('address_complement');
        $listMapper->addIdentifier('phone');
        $listMapper->addIdentifier('mobile');
        $listMapper->addIdentifier('notes');
    }
}