<?php

namespace CMS\ContentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class ContentRepository extends EntityRepository
{
    public function getContentByLangId($lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                       ->from('CMSContentBundle:CMContent', 'c')
                       ->leftjoin('c.language', 'l')
                       ->where('l.id = :id')
                     ->setParameter('id', $lang)
                    ->getQuery()
                       ->getResult();
    }

    public function getContentByLangIdQuery($lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                       ->from('CMSContentBundle:CMContent', 'c')
                       ->leftjoin('c.language', 'l')
                       ->where('l.id = :id')
                     ->setParameter('id', $lang)
                     ->orderby('c.created', 'desc')
                    ;
    }

    public function getTotalElements($lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('Count(c)')
                       ->from('CMSContentBundle:CMContent', 'c')
                       ->leftjoin('c.language', 'l')
                       ->where('l.id = :id')
                     ->setParameter('id', $lang)
                    ->getQuery()
                       ->getSingleScalarResult();
    }

    public function findByUrl($url)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                    ->from('CMSContentBundle:CMContent','c')
                    ->where('c.url=:url')
                    ->setParameter('url',$url)
                    ->andWhere('c.state=:state')
                    ->setParameter('state', 1)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

    public function findByCategories($idCats, $idLang, $categoryParent)
    {
        $query_builder =  $this->_em
                               ->createQueryBuilder()
                               ->select('co, c, ct')
                               ->from('CMSContentBundle:CMContent','co')
                               ->join('co.categories', 'c')
                               ->join('co.contenttype', 'ct')
                               ->where('c.id IN (:idcats)')
                               ->setParameter('idcats', $idCats)
                               ->andWhere('c.published=:published')
                               ->setParameter('published', 1)
                               ->andWhere('co.state=:pub_content')
                               ->setParameter('pub_content', 1);
        if($categoryParent->getOrdreClassement() != 'id' && $categoryParent->getOrdreClassement() != 'title') {
            $query_builder = $query_builder->join('co.fieldvalues', 'fv')
                                           ->join('fv.field', 'f')
                                           ->andWhere('f.name=:name')
                                           ->setParameter('name', $categoryParent->getOrdreClassement()) 
                                           ->orderby('fv.value', $categoryParent->getDirectionClassement());                       
        } else if($categoryParent->getOrdreClassement() == 'id') {
            $query_builder = $query_builder->orderby('co.id', $categoryParent->getDirectionClassement());
        } else if($categoryParent->getOrdreClassement() == 'title') {
            $query_builder = $query_builder->orderby('co.title', $categoryParent->getDirectionClassement());
        }
            
        $query_builder = $query_builder->getQuery()
                                       ->getResult();
        return $query_builder;                                       
    }

    public function getContentByFieldValue($name, $value)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                    ->from('CMSContentBundle:CMContent', 'c')
                    ->leftjoin('c.fieldvalues','fv')
                    ->leftjoin('fv.field', 'f')
                    ->where('f.name=:name')
                    ->andWhere('fv.value=:value')
                    ->setParameters(array('name' => $name, 'value' => serialize($value)))
                    ->getQuery()
                    ->getOneOrNullResult();
    }

}
