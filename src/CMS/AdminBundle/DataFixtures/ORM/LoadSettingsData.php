<?php
namespace CMS\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use CMS\AdminBundle\Entity\Settings;
use CMS\MenuBundle\Entity\Menu;
use CMS\MenuBundle\Entity\MenuTaxonomy;
use CMS\BlocBundle\Entity\BlocTaxonomy;
use CMS\BlocBundle\Entity\BlocMenu;

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

		$menuTax = $this->loadMenu($manager);
		$this->loadBloc($manager, $menuTax);
	}

	public function loadBloc(ObjectManager $manager, MenuTaxonomy $menuTax) {

		$lang_default = $manager->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);


		$bloc = new Bloc();
		$bloc->setTitle('Menu Admin');
		$bloc->setPosition('admin_menu_left');
		$bloc->setType('BlocMenu');
		$bloc->setPublished(1);
		$bloc->setAllPublished(1);
		$bloc->setLanguage($language);
		$manager->persist($bloc);

		$blocMenu = new BlocMenu();
		$blocMenu->setBloc($bloc);
		$blocMenu->setMenu($menuTax);
		$blocMenu->setDisplayType('admin_v');
		$manager->persist($bloc_menu);

		$manager->flush();
	}

	public function loadMenu(ObjectManager $manager)
	{
		$menuTax = new MenuTaxonomy();
		$menuTax->setName('Menu Admin');
		$menuTax->setAlias('menu-admin');
		$menuTax->setIsMenuAdmin(1);

		$manager->persist($menuTax);
		$manager->flush();

		$lang_default = $manager->getRepository('CMSContentBundle:CMLanguage')->findBy(array('default_lan'=>'1'));
        $language = current($language);

		$menuRoot = new Menu();
		$menuRoot->setTitle('Root Admin');
		$menuRoot->setSlug('root-admin');
		$menuRoot->setPublished(1);
		$menuRoot->setCategory(null);
		$menuRoot->setContent(null);
		$menuRoot->setParent(null);
		$menuRoot->setRoot(null);
		$menuRoot->setIdMenuTaxonomy($menuTax->getId());
		$menuRoot->setDefaultPage(false);
		$menuRoot->setLanguage($lang_default);
		$menuRoot->setIntern(true);
		$menuRoot->setNameRoute('#');
		$menuRoot->setIsRoot(true);
		$menuRoot->setDisplayIcon(false);
		$menuRoot->setDisplayName(false);

		$menuTax->addMenu($menuRoot);

		$manager->persist($menuRoot);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Dashboard');
		$menu->setSlug('dashboard');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/dashboard');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_settings');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Contenus');
		$menu->setSlug('contenus');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/contents/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_note');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Articles');
		$menuChild->setSlug('articles');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/contents/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Catégories');
		$menuChild->setSlug('categories');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/categories/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();


		$menuChild = new Menu();
		$menuChild->setTitle('Types de contenu');
		$menuChild->setSlug('types-de-contenu');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/contenttypes/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Champs');
		$menuChild->setSlug('champs');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/fields/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Metas');
		$menuChild->setSlug('metas');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/metas/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Menus');
		$menu->setSlug('menus');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/menus/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_data');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Apparence');
		$menu->setSlug('apparence');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/blocs/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_camera');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Blocs');
		$menuChild->setSlug('blocs');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/blocs/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Médias');
		$menuChild->setSlug('medias');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/media');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Paramètres');
		$menu->setSlug('parametres');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/languages/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_params');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menuChild = new Menu();
		$menuChild->setTitle('Langues');
		$menuChild->setSlug('langues');
		$menuChild->setPublished(1);
		$menuChild->setCategory(null);
		$menuChild->setContent(null);
		$menuChild->setParent($menu);
		$menuChild->setRoot($menu->getId());
		$menuChild->setIdMenuTaxonomy($menuTax->getId());
		$menuChild->setDefaultPage(false);
		$menuChild->setLanguage($lang_default);
		$menuChild->setIntern(true);
		$menuChild->setNameRoute('/admin/languages/list');
		$menuChild->setIsRoot(false);
		$menuChild->setClassIcon('');
		$menuChild->setDisplayIcon(false);
		$menuChild->setDisplayName(true);

		$menuTax->addMenu($menuChild);

		$manager->persist($menuChild);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Utilisateurs');
		$menu->setSlug('utilisateurs');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/users/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_user');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		$menu = new Menu();
		$menu->setTitle('Contacts');
		$menu->setSlug('contacts');
		$menu->setPublished(1);
		$menu->setCategory(null);
		$menu->setContent(null);
		$menu->setParent($menuRoot);
		$menu->setRoot($menuRoot->getId());
		$menu->setIdMenuTaxonomy($menuTax->getId());
		$menu->setDefaultPage(false);
		$menu->setLanguage($lang_default);
		$menu->setIntern(true);
		$menu->setNameRoute('/admin/contacts/list');
		$menu->setIsRoot(false);
		$menu->setClassIcon('li_stack');
		$menu->setDisplayIcon(true);
		$menu->setDisplayName(true);

		$menuTax->addMenu($menu);

		$manager->persist($menu);
		$manager->flush();

		return $menuTax;
	}

	
}