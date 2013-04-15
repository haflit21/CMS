<?php

namespace CMS\ContentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class CategoryRepository extends EntityRepository
{
    public function getCategoryByLangId($lang)
    {
        return $this->getCategoryByLangIdQuery($lang)
                    ->getQuery()
                       ->getResult();
    }

    public function getCategoryByLangIdQuery($lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                       ->from('CMSContentBundle:CMCategory', 'c')
                       ->leftjoin('c.language', 'l')
                       ->where('l.id = :id')
                       ->orderby('c.root,c.lft', 'asc')
                     ->setParameter('id', $lang)
                    ;
    }

    public function getTotalElements($lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(c)')
                    ->from('CMSContentBundle:CMCategory', 'c')
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
                    ->select('c, co, ct')
                    ->from('CMSContentBundle:CMCategory','c')
                    ->join('c.contents', 'co')
                    ->leftjoin('co.contenttype', 'ct')
                    ->where('c.url=:url')
                    ->setParameter('url',$url)
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }

}
