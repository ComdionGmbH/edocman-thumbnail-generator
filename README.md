# PDF Thumbnail Generator for Joomla! EDocman
This Joomla plugin automatically generates missing images for PDF documents in EDocman.

## Setup

* Install the imagick extension for your PHP version (e.g. `sudo apt-get install php-imagick -y`)
* Download [edocman-thumbnail-generator.zip](https://github.com/ComdionGmbH/edocman-thumbnail-generator/releases/download/1.1.0/edocman-thumbnail-generator.zip) from the Releases and install the plugin in Joomla!
* Enable the plugin

If a document without an image (or the thumbnail just got deleted) is saved, an image will be generated.

## Plugin settings (parameters)
Go to the [settings page](https://docs.joomla.org/Administration_of_a_Plugin_in_Joomla) of the newly installed plugin in Joomla.  
You can search for "EDocman Thumbnail".

You can change the page that is used for generating the thumbnail (starting at 1 for page 1 - not 0).

### Watermark
The PDF Thumbnail Generator includes a watermarking function (not enabled by default).  
Options to enable it and changing the text/font size are in the plugin settings.

## Logs
You can find the logs at `/logs/edocman_thumbnail_generator.log.php`. Access to this file from a browser is disallowed.
