<?php
/*
Plugin Name: WP-Obscure
Plugin URI: http://www.psxdns.com/wp-obscure/
Description: Obfuscates post content with ascii characters and hidden html to circumvent duplicate content penalties...
Author: psxdns
Version: 1.1.1
Author URI: http://www.psxdns.com/

#### COPYRIGHT:		This program is free software: you can redistribute it and/or modify
			it under the terms of the GNU General Public License as published by
			the Free Software Foundation, either version 3 of the License, or
			(at your option) any later version.

			This program is distributed in the hope that it will be useful,
			but WITHOUT ANY WARRANTY; without even the implied warranty of
			MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
			GNU General Public License for more details.

			You should have received a copy of the GNU General Public License
			along with this program.  If not, see <http://www.gnu.org/licenses/>.

#### COMMENTS:		I understand this code is basic and isn't that optimized. This is my
			first wordpress plugin and it wasn't intended to do anything more
			than described in the description. If you have any suggestions for
			improvement or additional features, contact me.

*/


// integrate with wordpress
add_filter('the_content','run_obscure');


// main function
function run_obscure($string) {

	// random, hidden html tags to be inserted into post for obfuscation
	$rndhtml = array('<input id="counter" type="hidden" />',
		 '<input id="stats" type="hidden" />',
		 '<input id="tracker" type="hidden" />',
		 '<input id="apps" type="hidden" />',
		 '<input id="phpint" type="hidden" />',
		 '<s></s>',
		 '<input type="hidden" />');


	// start character-by-character check loop
	for ($i=0; $i<strlen($string); $i++){
		
		// check for html tag, if so, skip
		if ($string[$i] == "<") { $script = 1; }

		// if not skipping, increase counts
		if ($script == 0) { $c++; $h++; }

		// we don't want to interrupt any current ascii conversions, being safe
		if ($string[$i] == "&") { $c = $c - 10; $h = $h - 10; }
		if ($string[$i] == ";") { $c = $c - 10; $h = $h - 10; }

		// after 80 characters, lets insert hidden html into the post
		if ($h == 80) { $obscure .= $rndhtml[array_rand($rndhtml)]; $h = 0; }

		// characters 51, 52 and 53 will be converted to ascii values
		if ($c > 50 && $c < 54 && $script == 0) {
			$obscure .= "&#" . ord($string[$i]);
		} else {
			$obscure .= $string[$i];
		}

		
		if ($c == 53) { $c = 0; }

		// detect close of html tag and resume obfuscation
		if ($string[$i] == ">") { $script = 0; }
	}

	// return obscured post
	return $obscure;
}
?>