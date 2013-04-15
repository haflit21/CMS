<?php
namespace CMS\BlocBundle\Classes;

public interface BlocInterface
{
    public function html(\Doctrine\Common\Collections\ArrayCollection $parameters);

}
