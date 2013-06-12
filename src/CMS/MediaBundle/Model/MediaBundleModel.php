<?php
namespace CMS\MediaBundle\Model;

class MediaBundleModel {
	
	public function display($media, $cacheManager) {

		// Get Extension from a file
		$ext = explode('.', $media);
		$ext = array_pop($ext);
		switch ($ext) {
			case 'png':
			case 'jpg':
			case 'jpeg':
			case 'bmp':	
        		$srcPath = $cacheManager->getBrowserPath($media, 'thumb_media_pin');
				return '<img src="'.$srcPath.'" />';
				break;
			default:
				return '<img src="/bundles/cmsmedia/images/'.$ext.'.png" width="128px" />';
				break;
		}

	}
}