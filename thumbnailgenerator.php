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
     * Called when a new user is created or updated.
     */
    function onDocumentAfterSave($context, $row, $isNew)
    {
        define('ALLOWED_EXTENSION', 'pdf');
        define('USED_PAGE', intval($this->params->get('thumbgen_page', 1)) - 1);
        define('ENABLE_WATERMARK', boolval($this->params->get('thumbgen_enable_watermark', false)));
        define('WATERMARK_TEXT', $this->params->get('thumbgen_watermark_text', 'Preview'));
        define('WATERMARK_SIZE', intval($this->params->get('thumbgen_watermark_size', 20)));

        JLog::addLogger(['text_file' => 'edocman_thumbnail_generator.log.php', 'text_file_no_php' => false], JLog::ALL, ['edocman_thumbnail_generator']);

        $config = EDocmanHelper::getConfig();
        JLog::add('Got EDocman config.', JLog::DEBUG, 'edocman_thumbnail_generator');
        // Get path to document
        $filePath = $config->documents_path . '/' . $row->filename;
        
        // Only generate for PDF documents
        if(empty($row->image) && pathinfo($filePath)["extension"] === ALLOWED_EXTENSION) {
            // Save unscaled image, substract 1 because we start at 0
            $im = new imagick($filePath . '[' . USED_PAGE . ']');

            // Source for watermarking code: https://www.sitepoint.com/adding-text-watermarks-with-imagick/
            if(ENABLE_WATERMARK) {
                // Create a new drawing palette
                $draw = new ImagickDraw();
                $watermark = new Imagick();
                $watermark->newImage(140, 80, new ImagickPixel('none'));

                // Set font properties
                $draw->setFont('/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf');
                $draw->setFillColor('grey');
                $draw->setFillOpacity(.5);
                $draw->setFontSize(WATERMARK_SIZE);

                // Position text at the top left of the watermark
                $draw->setGravity(Imagick::GRAVITY_NORTHWEST);

                // Draw text on the watermark
                $watermark->annotateImage($draw, 10, 10, 0, WATERMARK_TEXT);

                // Position text at the bottom right of the watermark
                $draw->setGravity(Imagick::GRAVITY_SOUTHEAST);

                // Draw text on the watermark
                $watermark->annotateImage($draw, 5, 15, 0, WATERMARK_TEXT);
            }

            // Save image
            $im->setImageFormat('jpg');
            $imgName = 'gen-pdf-' . $row->id . '-' . uniqid() . '.jpg';
            $imgPath = JPATH_ROOT . '/media/com_edocman/document/' . $imgName;
            $thumbPath = JPATH_ROOT . '/media/com_edocman/document/thumbs/' . $imgName;
            $im->writeImage($imgPath);

            // Overlay the watermark
            if(ENABLE_WATERMARK) {
                $im = new Imagick($imgPath);

                // Repeatedly overlay watermark on image
                for ($w = 0; $w < $im->getImageWidth(); $w += 140) {
                    for ($h = 0; $h < $im->getImageHeight(); $h += 80) {
                        $im->compositeImage($watermark, Imagick::COMPOSITE_OVER, $w, $h);
                    }
                }

                // Replace image with watermarked image
                $im->writeImage($imgPath);
                JLog::add('Saved watermarked image file to ' . $imgPath, JLog::DEBUG, 'edocman_thumbnail_generator');
            }
            else
            {
                JLog::add('Saved image file to ' . $imgPath, JLog::DEBUG, 'edocman_thumbnail_generator');
            }

            // Scale and save image = thumbnail
            $width  = $config->document_thumb_width;
            $height = $config->document_thumb_height;
            EDocmanHelper::resizeImage($imgPath, $thumbPath, $width, $height);
            JLog::add('Resized image in ' . $thumbPath, JLog::DEBUG, 'edocman_thumbnail_generator');

            $row->image = $imgName;
        }
    }
}
