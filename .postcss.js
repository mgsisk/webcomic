/* eslint-env node */

module.exports = {
  plugins: [
    require( 'postcss-import' ),
    require( 'postcss-modular-scale-unit' ),
    require( 'postcss-mixins' ),
    require( 'postcss-short' ),
    require( 'rucksack-css' ),
    require( 'postcss-cssnext' )({browsers: 'last 2 versions, ie 11'}),
    require( 'cssnano' )({autoprefixer: false})
  ]
};
