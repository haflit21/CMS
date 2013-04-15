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
                    ->getQuery()
                    ->getOneOrNullResult()
                    ;
    }
}
