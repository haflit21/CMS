<?php
namespace CMS\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use CMS\AdminBundle\Entity\Settings;

class LoadSettingsData implements FixtureInterface
{

	/**
	 * @{@inheritdoc}
	 */
	public function load(ObjectManager $manager)
	{
		$settings = new Settings();
		$settings->setOptionName('titre_site');
		$settings->setTypeField('text');
		$manager->persist($settings);

		$settings = new Settings();
		$settings->setOptionName('url_site');
		$settings->setTypeField('text');
		$manager->persist($settings);

		$settings = new Settings();
		$settings->setOptionName('email_admin');
		$settings->setTypeField('email');
		$manager->persist($settings);

		$settings = new Settings();
		$settings->setOptionName('timezone_string');
		$settings->setTypeField('timezone');
		$manager->persist($settings);

		$settings = new Settings();
		$settings->setOptionName('nb_articles_page');
		$settings->setTypeField('text');
		$settings->setOptionValue(10);

		$manager->persist($settings);

		$settings = new Settings();
		$settings->setOptionName('nb_articles_rss');
		$settings->setTypeField('text');
		$settings->setOptionValue(10);

		$manager->persist($settings);

		$manager->flush();
	}
}