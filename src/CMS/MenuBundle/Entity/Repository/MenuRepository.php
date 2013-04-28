<?php

namespace CMS\MenuBundle\Entity\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
/**
 * MenuRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuRepository extends NestedTreeRepository
{
    public function getMenuParent($menu_taxonomy)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('node')
                    ->from('CMSMenuBundle:Menu', 'node')
                    ->orderBy('node.root, node.lft', 'ASC')
                    ->where('node.id_menu_taxonomy=:menu_taxonomy')
                    ->setParameter('menu_taxonomy',$menu_taxonomy)
                    ;
    }

    public function getDefaultUrl()
    {
        $url = $this->_em
                    ->createQueryBuilder()
                    ->select('c.url')
                    ->from('CMSMenuBundle:Menu', 'm')
                    ->join('m.category', 'c')
                    ->where('m.default_page=:default')
                    ->setParameter('default',1)
                    ->getQuery()
                    ->getResult();
        if ($url == null) {
            $url = $this->_em
                    ->createQueryBuilder()
                    ->select('c.url')
                    ->from('CMSMenuBundle:Menu', 'm')
                    ->join('m.content', 'c')
                    ->where('m.default_page=:default')
                    ->setParameter('default_page',1)
                    ->getQuery()
                    ->getResult();
        }

        return current($url);
    }

    public function getEntryMenu($item_id,$cat_id)
    {
        $entry = null;
        if ($item_id != '' && $cat_id != '') {
            $entry = $this->_em
                    ->createQueryBuilder()
                    ->select('m')
                    ->from('CMSMenuBundle:Menu', 'm')
                    ->join('m.category', 'c')
                    ->join('m.content', 'co')
                    ->where('c.id=:cat_id')
                    ->andwhere('co.id=:item_id')
                    ->setParameter('cat_id',$cat_id)
                    ->setParameter('item_id',$item_id)
                    ->getQuery()
                    ->getResult();
            if (!is_object($entry)) {
                $entry = $this->_em
                    ->createQueryBuilder()
                    ->select('m')
                    ->from('CMSMenuBundle:Menu', 'm')
                    ->join('m.category', 'c')
                    ->where('c.id=:cat_id')
                    ->setParameter('cat_id',$cat_id)
                    ->getQuery()
                    ->getResult();
            }        
        } else if ($item_id != '') {
            $entry = $this->_em
                        ->createQueryBuilder()
                        ->select('m')
                        ->from('CMSMenuBundle:Menu', 'm')
                        ->join('m.content', 'c')
                        ->where('c.id=:item_id')
                        ->setParameter('item_id',$item_id)
                        ->getQuery()
                        ->getResult();
        } elseif ($cat_id != null) {
            $entry = $this->_em
                    ->createQueryBuilder()
                    ->select('m')
                    ->from('CMSMenuBundle:Menu', 'm')
                    ->join('m.category', 'c')
                    ->where('c.id=:cat_id')
                    ->setParameter('cat_id',$cat_id)
                    ->getQuery()
                    ->getResult();
        }

        return $entry;
    }

    public function getEntriesMenuByLangQuery($menu_taxonomy,$lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('node')
                    ->from('CMSMenuBundle:Menu', 'node')
                    ->leftjoin('node.language', 'l')
                    ->orderBy('node.root, node.lft', 'ASC')
                    ->where('node.id_menu_taxonomy=:menu_taxonomy')
                    ->setParameter('menu_taxonomy',$menu_taxonomy)
                    ->andWhere('l.id=:id')
                    ->setParameter('id',$lang)
                    ->getQuery()
                    ;
    }

    public function getTotalElements($menu_taxonomy,$lang)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(node)')
                    ->from('CMSMenuBundle:Menu', 'node')
                    ->leftjoin('node.language', 'l')
                    ->orderBy('node.root, node.lft', 'ASC')
                    ->where('node.id_menu_taxonomy=:menu_taxonomy')
                    ->setParameter('menu_taxonomy',$menu_taxonomy)
                    ->andWhere('l.id=:id')
                    ->setParameter('id',$lang)
                    ->getQuery()
                    ->getSingleScalarResult();  
    }

    public function getEntryMenuByUrlIntern($url_intern)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('node')
                    ->from('CMSMenuBundle:Menu', 'node')
                    ->leftjoin('node.id_menu_taxonomy', 'mt')
                    ->where('mt.is_menu_admin=:is_menu_admin')
                    ->setParameter('is_menu_admin', 1)
                    ->andWhere('node.name_route=:url_intern')
                    ->setParameter('url_intern', $url_intern)
                    ->getQuery()
                    ->getResult();
    }

}
