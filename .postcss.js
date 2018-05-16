/* eslint-env node */

module.exports = {
	plugins: [
		require( 'postcss-import' ),
		require( './.postcss.custom.js' ),
		require( 'postcss-easings' ),
		require( 'postcss-short' ),
		require( 'postcss-cssnext' )(),
		require( 'cssnano' )({autoprefixer: false})
	]
};
