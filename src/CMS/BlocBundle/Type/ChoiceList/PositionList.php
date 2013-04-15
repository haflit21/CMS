<?php

namespace CAF\BlocBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class PositionList implements ChoiceListInterface
{
    public function getChoices()
    {
        return array('top'=>'menu haut', 'banner'=>'menu bannière','bottom'=>'menu bas', 'top_middle'=>'milieu haut', 'bottom_middle'=>'milieu bas', 'left'=>'gauche', 'right'=>'droite');
    }

    public function getIndicesForChoices(array $choices)
    {
    }

    public function getValues()
    {
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
