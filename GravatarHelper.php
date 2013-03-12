<?php
/**
 * Gravatar Helper
 *
 * Extends the HTML Helper to allow us to make global changes without
 * affecting the cakephp core.
 *
 * @author	Author: Chuck Burgess <cdburgess@gmail.com>
 * @license	License: http://creativecommons.org/licenses/by-sa/3.0/
 * @copyright	Copyright: (c)2013 Chuck Burgess. All Rights Reserved.
 **/

/**
 * Define DocBlock
 **/
App::uses('HtmlHelper', 'View/Helper');

class GravatarHelper extends HtmlHelper {

/**
 * image class variable
 *
 * Change this to a devault image you have on your system in case Gravatar is not
 * available to return an image. This will prevent broken images from appearing.
 * @var string
 **/
public $icon = 'gravatar.jpg';

/**
 * icon
 *
 * Build the gravitar image icon. If it cannot be constructed to do a missing connection,
 * default to a local image instead.
 *
 * options:
 * - (all html helper options options)
 * <img src="http://www.gravatar.com/avatar/<?php echo md5( strtolower( trim($this->Session->read('Auth.User.email')))); ??s=64">
 *
 * USAGE:
 * <?php echo $this->Gravatar->icon($email, $size, $htmlOptions); ?>
 *
 * @param string $email The email address for the user to get the gravatar for
 * @param int $size The size in pixels of the gravatar icon image to display
 * @param array $options Any of the HtmlHelper->image options to apply
 * @return void
 * @author Chuck Burgess
 **/
	public function icon($email = null, $size = 16, $options = array()) {
		$md5 = null;
		if (isset($email)) {
			$md5 = md5(strtolower(trim($email)));
		}
		$gravatarUrl = 'http://www.gravatar.com/avatar/' . $md5 . '?s=' . $size;
		// we don't want any error to show, just we will supress it
		$size = @getimagesize($gravatarUrl);
		if ($size) {
			return parent::image($gravatarUrl, $options);
		} else {
			$options['width'] = $size;
			$options['height'] = $size;
			return parent::image($this->icon, $options);
		}
	}

}
