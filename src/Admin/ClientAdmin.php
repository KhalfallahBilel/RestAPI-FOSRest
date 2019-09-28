<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Information;
use App\Entity\User;

final class ClientAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('notes', TextType::class);
        $formMapper->add('manager', EntityType::class, [
                            'class' => User::class,
                            'choice_label' => 'firstname',
                        ]);
        $formMapper->add('information', EntityType::class, [
            'class' => Information::class,
            'choice_label' => 'city',
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('notes');
        $datagridMapper->add('manager');
        $datagridMapper->add('information');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('notes');
        $listMapper->addIdentifier('manager');
        $listMapper->addIdentifier('information');
    }
}