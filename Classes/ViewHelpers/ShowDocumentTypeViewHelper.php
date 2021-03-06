<?php
namespace EWW\Dpf\ViewHelpers;

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

class ShowDocumentTypeViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * documentTypeRepository
     *
     * @var \EWW\Dpf\Domain\Repository\DocumentTypeRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $documentTypeRepository = null;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('docType', 'string', 'The document type name', true);
    }

    /**
     * Gets the localized display name of the given document type.
     * @return string
     */
    public function render()
    {
        $docType = $this->arguments['docType'];

        /** @var \EWW\Dpf\Domain\Model\DocumentType $documentType */
        $documentType = $this->documentTypeRepository->findOneByName($docType);

        if ($documentType) {
            return $documentType->getDisplayName();
        }

        return "-";
    }
}
