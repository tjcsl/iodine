<?php
/**
* Just contains the definition for the class {@link Barcode}.
* @author The Intranet 2 Development Team <intranet@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Barcode
* @filesource
*/

/**
* Includes code retrieved from
* http://www.sid6581.net/cs/php-scripts/barcode/
* on 2007/09/18.
*/

/**
* A {@link Module} to generate a barcode from a given string.
* @package core
* @subpackage Module
*/
class Barcode implements Module {

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	function display_box($disp) {
	}
	
	function display_pane($disp) {
		global $I2_ARGS;
		Display::stop_display();

		$width = isset($I2_ARGS[3]) ? $I2_ARGS[3] : 400;
		$height = isset($I2_ARGS[4]) ? $I2_ARGS[4] : 60;
		$out = Barcode::gen_barcode($I2_ARGS[1], $I2_ARGS[2], $width, $height);
		header("Content-type: image/jpeg");
		ImageJPEG ($out, "", 100);
	}
	
	function get_name() {
		return "Barcode";
	}

	function init_box() {
		return FALSE;
	}

	function init_pane() {
		return "";
	}

	static function gen_barcode($str, $text, $width, $height) {
		$im = ImageCreate ($width, $height)
			or die ("Cannot Initialize new GD image stream");
		$White = ImageColorAllocate ($im, 255, 255, 255);
		$Black = ImageColorAllocate ($im, 0, 0, 0);
		ImageInterLace ($im, 1);

		$NarrowRatio = 20;
		$WideRatio = 55;
		$QuietRatio = 35;

		$nChars = (strlen($str)+2) * ((6 * $NarrowRatio) + (3 * $WideRatio) + ($QuietRatio));
		$Pixels = $width / $nChars;
		$NarrowBar = (int)(20 * $Pixels);
		$WideBar = (int)(55 * $Pixels);
		$QuietBar = (int)(35 * $Pixels);

		$ActualWidth = (($NarrowBar * 6) + ($WideBar*3) + $QuietBar) * (strlen ($str)+2);

		if (($NarrowBar == 0) || ($NarrowBar == $WideBar) || ($NarrowBar == $QuietBar) || ($WideBar == 0) || ($WideBar == $QuietBar) || ($QuietBar == 0))
			throw new I2Exception("barcode: image is too small!");

		$CurrentBarX = (int)(($width - $ActualWidth) / 2);
		$Color = $White;
		$BarcodeFull = "*".strtoupper ($str)."*";
		settype ($BarcodeFull, "string");

		$FontNum = 3;
		$FontHeight = ImageFontHeight ($FontNum);
		$FontWidth = ImageFontWidth ($FontNum);
		if ($text != 0)
		{
			$CenterLoc = (int)(($width-1) / 2) - (int)(($FontWidth * strlen($BarcodeFull)) / 2);
			ImageString ($im, $FontNum, $CenterLoc, $height-$FontHeight, "$BarcodeFull", $Black);
		} else {
			$FontHeight=-2;
		}

		for ($i=0; $i<strlen($BarcodeFull); $i++)
		{
			$StripeCode = Barcode::code39($BarcodeFull[$i]);

			for ($n=0; $n < 9; $n++)
			{
				if ($Color == $White)
					$Color = $Black;
				else
					$Color = $White;

				switch ($StripeCode[$n])
				{
				case '0':
					ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$NarrowBar, $height-1-$FontHeight-2, $Color);
					$CurrentBarX += $NarrowBar;
					break;
				case '1':
					ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$WideBar, $height-1-$FontHeight-2, $Color);
					$CurrentBarX += $WideBar;
					break;

				}
			}
			$Color = $White;
			ImageFilledRectangle ($im, $CurrentBarX, 0, $CurrentBarX+$QuietBar, $height-1-$FontHeight-2, $Color);
			$CurrentBarX += $QuietBar;
		}

		return $im;
	}

	static function code39 ($Asc)
	{
		switch ($Asc)
		{
			case ' ':
				return "011000100";     
			case '$':
				return "010101000";             
			case '%':
				return "000101010"; 
			case '*':
				return "010010100"; // * Start/Stop
			case '+':
				return "010001010"; 
			case '|':
				return "010000101"; 
			case '.':
				return "110000100"; 
			case '/':
				return "010100010"; 
			case '-':
				return "010000101";
			case '0':
				return "000110100"; 
			case '1':
				return "100100001"; 
			case '2':
				return "001100001"; 
			case '3':
				return "101100000"; 
			case '4':
				return "000110001"; 
			case '5':
				return "100110000"; 
			case '6':
				return "001110000"; 
			case '7':
				return "000100101"; 
			case '8':
				return "100100100"; 
			case '9':
				return "001100100"; 
			case 'A':
				return "100001001"; 
			case 'B':
				return "001001001"; 
			case 'C':
				return "101001000";
			case 'D':
				return "000011001";
			case 'E':
				return "100011000";
			case 'F':
				return "001011000";
			case 'G':
				return "000001101";
			case 'H':
				return "100001100";
			case 'I':
				return "001001100";
			case 'J':
				return "000011100";
			case 'K':
				return "100000011";
			case 'L':
				return "001000011";
			case 'M':
				return "101000010";
			case 'N':
				return "000010011";
			case 'O':
				return "100010010";
			case 'P':
				return "001010010";
			case 'Q':
				return "000000111";
			case 'R':
				return "100000110";
			case 'S':
				return "001000110";
			case 'T':
				return "000010110";
			case 'U':
				return "110000001";
			case 'V':
				return "011000001";
			case 'W':
				return "111000000";
			case 'X':
				return "010010001";
			case 'Y':
				return "110010000";
			case 'Z':
				return "011010000";
			default:
				return "011000100"; 
		}
	}
}
?>
