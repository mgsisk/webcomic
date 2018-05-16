/* eslint-env node */

module.exports = {
	formatter: 'string',
	extends: [ 'stylelint-config-standard', 'stylelint-config-wordpress' ],
	plugins: [
		'stylelint-declaration-block-no-ignored-properties',
		'stylelint-order'
	],
	rules: {

		// ----- Possible Errors ---------------------------------------------------

		'font-family-no-missing-generic-family-keyword': null,
		'no-unknown-animations': true,
		'property-no-unknown': [ true, {ignoreProperties: [ 'size' ]} ],
		'unit-no-unknown': [ true, {ignoreUnits: [ 'mfs', 'mlh', 'msu', 'vru' ]} ],

		// ----- Limit Language Features -------------------------------------------

		'at-rule-no-vendor-prefix': true,
		'declaration-block-no-redundant-longhand-properties': true,
		'declaration-no-important': true,
		'function-url-no-scheme-relative': true,
		'max-nesting-depth': 4,
		'media-feature-name-no-vendor-prefix': true,
		'number-max-precision': 5,
		'property-no-vendor-prefix': true,
		'selector-max-attribute': 4,
		'selector-max-class': 4,
		'selector-max-combinators': 4,
		'selector-max-compound-selectors': 4,
		'selector-max-id': 1,
		'selector-max-pseudo-class': 4,
		'selector-max-specificity': '1,4,4',
		'selector-max-type': 4,
		'selector-max-universal': 1,
		'selector-nested-pattern': '^&.+',
		'selector-no-vendor-prefix': true,
		'shorthand-property-no-redundant-values': true,
		'time-min-milliseconds': 100,
		'value-no-vendor-prefix': true,

		// ----- Stylistic Issues --------------------------------------------------

		'at-rule-name-newline-after': 'always-multi-line',
		'at-rule-semicolon-space-before': 'never',
		'block-no-empty': null,
		'block-opening-brace-newline-before': 'never-single-line',
		'declaration-block-semicolon-newline-before': 'never-multi-line',
		'function-comma-newline-before': 'never-multi-line',
		'media-query-list-comma-newline-before': 'never-multi-line',
		'selector-list-comma-newline-before': 'never-multi-line',
		'selector-list-comma-space-after': 'always-single-line',
		'value-list-comma-newline-before': 'never-multi-line',

		// ----- Plugins -----------------------------------------------------------

		'order/properties-alphabetical-order': true,
		'plugin/declaration-block-no-ignored-properties': true
	}
};
