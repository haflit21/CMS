<?php
/**
 * This sniff prohibits the use of Perl style hash comments.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/DataFixtures/ORM/LoadContentManagerData.php
 */
namespace CMS\ContentBundleDataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use CMS\ContentBundle\Entity\CMLanguage;

/**
 * Load GameData
 *
 * @category PHP
 * @package  ContentBundle
 * @author   Damien Corona <leoncorono@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version  SVN: 0.1: CMS Symfony
 * @link     https://github.com/damienc38/CMS/blob/master/src/CMS/ContentBundle/DataFixtures/ORM/LoadContentManagerData.php
 */

class LoadContentManagerData implements FixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager manage des objets
     *
     * @return null
     */
    public function load(ObjectManager $manager)
    {
        $langs = $this->getLangues();
        foreach ($langs as $key => $lg) {
            $lang = new CMLanguage;
            $lang->setTitle($lg['title']);
            $lang->setIso($lg['iso']);
            $lang->setPublished($lg['published']);
            $lang->setDefaultLan($lg['default']);

            $manager->persist($lang);
        }

        $manager->flush();
    }

    /**
     * Retourne une liste statique de langue
     *
     * @return tableau de langues
     */
    public function getLangues()
    {
        return array(
                array(
                    "title"     => "français",
                    "iso"       => "fr-FR",
                    "published" => 1,
                    "default"   => 1
                ),
                array(
                    "title"     => "anglais",
                    "iso"       => "en-UK",
                    "published" => 1,
                    "default"   => 0
                ),
                array(
                    "title"     => "allemand",
                    "iso"       => "de-DE",
                    "published" => 1,
                    "default"   => 0
                )
            );
    }
}