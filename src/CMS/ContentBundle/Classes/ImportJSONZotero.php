<?php
namespace CMS\ContentBundle\Classes;


use CMS\ContentBundle\Entity\CMContent;
use CMS\ContentBundle\Entity\CMFieldValue;
use CMS\ContentBundle\Entity\CMContentTaxonomy;

class ImportJSONZotero 
{

	private $controller;
	private $content_file;
	private $json_parsed;
	private $category;
	private $contentType;
	private $lang;

	/**
	 * Constructeur de la classe, il prend un fichier json en paramètre
	 * @param String              $fichier 	   chemin vers le fichier
	 * @param CMContentController $controller  Contenttroller to insert the articles
	 * @param CMCategory		  $category    Category of the articles to insert
	 * @param CMContentType   	  $contentType Content Type of the article 
	 */
	public function __construct($fichier, $controller, $category, $contentType, $lang)
	{
		$this->content_file = file_get_contents($fichier);
		$this->json_parsed = json_decode($this->content_file);
		$this->controller = $controller;
		$this->category = $category;
		$this->contentType = $contentType;
		$this->lang = $lang;
	}

	public function insertAll() {
		$title = '';
		$containerTitle = '';
		$page = '';
		$url = '';
		$doi = '';
		$shortTitle = '';
		$author = '';
		$year = '';
		//var_dump($this->json_parsed[0]); die;
		$j=0;
		foreach ($this->json_parsed as $obj) {
			$title = $obj->title;
			$containerTitle = isset($obj->{'container-title'}) ? $obj->{'container-title'} : '';
			$page = isset($obj->page) ? $obj->page : '';
			$url = isset($obj->URL) ? $obj->URL : '';
			$doi = isset($obj->DOI) ? $obj->DOI : '';
			$shortTitle = isset($obj->shortTitle) ? $obj->shortTitle : '';
			$year = isset($obj->issued->{'date-parts'}[0]) ? $obj->issued->{'date-parts'}[0][0] : '';
			$i = 0;
			$author_str = array();
			$author = array();
			if (isset($obj->author)) {
				$author_str = array();
				foreach ($obj->author as $auteur) {
					if(isset($auteur->family))
						$author_str[$i] = $auteur->family;
					if(isset($auteur->given))
						$author_str[$i] = $author_str[$i].' '.$auteur->given;


					if(isset($auteur->literal))
						$author_str[$i] = $auteur->literal;
					
					$i++;
				}
				
			}

			if (is_array($author_str))
				$author = implode(', ', $author_str);
			
			$publi = $this->controller->getDoctrine()->getRepository('CMSContentBundle:CMContent')->getContentByFieldValue('doi', $doi);
			if($publi == null)
				$this->insertPublication($title, $containerTitle, $page, $url, $doi, $shortTitle, $author, $year);
			else
				$this->updatePublication($publi, $title, $containerTitle, $page, $url, $doi, $shortTitle, $author, $year);
			$j++;
		}
		

	}

	/**
	 * Insert a publication in the contents of the CMS
	 * @param  String   $title          Title of the article
	 * @param  String   $containerTitle Container title of the article
	 * @param  String   $page           Page in the revue
	 * @param  String   $url            Url to the article
	 * @param  String   $doi            DOI Number of the article
	 * @param  String   $shortTitle     Short title of the article
	 * @param  String   $authors        Authors of the articles
	 * @param  int 	    $year           Issued year if the article
	 * @return boolean                  True if the article is inserted, else false
	 */
	private function insertPublication($title, $containerTitle, $pages, $url, $doi, $shortTitle, $authors, $year)
	{
		$publi = new CMContent();
		$publi->setTitle($title);
		$publi->setUrl($this->category->getUrl().'/'.$title);
		$publi->addCategorie($this->category);
		$publi->setContenttype($this->contentType);
		$publi->setPublished(1);
		$publi->setLanguage($this->lang);
		
		$em = $this->controller->getDoctrine()->getEntityManager();


		$contentTaxonomy = new CMContentTaxonomy;
        $contentTaxonomy->addContent($publi);
        $em->persist($contentTaxonomy);
        $em->flush();

        $publi->setTaxonomy($contentTaxonomy);
		
		$em->persist($publi);
		$em->flush();

		$fields = $this->contentType->getFields();

		

		foreach ($fields as $key => $field) {
			$fieldName = $field->getName();
			$value = '';
			switch($fieldName) {
				case 'container_title':
					$value = $containerTitle;
					break;
				case 'pages':
					$value = $pages;
					break;
				case 'resume_link':
					$value = $url;
					break;
				case 'doi':
					$value = $doi;
					break;
				case 'short_title':
					$value = $shortTitle;
					break;
				case 'date_publi':
					$value = $year;
					break;
				case 'auteurs':
					$value = $author;
					break;	

			}
			if($value != '') {
				$fieldValue = new CMFieldValue();
				$fieldValue->setValue($value);
				$fieldValue->setContent($publi);
				$fieldValue->setField($field);
				$publi->addFieldValue($fieldValue);
				$field->addFieldValue($fieldValue);
				$em->persist($fieldValue);
				$em->persist($field);
			}
		}

		$em->persist($publi);
		$em->flush();
		return true;
	}

	/**
	 * Update an article
	 * @param  String   $title          Title of the article
	 * @param  String   $containerTitle Container title of the article
	 * @param  String   $page           Page in the revue
	 * @param  String   $url            Url to the article
	 * @param  String   $doi            DOI Number of the article
	 * @param  String   $shortTitle     Short title of the article
	 * @param  String   $authors        Authors of the articles
	 * @param  int 	    $year           Issued year if the article
	 * @return boolean                  True if the article is updated
	 */
	private function updatePublication($publi, $title, $containerTitle, $pages, $url, $doi, $shortTitle, $authors, $year)
	{
		//$publi = $controller->getDoctrine()->getRepository('CMSContentBundle:CMContent')->getContentByFieldValue('doi', $doi);
		$publi->setTitle($title);

		$url_publi = str_replace(' ,\'\\', '-', $title);
        $url_publi = strtolower($url_publi);
        $url_publi = \Gedmo\Sluggable\Util\Urlizer::urlize($url_publi);

		$publi->setUrl($this->category->getUrl().'/'.$url_publi);
		$publi->setContenttype($this->contentType);
		
		$em = $this->controller->getDoctrine()->getEntityManager();
		
		$em->persist($publi);
		$em->flush();

		$fields = $this->contentType->getFields();

		$value = '';
		foreach ($this->contentType->getFields() as $key => $field) {
			$value_exist = false;
			foreach ($publi->getFieldValues() as $key => $fieldValue) {
	            if ($fieldValue->getField()->getId() == $field->getId()) {
					$fieldName = $field->getName();

					switch($fieldName) {
						case 'container_title':
							$value = $containerTitle;
							break;
						case 'pages':
							$value = $pages;
							break;
						case 'resume_link':
							$value = $url;
							break;
						case 'doi':
							$value = $doi;
							break;
						case 'short_title':
							$value = $shortTitle;
							break;
						case 'date_publi':
							$value = $year;
							break;
						case 'auteurs':
							$value = $authors;
							break;			

					}
					$fieldValue->setValue($value);
					$em->persist($fieldValue);
					$value_exist = true;
					
				}
			}
			if (!$value_exist) {
				$fieldName = $field->getName();
				$value = '';
				switch($fieldName) {
					case 'container_title':
						$value = $containerTitle;
						break;
					case 'pages':
						$value = $pages;
						break;
					case 'resume_link':
						$value = $url;
						break;
					case 'doi':
						$value = $doi;
						break;
					case 'short_title':
						$value = $shortTitle;
						break;
					case 'date_publi':
						$value = $year;
						break;
					case 'auteurs':
						$value = $authors;
						break;			

				}
				if($value != '') {
					$fieldvalue = new CMFieldValue;
	                $fieldvalue->setValue($value);
	                $fieldvalue->setContent($publi);
	                $fieldvalue->setField($field);
	                $publi->addFieldValue($fieldvalue);
	                $field->addFieldValue($fieldvalue);
	                $em->persist($fieldvalue);
                }
			}
		}

		$em->persist($publi);
		$em->flush();
		return true;
	}

}