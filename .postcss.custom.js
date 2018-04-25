/* eslint-env node */
/* global Set */

const postcss = require( 'postcss' );

module.exports = postcss.plugin( '.postcss.custom.js', ( options )=> {
	options = options || {};

	/**
	 * Calculate a modular scale value.
	 *
	 * @param {int}   power The power of the modular scale value.
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
	 * Calculate a unitless line height based on a modular scale.
	 *
	 * @param {float} lineHeight The base, unitless line height.
	 * @param {int}   power The power of the modular scale value.
	 * @param {float} ratio The modular scale ratio.
	 * @param {array} bases One or more modular scale bases.
	 * @return {float}
	 */
	function lineHeightScale( lineHeight, power, ratio, bases ) {
		let multiplier = 1;
		let output = lineHeight / modularScale( power, ratio, bases );

		while ( 1 > output ) {
			multiplier += 1;

			output *= multiplier;
		}

		return output;
	}

	return ( root )=> {
		root.walkDecls( ( decl )=> {
			if ( ':root' !== decl.parent.selector ) {
				return;
			}

			if ( '--font-size' === decl.prop  ) {
				options.fontSize = parseFloat( decl.value );
			} else if ( '--line-height' === decl.prop ) {
				options.lineHeight = parseFloat( decl.value );
			} else if ( '--modular-scale' === decl.prop ) {
				const [ ratio, ...bases ] = postcss.list.space( decl.value );

				options.ratio = parseFloat( ratio );
				options.bases = Array.from( new Set( bases ) ).map( value=> parseFloat( value ) );

				if ( ! options.bases.length ) {
					options.bases.push( 1 );
				}
			} else if ( '--vertical-rhythm' === decl.prop ) {
				options.rhythmUnit = decl.value;
			}
		});

		root.replaceValues(
			/\b(-?\d*\.?\d+)mlh\b/,
			value=> lineHeightScale( options.lineHeight, parseFloat( value ), options.ratio, options.bases )
		);

		root.replaceValues(
			/\b(-?\d*\.?\d+)mfs\b/,
			value=> modularScale( parseFloat( value ), options.ratio, options.bases ) * options.fontSize + options.rhythmUnit
		);

		root.replaceValues(
			/\b(-?\d*\.?\d+)msu\b/,
			value=> modularScale( parseFloat( value ), options.ratio, options.bases )
		);

		root.replaceValues(
			/\b(-?\d*\.?\d+)vru\b/,
			value=> parseFloat( value ) * options.lineHeight + options.rhythmUnit
		);
	};
});
