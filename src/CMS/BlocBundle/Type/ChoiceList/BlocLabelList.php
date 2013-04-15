<?php

namespace CAF\BlocBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class BlocLabelList implements ChoiceListInterface
{
    public function getChoices()
    {
        return array('BlocMenu'=>'module menu', 'BlocActu'=>'module actualités', 'BlocHtml' =>'module text');
    }

    public function getIndicesForChoices(array $choices)
    {
    }

    public function getValues()
    {
        $this::parent();
    }

    public function getPreferredViews()
    {
    }

    public function getRemainingViews()
    {
    }

    public function getChoicesForValues(array $values)
    {
    }

    public function getValuesForChoices(array $choices)
    {
    }

    public function getIndicesForValues(array $values)
    {
    }
}
