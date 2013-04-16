<?php

namespace CMS\AdminBundle\Classes;

class BlocDisplay
{

	private $bloc;
	private $session;
	private $entries;

	private $repositoryMenu;
	private $url_intern;

	private $request;

	public function __construct()
	{
		
	}

	public function setBloc($bloc)
	{
		$this->bloc = $bloc;
	}

	public function getBloc()
	{
		return $this->bloc;
	}

	public function setSession($session) 
	{
		$this->session = $session;
	}

	public function getSession()
	{
		return $this->session;
	}

	public function displayBloc() 
	{
		return '';
	}

	public function getEntries() {
		return $this->entries;
	}

	public function setRepositoryMenu($repo)
	{
		$this->repositoryMenu = $repo;
	}

	public function setUrlIntern($url_intern)
	{
		$this->url_intern = $url_intern;
	}

	public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
	{
		$this->request = $request;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getOptionsBreadcrumb() {
		$entry = $this->repositoryMenu->getEntryMenuByUrlIntern($this->url_intern);
		$parent = null;
		if($entry != null && !$entry->getParent()->getIsRoot())
			$parent = $entry->getParent();
		$path = array();

        while ($parent != null && $parent->getLevel() > 2 && !$parent->getIsRoot()) {
            $parent = $parent->getParent();

        }


        if($parent != null) {
	        $path  = $this->repositoryMenu->getChildren($parent, true, null, 'desc');
	        $path_real = array();
	        foreach ($path as $leaf) {
	            if($leaf->getId() == $entry->getId() && !$parent->getIsRoot())
	                $path_real[] = $leaf;
	        }
	        $path_real[] = $parent;
	        $path = array_reverse($path_real);
        } else if($entry != null) {
        	$path[] = $entry;
        }
        $options['entries'] = $path;
        $options['default_url'] = $this->repositoryMenu->getDefaultUrl();
        if($entry != null)
        	$options['url'] = $entry->getUrl();
        else
        	$options['url'] = '';
        return $options;
	}


}	



