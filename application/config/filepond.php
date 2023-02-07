<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// where to get files from
$config['ENTRY_FIELD'] = array('attach');
// where to write files to
$config['TRANSFER_DIR'] = FCPATH.'upload/tmp';
$config['UPLOAD_DIR'] = FCPATH.'upload/final';
$config['VARIANTS_DIR'] = FCPATH.'upload/variants';
$config['METADATA_FILENAME'] = '.metadata';
//$config['TRANSFER_PROCESSOR'] = 'filepond_transfer_processor';
//$config['TRANSFER_PROCESSOR_FILE_PREFIX'] = 'processed_';
// this automatically creates the upload and transfer directories, if they're not there already
if (!is_dir($config['UPLOAD_DIR'])) mkdir($config['UPLOAD_DIR'], 0755, TRUE);
if (!is_dir($config['TRANSFER_DIR'])) mkdir($config['TRANSFER_DIR'], 0755, TRUE);
if (!is_dir($config['VARIANTS_DIR'])) mkdir($config['VARIANTS_DIR'], 0755, TRUE);
// this is optional and only needed if you're doing server side image transforms, if images are transformed on the clients, this can stay commented
// allowed file formats, if empty all files allowed
$config['ALLOWED_FILE_FORMATS'] = array(
    // images
    'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/webp',

    // video
    'video/mpeg', 'video/mp4', 'video/x-msvideo', 'video/webm', 'video/ogg',

    // audio
    'audio/mpeg', 'audio/ogg', 'audio/mpeg', 'audio/webm',

    // docs
    'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.oasis.opendocument.spreadsheet','application/vnd.oasis.opendocument.text',
    'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'text/plain', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);


