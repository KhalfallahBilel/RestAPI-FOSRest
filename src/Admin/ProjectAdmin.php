<?php
namespace App\Admin;

use App\Entity\Client;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class ProjectAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        $formMapper->add('description', TextType::class);
        $formMapper->add('url', TextType::class);
        $formMapper->add('dbname', TextType::class);
        $formMapper->add('storage', TextType::class);
        $formMapper->add('storageKey', TextType::class);
        $formMapper->add('client', EntityType::class, [
            'class' => Client::class,
            'choice_label' => 'id',
        ]);

        $formMapper->add('manager', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'firstname',
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('description');
        $datagridMapper->add('url');
        $datagridMapper->add('dbname');
        $datagridMapper->add('storage');
        $datagridMapper->add('storageKey');
        $datagridMapper->add('client');
        $datagridMapper->add('manager');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('description');
        $listMapper->addIdentifier('url');
        $listMapper->addIdentifier('dbname');
        $listMapper->addIdentifier('storage');
        $listMapper->addIdentifier('storageKey');
        $listMapper->addIdentifier('client');
        $listMapper->addIdentifier('manager');
    }
}