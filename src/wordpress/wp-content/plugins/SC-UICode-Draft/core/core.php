<?php

/**
 * 
 * @return string/false
 */
function get_include_contents($filename) {
	if (is_file(PLUGPATH . $filename)) {
        ob_start();
        include PLUGPATH . $filename;
        return ob_get_clean();
    }

    include PLUGPATH . $filename;
    return false;
}