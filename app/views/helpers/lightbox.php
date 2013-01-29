<?php  
class LightboxHelper extends Helper {

    var $helpers = array('Html');

    function img($thumb, $full, $imgAttributes = array(), $linkAttributes = array()) {
        $defaultLinkAttributes = array('rel'=>'lightbox');
        $linkAttributes = array_merge($defaultLinkAttributes, $linkAttributes);

        $thumb = $this->Html->image($thumb, $imgAttributes);
        if (strpos($full, '://') === false) {
            $full = $this->Html->webroot(IMAGES_URL . $full);
        }
        return $this->Html->output(sprintf($this->Html->tags['link'], $full, $this->Html->_parseAttributes($linkAttributes), $thumb));
    }
}
?>