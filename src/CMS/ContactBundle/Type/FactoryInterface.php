<?php

namespace CMS\ContactBundle\Type;

interface FactoryInterface
{
    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm();
}
