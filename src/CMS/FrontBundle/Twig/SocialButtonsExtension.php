<?php
namespace CMS\FrontBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;

class SocialButtonsExtension extends Twig_Extension
{

    public function getFilters()
    {
        return array('socialButtons' => new Twig_Filter_Method($this, 'socialButtonsFilter'));
    }

    public function socialButtonsFilter($value)
    {

        preg_match_all('/\[%%id:(.*), url:(.*), services:(.*)[^%%]%%\]/', $value, $values);
        $id = $values[1][0];
        $url = urlencode($values[2][0]);
        $services = explode(',',$values[3][0]);
        $str = '';
        $mail = '<a href="#modalMail-'.$id.'" data-toggle="modal"><i class="social-icon-mail"></i></a>';
        $mail .= '<div class="modal hide fade" id="modalMail-'.$id.'">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Email</h3>
          </div>
          <div class="modal-body-share">
          <ul class="unstyled">
            <li class="li-modal"><input type="email" class="input-modal" name="shareMail[to]" placeholder="À : " /></li>
            <li class="li-modal"><input type="email" class="input-modal" name="shareMail[From]" placeholder="De : " /></li>
            <li class="li-modal"><input type="text" class="input-modal" name="shareMail[subject]" placeholder="Sujet : " /></li>
            <li class="li-text-modal"><textarea class="textarea-modal" placeholder="Message"></textarea></li>
            <li>Url rajoutée à votre message : '.urldecode($url).'</li>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Annuler</a>
            <a href="#" class="btn btn-danger">Envoyer</a>
          </div>
        </div>';

        $twitter  = '<a href="http://www.twitter.com/share?url='.$url.'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Twitter"><i class="social-icon-twitter"></i></a>';
        $facebook = '<a href="http://www.facebook.com/sharer.php?u='.$url.'" target="_blank"  data-toggle="tooltip" data-placement="top" data-original-title="Facebook"><i class="social-icon-facebook"></i></a>';
        $linkedin = '<a href="http://www.linkedin.com/shareArticle?mini=true&url='.$url.'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Linkedin"><i class="social-icon-linkedin"></i></a>';
        $gplus    = '<a href="https://plus.google.com/share?url='.$url.'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Google Plus"><i class="social-icon-gplus"></i></a>';
        $viadeo	  = '<a href="http://www.viadeo.com/shareit/share/?url='.$url.'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Viadeo"><i class="social-icon-viadeo"></i></a>';

        foreach ($services as $service) {
            $str .= ${$service};
        }

        return $str;
    }

    public function getName()
    {
        return 'socialButtons_extension';
    }

}
