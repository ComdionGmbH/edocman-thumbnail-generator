<?php
/**
 * Comdion GmbH
 *
 * Edocman Thumbnail Generator:
 * Main class file
 */

// Disallow direct access to this file
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';

class plgEdocmanThumbnailgenerator extends JPlugin
{
    /**
     * Called when a new document is saved.
     */
    function onDocumentAfterSave($context, $row, $isNew)
    {
        JLog::addLogger(['text_file' => 'edocman_thumbnail_generator.log.php', 'text_file_no_php' => false], JLog::ALL, ['edocman_thumbnail_generator']);

        $config = EDocmanHelper::getConfig();
        JLog::add('Got EDocman config.', JLog::DEBUG, 'edocman_thumbnail_generator');
        // Get path to document
        $filePath = $config->documents_path . '/' . $row->filename;

        // Only generate for PDF documents
        if(empty($row->image) && pathinfo($filePath)["extension"] === "pdf") {
            // Save unscaled image, substract 1 because we start at 0
            $im = new imagick($filePath . '[' . (intval($this->params->get('thumbgen_page', '1')) - 1) . ']');
            $im->setImageFormat('jpg');
            $imgName = 'gen-pdf-' . $row->id . '-' . uniqid() . '.jpg';
            $imgPath = JPATH_ROOT . '/media/com_edocman/document/' . $imgName;
            $thumbPath = JPATH_ROOT . '/media/com_edocman/document/thumbs/' . $imgName;
            $im->writeImage($imgPath);
            JLog::add('Saved image file to ' . $imgPath, JLog::DEBUG, 'edocman_thumbnail_generator');

            // Scale and save image = thumbnail
            $width  = $config->document_thumb_width;
            $height = $config->document_thumb_height;
            EDocmanHelper::resizeImage($imgPath, $thumbPath, $width, $height);
            JLog::add('Resized image in ' . $thumbPath, JLog::DEBUG, 'edocman_thumbnail_generator');

            $row->image = $imgName;
        }
    }
}
