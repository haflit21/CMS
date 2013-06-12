<?php

namespace CMS\ContentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class ContentTypeRepository extends EntityRepository
{

	public function getContentTypeById($id)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                    ->from('CMSContentBundle:CMContentType', 'c')
                    ->where('c.id = :id')
                    ->setParameter('id', $id)
                    ;
    }

    public function getAllContentType()
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c')
                    ->from('CMSContentBundle:CMContentType', 'c');
    }
}