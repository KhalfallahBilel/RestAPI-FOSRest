<?php
namespace App\Admin;

use App\Entity\Information;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('firstname', TextType::class);
        $formMapper->add('lastname', TextType::class);
        $formMapper->add('email', TextType::class);
        $formMapper->add('password', TextType::class);
        $formMapper->add('information', EntityType::class, [
            'class' => Information::class,
            'choice_label' => 'city',
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstname');
        $datagridMapper->add('lastname');
        $datagridMapper->add('email');
        $datagridMapper->add('password');
        $datagridMapper->add('information');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('firstname');
        $listMapper->addIdentifier('lastname');
        $listMapper->addIdentifier('email');
        $listMapper->addIdentifier('password');
        $listMapper->addIdentifier('information');
    }
}