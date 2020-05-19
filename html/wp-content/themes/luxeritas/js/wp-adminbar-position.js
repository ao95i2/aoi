/*! Luxeritas WordPress Theme - free/libre wordpress platform
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @link https://thk.kanzae.net/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 */

/*
-------------------------------------------------------
 ログイン時に管理バーが見えてる時の位置調整用スクリプト
------------------------------------------------------- */
if( typeof( document.getElementsByClassName ) === 'undefined' ){
	document.getElementsByClassName = function(t){
		var d = document
		,   e = new Array();
		if( d.all ) {
			var allelem = d.all;
		} else {
			var allelem = d.getElementsByTagName("*");
		} for( var i = j = 0, l = allelem.length; i < l; i++ ) {
			var names = allelem[i].className.split( /\s/ );
			for( var k = names.length - 1; k >= 0; k-- ) {
				if( names[k] === t ) {
					e[j] = allelem[i];
					j++;
					break;
				}
			}
		}
		return e;
	};
}

function adbarPosition() {
	var b, c, e, w = window
	,   d = document
	,   a = d.getElementById('wpadminbar');

	b = a.offsetHeight;

	if( typeof w.getComputedStyle !== 'undefined' ) {
		if( d.getElementById('wp-admin-bar-top-secondary') !== null ) {
			c = w.getComputedStyle(a).backgroundColor;
			d.getElementById('wp-admin-bar-top-secondary').style.backgroundColor = c;
		}
	}

	e = d.getElementsByClassName('band');
	if( e.length > 0 ) {
		e[0].style.top = b + 'px';
	}
}

!function(w) {
	if( document.getElementById('wpadminbar') !== null ) {
		var a, b = null;

		adbarPosition();

		a = ('resize', function() {
			if( b === null ) {
				b = setTimeout( function() {
					adbarPosition();
					b = null;
				}, 200 );
			}
		});

		if( typeof w.addEventListener !== 'undefined' ) {
			w.addEventListener( 'resize', a, false );
		} else if( typeof w.attachEvent !== 'undefined' ) {
			w.attachEvent( 'onresize', a );
		}
	}
}(window);
