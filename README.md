# PDF Thumbnail Generator for Joomla! EDocman
This Joomla plugin automatically generates missing images for PDF documents in EDocman.

## Setup

* Install the imagick extension for your PHP version (e.g. `sudo apt-get install php-imagick -y`)
* Download and install this plugin in Joomla!

## Plugin settings (parameters)
Go to the [settings page](https://docs.joomla.org/Administration_of_a_Plugin_in_Joomla) of the newly installed plugin in Joomla.  
You can search for "EDocman Thumbnail".

You can change the page that is used for generating the thumbnail (starting at 1 for page 1 - not 0).

## Logs
You can find the logs at `/logs/edocman_thumbnail_generator.log.php`. Access to this file from a browser is disallowed.
