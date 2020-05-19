/**
 * Aurora Heatmap Reporter
 *
 * @package aurora-heatmap
 * @copyright 2019-2020 R3098 <info@seous.info>
 * @version 1.4.3
 */

/**
 * Anonymous function for scope
 */
(function($) {
	"use strict";

	var html, body;

	/**
	 * Main object
	 */
	var self = {
		args: aurora_heatmap_reporter,
		readY: 0,
		readPosition: 1,
		readTimerCount: 0,
		maxReadY: 0,
		disabled: false,

		/**
		 * Initializer
		 */
		init: function() {
			html = document.documentElement;
			body = document.body;

			this.args.interval = ( parseInt( this.args.interval ) || 10 ) * 1000;
			this.args.stacks   = parseInt( this.args.stacks ) || 10;
			this.args.reports  = this.args.reports.split( ',' );
			this.args.debug    = ! ! parseInt( this.args.debug );

			const callback_click     = self.push_click.bind( self );
			const callback_breakaway = self.push_breakaway.bind( self );

			if ( 'mobile' === this.args.access ) {
				this.readPosition = 0.1; // 10% of the window.
				window.addEventListener( 'pagehide', callback_breakaway );
				Array.prototype.forEach.call(
					document.body.children,
					function( e ) {
						e.addEventListener( 'click', callback_click );
					}
				);
			} else {
				this.readPosition = 0.5; // Center of the screen.
				window.addEventListener( 'beforeunload', callback_breakaway );
				document.addEventListener( 'click', callback_click );
			}

			/**
			 * Hook Calculate reading area
			 */
			window.setInterval(
				function() {
					self.calc_attention();
				},
				1000
			);
		},

		/**
		 * Get current readY
		 */
		getReadY: function() {
			return Math.floor( this.getScrollTop() + this.getWindowHeight() * this.readPosition );
		},

		/**
		 * Calcurate attention area
		 */
		calc_attention: function() {
			var readY = this.getReadY();
			this.maxReadY = Math.max( this.maxReadY, readY );
			if ( readY === this.readY ) {
				this.readTimerCount++;
			} else {
				this.readTimerCount = 0;
			}
			this.readY = readY;
			if ( 3 === this.readTimerCount ) {
				this.push_attention();
			}
			var now = new Date().getTime();
			if ( now - this.lastTime > this.args.interval ) {
				this.push_data( null, true );
			}
		},

		/**
		 * Get Mouse Cursor Position
		 *
		 * @param event event
		 * @return Object
		 */
		getCursorPos: function(event) {
			var x, y;
			if ((event.clientX || event.clientY) && body.scrollLeft) {
				return {
					x: event.clientX + body.scrollLeft,
					y: event.clientY + body.scrollTop,
				};
			} else if ((event.clientX || event.clientY) && document.compatMode == 'CSS1Compat' && html.scrollLeft) {
				return {
					x: event.clientX + html.scrollLeft,
					y:  event.clientY + html.scrollTop,
				};
			} else if (event.pageX || event.pageY) {
				return {
					x: event.pageX,
					y: event.pageY,
				};
			}
		},

		/**
		 * Push click data
		 *
		 * @param event event
		 */
		push_click: function(event) {
			var pos = this.getCursorPos( event );
			if ( ! pos ) {
				return;
			}
			pos.event = 'click_' + this.args.access;
			this.push_data( pos, true );
		},

		/**
		 * Get content end
		 */
		getContentEnd: function() {
			var e = document.getElementsByClassName( 'ahm-content-end-marker' ), y = 0;
			if ( e && e.length ) {
				e = e[ e.length - 1 ];
				y = this.getPageHeight() - window.pageYOffset - e.getBoundingClientRect().bottom;
				y = Math.max( 0, Math.floor( y ) );
			}
			return y;
		},

		/**
		 * Push breakaway data
		 *
		 * @param event event
		 */
		push_breakaway: function(event) {
			var maxReadY = Math.max( this.maxReadY, this.getReadY() );

			this.push_data(
				{
					event: 'breakaway_' + this.args.access,
					x: this.getContentEnd(),
					y: maxReadY,
				},
				false
			);
		},

		/**
		 * Push attention data
		 */
		push_attention: function() {
			this.push_data(
				{
					event: 'attention_' + this.args.access,
					x: this.getContentEnd(),
					y: Math.floor( this.readY ),
				},
				true
			);
		},

		/**
		 * Get Scroll Top
		 *
		 * @return Number
		 */
		getScrollTop: function() {
			return html.scrollTop || body.scrollTop;
		},

		/**
		 * Get Page Width
		 *
		 * @return Number
		 */
		getPageWidth: function() {
			return html.clientWidth || body.clientWidth || 0;
		},

		/**
		 * Get Page Height
		 *
		 * @return Number
		 */
		getPageHeight: function() {
			return Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
		},

		/**
		 * Get Window Height
		 *
		 * @return Number
		 */
		getWindowHeight: function() {
			return window.innerHeight || $( window ).height() || null;
		},

		/**
		 * Temporary stacking data
		 */
		stack: [],

		/**
		 * Last sending time
		 */
		lastTime: new Date().getTime(),

		/**
		 * Build preview HTML
		 *
		 * For debug.
		 *
		 * @param Object e
		 * @return String
		 */
		build_preview: function( e ) {
			return '<div><b>event=</b> ' + e.event + ' <b>x=</b> ' + ('x' in e ? e.x : 'null') + ' <b>y=</b> ' + e.y + ' <b>height=</b> ' + e.height + ' <b>width=</b> ' + e.width + '</div>';
		},

		/**
		 * Show preview HTML
		 *
		 * @param String title
		 * @param Array  content
		 * @param Object style
		 * @param Number timeout
		 */
		show_preview: function( title, content, style, timeout ) {
			var div = document.createElement( 'div' );
			div.setAttribute( 'style', 'color:#000;padding:0.2em;position:fixed;right:0;border:1px solid #000;font-family:monospace;z-index:999999;' );
			div.style.background = style.background;
			div.style.top        = style.top;
			div.innerHTML        = '<div style="color:' + style.color + '"><b>' + title + '</b></div>' + content.map( this.build_preview ).join( '' );
			body.appendChild( div );
			window.setTimeout(
				function() {
					body.removeChild( div );
				},
				timeout
			);
		},

		/**
		 * Push data
		 *
		 * @param Object  data
		 * @param Boolean is_async
		 */
		push_data: function(data, is_async) {
			if ( this.disabled ) {
				return;
			}

			var now = new Date().getTime(), post;

			if ( data && ~this.args.reports.indexOf( data.event ) ) {
				data.time   = now;
				data.width  = this.getPageWidth();
				data.height = this.getPageHeight();
				this.stack.push( data );

				// Debug: display stored data for 1 second.
				if ( this.args.debug ) {
					this.show_preview( 'Store', [ data ], { color: '#963', background: '#ffc', top: '0' }, 1000 );
				}
			}

			// For async, check interval and stacks.
			if (is_async && (now - this.lastTime) < this.args.interval && this.stack.length < this.args.stacks) {
				return;
			}

			// Stacked no data, do nothing.
			if ( ! this.stack.length) {
				return;
			}

			[post, this.stack] = [this.stack, []];
			post.forEach( function(e) { e.time = Math.floor( (e.time - now) / 1000 ); } );
			this.lastTime = now;

			// Debug: display sending data for 5 seconds.
			if ( this.args.debug ) {
				this.show_preview( 'Send', post, { color: '#369', background: '#cff', top: '4em' }, 5000 );
			}

			var res = $.ajax( {
				type: 'POST',
				datatype: 'json',
				url: this.args.ajax_url,
				cache: false,
				timeout: 3000,
				async: is_async,
				data: {
					url: document.location.href,
					title: document.title,
					data: post,
					action: this.args.action,
				},
			} );

			if ( res.readyState ) {
				return;
			}

			if ( navigator.sendBeacon ) {
				var form = new FormData();
				form.append( 'action', this.args.action );
				form.append( 'url', document.location.href );
				form.append( 'title', document.title );
				post.forEach( function( e, i ) {
					for ( var key in e ) {
						if ( ! e.hasOwnProperty( key ) ) {
							continue;
						}
						form.append( 'data[' + i + '][' + key + ']', e[key] );
					}
				} );

				if ( navigator.sendBeacon( this.args.ajax_url, form ) ) {
					return;
				}
			}

			self.disabled = true;
		},
	};

	function init() {
		self.init();
	}

	if (
		'object' !== typeof aurora_heatmap_reporter ||
		~location.search.indexOf( "aurora-heatmap=" )
	) {
		return;
	} else if ( 'loading' !== document.readyState ) {
		init();
	} else {
		document.addEventListener( 'DOMContentLoaded', init );
	}
})( jQuery );

/* vim: set ts=4 sw=4 sts=4 noet: */
