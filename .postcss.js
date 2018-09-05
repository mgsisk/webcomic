/* eslint-env node */

module.exports = {
	plugins: [
		require( 'postcss-import' ),
		require( './.postcss.custom.js' ),
		require( 'postcss-easings' ),
		require( 'postcss-short' ),
    require( 'postcss-preset-env' )({
      stage: 0,
      features: {
        'custom-properties': {preserve: false},
      },
    }),
		require( 'cssnano' )({preset: 'default'})
	]
};
