/**
 * Aurora Heatmap Viewer
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
		args: aurora_heatmap_viewer,

		/**
		 * Initializer
		 */
		init: function() {
			html = document.documentElement;
			body = document.body;

			this.args.count_bar = parseInt( this.args.count_bar );
			this.args.width     = parseInt( this.args.width );

			var data = JSON.parse( this.args.data );
			var div  = this.createHeatmapContainer();
			var palette;
			this.set_viewport();
			switch (this.args.event) {
				case 'click_pc':
				case 'click_mobile':
					this.drawHeatmap( div, data );
					break;
				case 'breakaway_pc':
				case 'breakaway_mobile':
					palette = chroma
						.scale( [ '#848484', '#9A9B6C', '#B0A25A', '#C0A847', '#E29A34', '#FD8D3C' ] )
						.mode( 'lab' );
					this.drawVerticalHeatmap( div, data, palette, true );
					break;
				case 'attention_pc':
				case 'attention_mobile':
					palette = chroma
						.scale( [ '#004046', '#006D72', '#00CED1', '#FFD700', '#FFFF00' ] )
						.domain( [ 0, 0.06, 0.16, 0.9, 1 ] )
						.mode( 'lab' );
					this.drawVerticalHeatmap( div, data, palette, false );
					break;
			}
			this.setPageHeightAdjuster( div );
		},

		/**
		 * Set viewport for non PC
		 */
		set_viewport : function() {
			var view_width;
			if ( this.args.event.endsWith( '_pc' ) ) {
				view_width = 1250;
			}
			if ( this.args.event.endsWith( '_mobile' ) ) {
				view_width = 375;
			}
			if ( 'pc' === this.args.access_from || ! view_width ) {
				return;
			}
			var viewport = $( 'meta[name="viewport"]' );
			var content  = 'width=' + view_width;
			if ( viewport ) {
				viewport.attr( 'content', content );
			} else {
				viewport         = document.createElement( 'meta' );
				viewport.name    = 'viewport';
				viewport.content = content;
				document.head.appendChild( viewport );
			}
		},

		/**
		 * Get page height
		 */
		getPageHeight: function() {
			return Math.max( body.scrollHeight, body.offsetHeight, html.offsetHeight );
		},

		/**
		 * Create heatmap Container
		 *
		 * @return Element
		 */
		createHeatmapContainer: function() {
			var div       = document.createElement( 'div' );
			div.className = 'ahm-heatmap-container';
			body.appendChild( div );
			return div;
		},

		/**
		 * Set page height adjuster
		 *
		 * @param Element div
		 */
		setPageHeightAdjuster: function( div ) {
			window.setInterval(
				function() {
					var h = self.getPageHeight() + 'px';
					if (div.style.height !== h) {
						div.style.height = h;
					}
				},
				1000
			);
		},

		/**
		 * Draw vertical heatmap
		 *
		 * @param Element  div
		 * @param Array    data
		 * @param Function palette
		 * @param Boolean  is_show_ratio
		 */
		drawVerticalHeatmap: function(div, data, palette, is_show_ratio) {
			if ( ! data ) {
				return;
			}

			/**
			 * Make label
			 *
			 * @param Array data
			 * @return Array
			 */
			function make_label(data) {
				// Convert 0.0 ~ 1.0 to 0 ~ 100 step 10.
				var steps = data.map(
					function(e) {
						return Math.floor( e * 10 ) * 10;
					}
				);

				var labels = new Array( data.length );

				// Get changing index.
				steps.reduce(
					function(a, c, i) {
						if (a !== c) {
							labels[i] = steps[i] + '%';
						}
						return c;
					}
				);

				return labels;
			}

			if (is_show_ratio) {
				data.ratios = make_label( data.ratios );
			}

			var color_prev, max_index = data.colors.length - 1;
			div.style.minHeight       = (40 * data.colors.length) + 'px';
			data.colors.forEach(
				function(colors, i) {
					var grad = document.createElement( 'div' ), bg;
					// Set background, linear-gradient.
					var color_next = (i < max_index) ? data.colors[i + 1][0] : colors[3] || colors[2] || colors[1] || colors[0];
					if ( colors[0] === colors[2] && colors[2] === color_next ) {
						bg = palette( color_next ).alpha( 0.5 ).css();
					} else {
						var c = [
							palette( colors[0] ).alpha( 0.5 ).css(),
							palette( colors[1] || colors[0] ).alpha( 0.5 ).css(),
							palette( colors[2] || colors[1] || colors[0] ).alpha( 0.5 ).css(),
							palette( colors[3] || colors[2] || colors[1] || colors[0] ).alpha( 0.5 ).css(),
							palette( color_next ).alpha( 0.5 ).css(),
						];
						bg    = 'linear-gradient(to bottom,' + c.join( ',' ) + ')';
					}
					grad.setAttribute( 'style', 'background:' + bg );
					color_prev = color_next;
					// Set ratio label.
					if ( is_show_ratio && data.ratios[i] ) {
						grad.innerHTML = '<span>' + data.ratios[i] + '</span>';
					}
					// Count bar.
					if ( self.args.count_bar ) {
						grad.innerHTML += '<span class="count-bar">' + data.counts[i] + '</span>';
					}
					grad.className = 'height-40px';
					div.appendChild( grad );
				}
			);
			// Automatic stretch element.
			var grad = document.createElement( 'div' );
			grad.setAttribute( 'style', 'flex:1 1 auto;width:100%;background:' + palette( color_prev ).alpha( 0.5 ).css() + ';' );
			div.appendChild( grad );
		},

		/**
		 * Draw heatmap
		 *
		 * @param Element div
		 * @param Array   data
		 */
		drawHeatmap: function(div, data) {
			// Count bar.
			if ( this.args.count_bar ) {
				data.counts.forEach(
					function(e, i) {
						var s       = document.createElement( 'div' );
						s.className = 'count-bar';
						s.style.top = (40 * i) + 'px';
						s.innerText = e;
						div.appendChild( s );
					}
				);
			}
			var max_height = data.points.reduce( function(prev, curr) { return Math.max( curr.y, prev ); }, 0 );
			max_height     = Math.ceil( max_height / 4000 );
			// Draw heatmap on canvas every 4000px.
			for (var i = 0; i <= max_height; i++) {
				var id  = 'heatmapCanvas' + i;
				var cv  = document.createElement( 'div' );
				var top = i * 4000;
				cv.setAttribute( 'id', id );
				cv.style.width  = this.args.width + 'px';
				cv.style.height = '4000px';
				cv.style.top    = top + 'px';
				div.appendChild( cv );
				var heatmap = h337.create(
					{
						container: cv,
						maxOpacity: .6,
						radius: 50,
						blur: .9,
						backgroundColor: 'transparent',
					}
				);
				heatmap.setData(
					{
						min: 0,
						max: 5,
						data: data.points.map(
							function(e) {
								return {
									x: e.x,
									y: e.y - top,
									value: 1
								};
							}
						)
					}
				);
			}
		}
	};

	function init() {
		self.init();
	}

	if ( 'object' !== typeof aurora_heatmap_viewer ) {
		return;
	} else if ( 'loading' !== document.readyState ) {
		init();
	} else {
		document.addEventListener( 'DOMContentLoaded', init );
	}
})( jQuery );

/* vim: set ts=4 sw=4 sts=4 noet: */
