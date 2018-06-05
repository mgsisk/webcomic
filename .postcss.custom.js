/* eslint-env node */
/* global Set */

const postcss = require( 'postcss' );

/**
 * Add custom units for modular scale vertical rhythm values.
 */
module.exports = postcss.plugin( '.postcss.custom.js', ( options )=> {
	options = options || {
		fontSize: 1,
		fontUnit: 'em',
		lineHeight: 1.5,
		rhythmUnit: 'rem',
		ratio: 1.2,
		bases: [ 1 ],
		round: 5
	};

	/**
	 * Calculate a modular scale value.
	 *
	 * @param {int} power The power of the modular scale value.
	 * @param {float} ratio The modular scale ratio.
	 * @param {array} bases One or more modular scale bases.
	 * @return {float}
	 */
	function modularScale( power, ratio, bases ) {
		const scale = [];
		let step = 0;

		while ( Math.abs( step ) <= Math.abs( power ) ) {
			for ( let i = 0; i < bases.length; i++ ) {
				scale.push( bases[i] * Math.pow( ratio, step ) );
			}

			step += 1;

			if ( 0 > power ) {
				step -= 2;
			}
		}

		return Array.from( new Set( scale ) )[Math.abs( step ) - 1];
	}

	/**
	 * Calculate a unitless line height for a given modular scale.
	 *
	 * @param {float} lineHeight The base, unitless line height.
	 * @param {int} power The power of the modular scale value.
	 * @param {float} ratio The modular scale ratio.
	 * @param {array} bases One or more modular scale bases.
	 * @return {float}
	 */
	function lineHeightScale( lineHeight, power, ratio, bases ) {
		const baseHeight = lineHeight / modularScale( power, ratio, bases );
		let realHeight = baseHeight;

		while ( 1 > realHeight ) {
			realHeight += baseHeight;
		}

		return realHeight;
	}

	return ( root )=> {
		root.walkDecls( ( decl )=> {
			if ( ':root' !== decl.parent.selector ) {
				return;
			}

			if ( '--font-size' === decl.prop ) {
				options.fontSize = parseFloat( decl.value );
				options.fontUnit = decl.value.replace( /(\d|\.|-)+/, '' );
			} else if ( '--line-height' === decl.prop ) {
				options.lineHeight = parseFloat( decl.value );
				options.rhythmUnit = decl.value.replace( /(\d|\.|-)+/, '' );
			} else if ( '--modular-scale' === decl.prop ) {
				const [ ratio, ...bases ] = postcss.list.space( decl.value );

				options.ratio = parseFloat( ratio );
				options.bases = Array.from( new Set( bases ) ).map( value=> parseFloat( value ) );

				if ( ! options.bases.length ) {
					options.bases.push( 1 );
				}
			}
		});

		root.replaceValues(
			/(-?\d*\.?\d+)mfs\b/g,
			( value )=> {
				const size = modularScale( parseFloat( value ), options.ratio, options.bases ) * options.fontSize;

				return size.toFixed( options.round ) + options.fontUnit;
			}
		);

		root.replaceValues(
			/(-?\d*\.?\d+)mlh\b/g,
			( value )=> {
				const height = lineHeightScale( options.lineHeight, parseFloat( value ), options.ratio, options.bases );

				return height.toFixed( options.round );
			}
		);

		root.replaceValues(
			/(-?\d*\.?\d+)msu\b/g,
			value=> modularScale( parseFloat( value ), options.ratio, options.bases ).toFixed( options.round )
		);

		root.replaceValues(
			/(-?\d*\.?\d+)vru\b/g,
			( value )=> {
				const rhythm = parseFloat( value ) * options.lineHeight;

				return rhythm.toFixed( options.round ) + options.rhythmUnit;
			}
		);
	};
});
