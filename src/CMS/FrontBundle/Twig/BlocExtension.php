<?php
namespace CMS\FrontBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Environment;

class BlocExtension extends Twig_Extension
{

	private $environment = null;

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFilters()
    {
        return array('bloc' => new Twig_Filter_Method($this, 'blocFilter'));
    }

    public function blocFilter($value) 
    {           
 		//TAG : [%%module position:right, attributs:[cat_id:5, item_id:5]%%]
 		preg_match_all('/\[%%module position:(.*), attributs:\[(.*)\]%%\]/', $value, $values, PREG_PATTERN_ORDER);

 		//$values    		 = current($values);
 		$position  		 = $values[1][0];
 		$attributs_regex = preg_split('/[\s]*[,][\s]*/', $values[2][0], -1, PREG_SPLIT_DELIM_CAPTURE);
 		$attributs 		 = array();
 		$i=0;		
 		foreach ($attributs_regex as $attr) {
 			$attribut 				 = explode(':',$attr);
 			$attributs[$attribut[0]] = $attribut[1];
 		}

 		return $this->environment->render('CMSFrontBundle:Common:'.$position.'.html.twig', $attributs);
 	}

    public function getName()
    {
        return 'bloc_extension';
    }
}    