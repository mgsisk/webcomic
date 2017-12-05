/* eslint-env node */

const babel = require( 'rollup-plugin-babel' );
const uglify = require( 'rollup-plugin-uglify' );

module.exports = {
  format: 'iife',
  plugins: [ babel(), uglify() ]
};
