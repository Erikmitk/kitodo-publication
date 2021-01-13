<?php
namespace EWW\Dpf\Plugins\Coins;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Plugin 'DPF: Coins' for the 'dlf / dpf' extension.
 *
 * @author    Erik Sommer <erik.sommer@slub-dresden.de>
 * @package    TYPO3
 * @subpackage    tx_dpf
 * @access    public
 */
class Coins extends \Kitodo\Dlf\Common\AbstractPlugin
{
    public $scriptRelPath = 'Classes/Plugins/Coins.php';

    /**
     * The main method of the PlugIn
     *
     * @access    public
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The PlugIn configuration
     *
     * @return    string        The content that is displayed on the website
     */
    public function main($content, $conf)
    {

       $this->init($conf);

       // get the tx_dpf.settings too
       // Flexform wins over TS
       $dpfTSconfig = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_dpf.'];
       if (is_array($dpfTSconfig['settings.'])) {
           \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($dpfTSconfig['settings.'], $this->conf, true, false);
           $this->conf = $dpfTSconfig['settings.'];
       }

       // Load current document.
       $this->loadDocument();
       if ($this->doc === null || empty($this->conf['fileGrpDownload'])) {
           // Quit without doing anything if required variables are not set.
           return $content;
       }

       $metadata = array();
       $metadata = $this->doc->getTitleData($this->conf['pages']);

       $metadata['_id'] = $this->doc->toplevelId;
       if (empty($metadata)) {
           if (TYPO3_DLOG) {
               GeneralUtility::devLog('[tx_dpf_metatags->main(' . $content . ', [data])] No metadata found for document with UID "' . $this->doc->uid . '"', 'tx_dpf', SYSLOG_SEVERITY_WARNING, $conf);
           }
           return;
       }

       ksort($metadata);

       $coins = $this->generateCoins($metadata);
        // Load template file.
         $this->template = $this->templateService->getSubpart($this->cObj->fileResource('EXT:dpf/Classes/Plugins/Coins/template.tmpl'), '###TEMPLATE###');

        // Generate Coins String
        $coins = "lol";


        return $this->cObj->substituteSubpart($this->template, '###COINS###', $coins, true);
    }

    /**
     * Prepares the metadata array for output
     *
     * @access    protected
     *
     * @param    array        $metadata: The metadata array
     *
     * @return    string        The metadata array ready for output
     */
    protected function generateCoins(array $metadata)
    {

        $output = '';
        // Load all the metadata values into the content object's data array.
        foreach ($metadata as $index_name => $values) {
            if (preg_match("/^author[[:digit:]]+/", $index_name)) {
                if (is_array($values)) {
                    foreach ($values as $id => $value) {
                        if ($value) {
                            $outArray['citation_author'][] = $value;
                        }
                    }
                }
            }
            switch ($index_name) {
                case 'title':
                    if (is_array($values)) {
                        $outArray['citation_title'][] = $values[0];
                        $GLOBALS['TSFE']->page['title'] =  $values[0];
                    }
                    break;

                case 'dateissued':
                    if (is_array($values)) {
                        if ($values[0]) {
                            // Provide full dates in the "2010/5/12" format if available; or a year alone otherwise.
                            $outArray['citation_online_date'][] = $this->safelyFormatDate("Y/m/d", $values[0]);
                        }
                    }
                    break;

                case 'publication_date':
                    if (is_array($values)) {
                        if ($values[0]) {
                            $outArray['citation_publication_date'][] = $this->safelyFormatDate("Y", $values[0]);
                        }
                    }
                    break;

                case 'attachments':
                    foreach($values as $attachment) {
                        $conf = array(
                            'useCacheHash'     => 0,
                            'parameter'        => $this->conf['apiPid'],
                            'additionalParams' => '&tx_dpf[qid]=' . $this->doc->recordId . '&tx_dpf[action]=attachment&tx_dpf[attachment]=' . $attachment['ID'],
                            'forceAbsoluteUrl' => true,
                        );
                        $outArray['citation_pdf_url'][] = $this->cObj->typoLink_URL($conf);
                    }
                    break;

                case 'abstract_ger':
                    $lang = $GLOBALS['TSFE']->lang;
                    if (is_array($values) && $GLOBALS['TSFE']->lang == 'de') {
                        $outArray['description'][] = $values[0];
                    }
                    break;

                case 'abstract_eng':
                    if (is_array($values) && $GLOBALS['TSFE']->lang == 'en') {
                        $outArray['description'][] = $values[0];
                    }
                    break;

                default:
                    break;
            }
        }

        foreach ($outArray as $tagName => $values) {
            foreach ($values as $value) {
                $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
                $pageRenderer->addMetaTag('<meta name="' . $tagName . '" content="' . htmlspecialchars($value) . '">');
            }
        }
        return $output;
    }

    /**
     * Format given date with given format, assuming the input date format is
     * parseable by strtotime(). If the input string has a length of 4 (like
     * "1989") the string is returned as is, without formatting.
     *
     * @param String $format Target string format
     * @param String $date   Date string to format
     *
     * @return Formatted date
     */
    protected function safelyFormatDate($format, $date)
    {
        return (strlen($date) == 4) ? $date : date($format, strtotime($date));
    }

    protected function dump($variable, $title = "SLUB Account Variable Dump" ) {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($variable,  $title);
    }
}
