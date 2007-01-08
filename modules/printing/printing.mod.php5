<?php
/**
* Just contains the definition for the class {@link EighthPrint}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the utilities for an eighth period printing.
* @package modules
* @subpackage Eighth
*/

class Printing {

	public static $sections = array();
	public static $printing_path = NULL;

	public static function print_parking($people, $format = 'pdf') {
		$output = self::latexify('parking');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Parking', 'Parking Applications as of ' . date('F j, Y'), TRUE);
			}
			self::do_display($output, $format, 'Parking Applications as of ' . date('F j, Y'), TRUE);
		}
	}

	/**
	* Prints the given LaTeX output.
	*
	* @access private
	* @param string The LaTeX output to print.
	*/
	private static function do_print($output, $landscape = FALSE) {
		$temp = tempnam('/tmp', 'IodinePrinting');
		file_put_contents($temp, $output);
		exec("cd /tmp; latex {$temp}");
		exec("cd /tmp; dvips {$temp}.dvi -t letter" . ($landscape ? ' -t landscape' : ''));
		$ftpconn = ftp_connect('198.38.28.59');
		ftp_login($ftpconn, 'anonymous', '');
		ftp_chdir($ftpconn, 'PORT1');
		ftp_put($ftpconn, "{$temp}.ps", "{$temp}.ps", FTP_BINARY);
		ftp_close($ftpconn);
	}
	
	/**
	* Displays the given LaTeX output.
	*
	* @access private
	* @param string The LaTeX output to print.
	*/
	private static function do_display($output, $format, $filename, $landscape = FALSE) {
		Display::stop_display();
		$temp = tempnam('/tmp', 'IodinePrinting');
		file_put_contents("{$temp}", $output);
		//$disposition = 'attachment';
		$disposition = 'inline';
		if($format == 'pdf') {
			exec("cd /tmp; pdflatex {$temp}");
			exec("cd /tmp; pdflatex {$temp}");
			header('Content-type: application/pdf');
		}
		else if($format == 'ps') {
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; dvips {$temp}.dvi -t letter" . ($landscape ? ' -t landscape' : ''));
			header("Content-type: application/postscript");
		}
		else if($format == 'dvi') {
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; latex {$temp}");
			header('Content-type: application/x-dvi');
		}
		else if($format == 'tex' || $format == 'latex') {
			rename($temp, "{$temp}.{$format}");
			header('Content-type: text/plain');
		}
		else if($format == 'html') {
			header('Content-type: text/html');
			$disposition = 'inline';
		}
		else if($format == 'rtf') {
			rename($temp, "{$temp}.tex");
			exec("cd /tmp; latex2rtf {$temp}");
			header('Content-type: application/rtf');
		}
		header("Content-Disposition: {$disposition}; filename=\"{$filename}.{$format}\"");
		readfile("{$temp}.{$format}");
	}

	/**
	* Takes input file and makes it into valid latex output
	*
	* @access private
	* @param $filename string The filename
	*/
	private static function latexify($filename) {
		if(!self::$printing_path) {
			self::$printing_path = i2config_get('printing_path', NULL, 'printing');
		}
		$lines = file(self::$printing_path . "{$filename}.tex.in");
		self::$sections = array();
		$currsections = array();
		$code = '';
		$output = '';
		$echoed = FALSE;
		$incode = FALSE;
		foreach($lines as $line) {
			$line = trim($line);
			if(preg_match('/^\%\@begin (.*)$/', $line, $matches)) {
				$currsections[] = $matches[1];
				self::$sections[$matches[1]] = '';
			}
			else if(preg_match('/^\%\@end (.*)$/', $line, $matches)) {
				unset($currsections[array_search($matches[1], $currsections)]);
			}
			else if(preg_match('/^\%\@include (.*)$/', $line, $matches)) {
				if(count($currsections) == 0) {
					if(!$echoed) {
						$output .= "echo '";
						$echoed = TRUE;
					}
					$output .= self::$sections[$matches[1]];
				}
				else {
					foreach($currsections as $section) {
						self::$sections[$section] .= self::$sections[$matches[1][0]];
					}
				}
			}
			else if(substr($line, 0, 3) == '%@?') {
				$output .= "';\n";
				$echoed = FALSE;
				$incode = TRUE;
				if(substr($line, 3) != '') {
					if(substr($line, -2) != '@%') {
						$code .= substr($line, 3) . "\n";
					}
					else {
						$code .= substr($line, 3, -2);
						$output .= "{$code}\n";
						$code = '';
						$incode = FALSE;
					}
				}
			}
			else if($incode && substr($line, -2) == '@%') {
				$output .= "{$code}\n";
				$code = '';
				$incode = FALSE;
			}
			else {
				$line = preg_replace('/%@(.*?)@%/', '\' . strtr($1, array(\'$\' => \'\\\\$\', \'&\' => \'\&\', \'%\' => \'\%\', \'{\' => \'\{\', \'}\' => \'\}\', \'_\' => \'\_\', \'#\' => \'\#\')) . \'', $line);
				$line = strtr($line, array('\\' => '\\\\'));
				if(count($currsections) == 0) {
					if($incode) {
						$code .= "{$line}\n";
					}
					else {
						if(!$echoed) {
							$output .= "echo '";
							$echoed = TRUE;
						}
						$output .= "{$line}\n";
					}
				}
				else {
					foreach($currsections as $section) {
						self::$sections[$section] .= "{$line}\n";
					}
				}
			}
		}
		if($echoed) {
			$output .= "';";
		}
		return $output;
	}

	/**
	* Add PDF information to the file
	*
	* @access private
	* @param string The file contents
	* @param string Producer
	* @param string Title
	*/
	private static function add_info(&$output, $producer = '', $title = '') {
		$output = "\pdfinfo {
/Author (Eighth Period Office)
/Producer ({$producer})
/Title ({$title})
/Creator (Eighth Period Office Online)
}
{$output}";
	}
}
