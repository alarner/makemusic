<?php
class Omniture {
    private $pathToOmnitureJsp = '/jsp/js/omniture/omniture.js.jsp?';
    private $pantherUrl        = 'http://media.timeoutnewyork.com';
    private $pageName;
    private $server;
    private $channel;
    private $hierarchy1;
    private $contentTitle;
    private $contentId;
    private $contentType;
    private $siteSection;
    private $subSection1;
    private $subSection2;
    private $subSection3;
    private $subSection4;

    public function __construct($vars) {
        $this->pageName     = $vars['pageName'];
        $this->server       = $vars['server'];
        $this->channel      = $vars['channel'];
        $this->hierarchy1   = $vars['hierarchy1'];
        $this->contentTitle = $vars['contentTitle'];
        $this->contentId    = $vars['contentId'];
        $this->contentType  = $vars['contentType'];
        $this->siteSection  = $vars['siteSection'];
        $this->subSection1  = $vars['subSection1'];
        $this->subSection2  = $vars['subSection2'];
        $this->subSection3  = $vars['subSection3'];
        $this->subSection4  = $vars['subSection4'];
    }

    function getOutput() {
        $omnitureString = $this->pathToOmnitureJsp
            . 'pn='            . $this->pageName
            . '&server='       . $this->server
            . '&channel='      . $this->channel
            . '&hierarchy1='   . $this->hierarchy1
            . '&contentTitle=' . $this->contentTitle
            . '&contentId='    . $this->contentId
            . '&contentType='  . $this->contentType
            . '&siteSection='  . $this->siteSection
            . '&subSection1='  . $this->subSection1
            . '&subSection2='  . $this->subSection2
            . '&subSection3='  . $this->subSection3
            . '&subSection4='  . $this->subSection4;

        ob_start();
        define('OMNITURE_INCLUDE', $this->pantherUrl . $omnitureString);
        include(OMNITURE_INCLUDE);
        $omnitureData = ob_get_contents();
        ob_end_clean();

        return $omnitureData;
    }
}
