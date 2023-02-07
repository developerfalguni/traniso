<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Comment if you don't want to allow posts from other domains
header('Access-Control-Allow-Origin: *');
// Allow the following methods to access this file
header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');
// Allow the following headers in preflight
header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');
// Allow the following headers in response
header('Access-Control-Expose-Headers: upload-offset');

class Upload extends MY_Controller {
	function __construct() {
		parent::__construct();

		
	}

	public function index(){
		
		//FilePond\catch_server_exceptions();
		// Route request to handler method
		$entries = $this->_entries;

		// if a singly field entry is supplied, turn it into an array
	    if (is_string($entries)) $entries = array($entries);
	    // get the request method so we don't have to use $_SERVER each time
	    $request_method = $_SERVER['REQUEST_METHOD'];

	    // loop over all set entry fields to find posted values
	    foreach ($entries as $entry) {
	    	// post new files
	        if ($request_method === 'POST') {
	            $post = Filepond\get_post($entry);
	            if (!$post) continue;
	            $transfer = new Filepond\Transfer();

	            $transfer->populate($entry);
	            $this->handle_file_transfer($transfer);

	        }
	        // revert existing transfer
	        if ($request_method === 'DELETE') {
	        	$deletedir = preg_replace('/\s+/', '', file_get_contents('php://input'));
	        	$this->handle_revert_file_transfer($deletedir);
	        }
	        // fetch, load, restore
	        if ($request_method === 'GET' || $request_method === 'HEAD' || $request_method === 'PATCH') {
	            $handlers = array(
	                'fetch' => 'FETCH_REMOTE_FILE',
	                'restore' => 'RESTORE_FILE_TRANSFER',
	                'load' => 'LOAD_LOCAL_FILE',
	                'patch' => 'PATCH_FILE_TRANSFER'
	            );
	            foreach ($handlers as $param => $handler) {
	                if (isset($_GET[$param])) {
	                    return call_user_func(array('home', $routes[$handler]), $_GET[$param], $entry);
	                }
	            }
	        }

	    }

	    //$this->handle_file_post($entries);
	    /*$this->handle_base64_encoded_file_post($chetan);
	    $this->handle_base64_encoded_file_post($chetan);*/
	    	
	    

	    /*FilePond\route_form_post(ENTRY_FIELD, [
		    'FILE_OBJECTS' => 'handle_file_post',
		    'BASE64_ENCODED_FILE_OBJECTS' => 'handle_base64_encoded_file_post',
		    'TRANSFER_IDS' => 'handle_transfer_ids_post'
		]);*/


	}

	public static function handle_file_transfer($transfer) {
		$CI =& get_instance();
		$metadata = $transfer->getMetadata();
	    $files = $transfer->getFiles();
	    // something went wrong, most likely a field name mismatch
	    if ($files !== null && count($files) === 0) return http_response_code(400);
	    // store data
	    FilePond\store_transfer($CI->_transfer_dir, $transfer);
	    // created the temp entry
	    http_response_code(201);
	    // returns plain text content
	    header('Content-Type: text/plain');
	    // remove item from array Response contains uploaded file server id
	    echo $transfer->getId();
	    //return $transfer->getId();
	}

	public static function handle_patch_file_transfer($id) {
		$CI =& get_instance();
	    // location of patch files
	    $dir = $CI->_transfer_dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
	    
	    // exit if is get
	    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
	        $patch = glob($dir . '.patch.*');
	        $offsets = array();
	        $size = '';
	        $last_offset = 0;
	        foreach ($patch as $filename) {
	        	// get size of chunk
	            $size = filesize($filename);
	            // get offset of chunk
	            list($dir, $offset) = explode('.patch.', $filename, 2);
	            // offsets
	            array_push($offsets, $offset);
	            // test if is missing previous chunk
	            // don't test first chunk (previous chunk is non existent)
	            if ($offset > 0 && !in_array($offset - $size, $offsets)) {
	                $last_offset = $offset - $size;
	                break;
	            }
	            // last offset is at least next offset
	            $last_offset = $offset + $size;
	        }

	        // return offset
	        http_response_code(200);
	        header('Upload-Offset: ' . $last_offset);
	        return;
	    }

	    // get patch data
	    $offset = $_SERVER['HTTP_UPLOAD_OFFSET'];
	    $length = $_SERVER['HTTP_UPLOAD_LENGTH'];
	    // should be numeric values, else exit
	    if (!is_numeric($offset) || !is_numeric($length)) {
	        return http_response_code(400);
	    }
	    // get sanitized name
	    $name = FilePond\sanitize_filename($_SERVER['HTTP_UPLOAD_NAME']);
	    // write patch file for this request
	    file_put_contents($dir . '.patch.' . $offset, fopen('php://input', 'r'));
	    // calculate total size of patches
	    $size = 0;
	    $patch = glob($dir . '.patch.*');
	    foreach ($patch as $filename) {
	        $size += filesize($filename);
	    }

	    // if total size equals length of file we have gathered all patch files
	    if ($size == $length) {
	    	// create output file
	        $file_handle = fopen($dir . $name, 'w');
	        // write patches to file
	        foreach ($patch as $filename) {
	        	// get offset from filename
	            list($dir, $offset) = explode('.patch.', $filename, 2);
	            // read patch and close
	            $patch_handle = fopen($filename, 'r');
	            $patch_contents = fread($patch_handle, filesize($filename));
	            fclose($patch_handle); 
	            // apply patch
	            fseek($file_handle, $offset);
	            fwrite($file_handle, $patch_contents);
	        }
	        // remove patches
	        foreach ($patch as $filename) {
	            unlink($filename);
	        }
	        // done with file
	        fclose($file_handle);
	    }

	    http_response_code(204);
	}

	public static function handle_revert_file_transfer($id) {
		$CI =& get_instance();
	    // test if id was supplied
	    if (!isset($id) || !FilePond\is_valid_transfer_id($id)) return http_response_code(400);
	    // remove transfer directory
	    FilePond\remove_transfer_directory($CI->_transfer_dir, $id);
	    // no content to return
	    http_response_code(204);
	}

	public static function handle_restore_file_transfer($id) {
		$CI =& get_instance();
	    // Stop here if no id supplied
	    if (empty($id) || !FilePond\is_valid_transfer_id($id)) return http_response_code(400);
	    // create transfer wrapper around upload

	    $transfer = FilePond\get_transfer($CI->_transfer_dir, $id);
	    // Let's get the temp file content
	    $files = $transfer->getFiles();
	    // No file returned, file not found
	    if (count($files) === 0) return http_response_code(404);
	    // Return file
	    FilePond\echo_file($files[0]);
	}

	public static function handle_load_local_file($ref) {
		$CI =& get_instance();
	    // Stop here if no id supplied
	    if (empty($ref)) return http_response_code(400);
	    // In this example implementation the file id is simply the filename and 
	    // we request the file from the uploads folder, it could very well be 
	    // that the file should be fetched from a database or a totally different system.
	    // path to file
	    $path = $CI->_upload_dir . DIRECTORY_SEPARATOR . FilePond\sanitize_filename($ref);
	    // Return file
	    FilePond\echo_file($path);
	}

	public static function handle_fetch_remote_file($url) {
		$CI =& get_instance();
	    // Stop here if no data supplied
	    if (empty($url)) return http_response_code(400);
	    // Is this a valid url
	    if (!FilePond\is_url($url)) return http_response_code(400);
	    // Let's try to get the remote file content
	    $file = FilePond\fetch($url);
	    // Something went wrong
	    if (!$file) return http_response_code(500);
	    // remote server returned invalid response
	    if ($file['error'] !== 0) return http_response_code($file['error']);
	    // if we only return headers we store the file in the transfer folder
	    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
	    	// deal with this file as if it's a file transfer, will return unique id to client
	        $transfer = new FilePond\Transfer();
	        $transfer->restore($file);
	        FilePond\store_transfer($CI->_transfer_dir, $transfer);
	        // send transfer id back to client
	        header('X-Content-Transfer-Id: ' . $transfer->getId());
	    }
	    // time to return the file to the client
	    FilePond\echo_file($file);
	}

	
}
