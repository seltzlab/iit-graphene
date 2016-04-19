<?php

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends BaseUserAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('Utente')
                ->with('Profilo', array('class' => 'col-md-6'))->end()
                ->with('Generale', array('class' => 'col-md-6'))->end()
            ->end()
        ;

        $embedded = false;
        if ($this->hasParentFieldDescription()) { // this Admin is embedded
            $getter = 'get' . $this->getParentFieldDescription()->getFieldName();
            $parent = $this->getParentFieldDescription()->getAdmin()->getSubject();
            if ($parent) {
                $subject = $parent->$getter();
                $embedded = true;
            } else {
                $subject = null;
            }
        } else {
            $subject = $this->getSubject();
        }

        $now = new \DateTime();
        $formMapper
            ->tab('Utente')
                ->with('Generale')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', array(
                        'required' => (!$subject || is_null($subject->getId())),
                    ))
                ->end()
        ;

        if ($embedded !== true) {
            $formMapper
                ->with('Profilo')
                    ->add('firstname', null, array('required' => true, "label" => "Nome"))
                    ->add('lastname', null, array('required' => true, "label" => "Cognome"))
                    ->add('gender', 'sonata_user_gender', array(
                        'required' => true,
                        'translation_domain' => $this->getTranslationDomain(),
                    ))
                    ->add('locale', 'locale', array('required' => true))
                    ->add('phone', null, array('required' => true))
                    ->add('dateOfBirth', 'sonata_type_date_picker', array(
                        'label' => "Data di nascita",
                        'years' => range(1900, $now->format('Y')),
                        'dp_min_date' => '1-1-1900',
                        'dp_max_date' => $now->format('c'),
                        'required' => false,
                        'format' => 'dd-MM-yyyy'
                    ))
                ->end();
        }

        $formMapper->end();

        $currentUser = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
        if ($this->getSubject() && $currentUser->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                ->with('Status', array('class' => 'col-md-4'))
                ->add('locked', null, array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups', array('class' => 'col-md-4'))
                ->add('groups', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()
                ->with('Roles', array('class' => 'col-md-12'))
                ->add('realRoles', 'sonata_security_roles', array(
                    'label'    => 'form.label_roles',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ))
                ->end()
                ->end()
            ;


        }

    }
}