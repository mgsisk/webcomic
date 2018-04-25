/* eslint-env node */

module.exports = {
	plugins: [
		require( 'postcss-import' ),
		require( './.postcss.custom.js' ),
		require( 'postcss-easings' ),
		require( 'postcss-short' ),
		require( 'postcss-cssnext' )({
			browsers: 'last 2 versions, ie 11'
		}),
		require( 'cssnano' )({
			autoprefixer: false
		})
	]
};
