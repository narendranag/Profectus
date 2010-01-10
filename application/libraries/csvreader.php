<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* CSVReader Class
*
* $Id: csvreader.php 147 2007-07-09 23:12:45Z Pierre-Jean $
*
* Allows to retrieve a CSV file content as a two dimensional array.
* The first text line shall contains the column names.
*
* Let's consider the following CSV formatted data:
*
*        col1;col2;col3
*         11;12;13
*         21;22;23
*
* It's returned as follow by the parsing operations:
*
*         Array(
*             [0] => Array(
*                     [col1] => 11,
*                     [col2] => 12,
*                     [col3] => 13
*             )
*             [1] => Array(
*                     [col1] => 21,
*                     [col2] => 22,
*                     [col3] => 23
*             )
*        )
*
* @author        Pierre-Jean Turpeau
* @link        http://www.codeigniter.com/wiki/CSVReader
*/
class CSVReader {
    
    var $fields;        /** columns names retrieved after parsing */
    var $separator = ';';    /** separator used to explode each line */
    
    /**
     * Parse a text containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_text($p_Text) {
        $lines = explode("\n", $p_Text);
        return $this->parse_lines($lines);
    }
    
    /**
     * Parse a file containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_file($p_Filepath) {
        $lines = file($p_Filepath);
        return $this->parse_lines($lines);
    }
    
    /**
     * Parse an array of text lines containing CSV formatted data.
     *
     * @access    public
     * @param    array
     * @return    array
     */
    function parse_lines($p_CSVLines) {    
        $content = FALSE;
        foreach( $p_CSVLines as $line_num => $line ) {
            if( $line != '' ) { // skip empty lines
                $elements = split($this->separator, $line);

                if( !is_array($content) ) { // the first line contains fields names
                    $this->fields = $elements;
                    $content = array();
                } else {
                    $item = array();
                    foreach( $this->fields as $id => $field ) {
                        if( isset($elements[$id]) ) {
                            $item[$field] = $elements[$id];
                        }
                    }
                    $content[] = $item;
                }
            }
        }
        return $content;
    }

	// Added by NAG
	
	function get_csv_values($string, $separator=",")
	{
	    $elements = explode($separator, $string);
	    for ($i = 0; $i < count($elements); $i++) {
	        $nquotes = substr_count($elements[$i], '"');
	        if ($nquotes %2 == 1) {
	            for ($j = $i+1; $j < count($elements); $j++) {
	                if (substr_count($elements[$j], '"') > 0) {
	                    // Put the quoted string's pieces back together again
	                    array_splice($elements, $i, $j-$i+1,
	                        implode($separator, array_slice($elements, $i, $j-$i+1)));
	                    break;
	                }
	            }
	        }
	        if ($nquotes > 0) {
	            // Remove first and last quotes, then merge pairs of quotes
	            $qstr =& $elements[$i];
	            $qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
	            $qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
	            $qstr = str_replace('""', '"', $qstr);
	        }
	    }
	    return $elements;
	}
}

?>