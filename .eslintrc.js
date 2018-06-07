/* eslint-env node */

module.exports = {
	parserOptions: {
		ecmaVersion: 9,
		ecmaFeatures: {
			globalReturn: false,
			impliedStrict: true
		}
	},
	extends: [ 'eslint:recommended', 'wordpress' ],
	rules: {

		// ----- Possible Errors ---------------------------------------------------

		'for-direction': 'error',
		'getter-return': [ 'error', {allowImplicit: true} ],
		'no-await-in-loop': 'error',
		'no-extra-parens': 'error',
		'no-prototype-builtins': 'error',
		'no-template-curly-in-string': 'error',
		'valid-jsdoc': [ 'error', {requireReturnDescription: false} ],

		// ----- Best Practices ----------------------------------------------------

		'accessor-pairs': 'error',
		'array-callback-return': 'error',
		'block-scoped-var': 'error',
		complexity: [ 'error', 8 ],
		'default-case': 'error',
		'dot-location': [ 'error', 'property' ],
		eqeqeq: 'error',
		'guard-for-in': 'error',
		'no-alert': 'error',
		'no-caller': 'error',
		'no-div-regex': 'error',
		'no-else-return': 'error',
		'no-empty-function': 'error',
		'no-eq-null': 'error',
		'no-eval': 'error',
		'no-extend-native': 'error',
		'no-extra-bind': 'error',
		'no-extra-label': 'error',
		'no-floating-decimal': 'error',
		'no-implicit-coercion': 'error',
		'no-implicit-globals': 'error',
		'no-implied-eval': 'error',
		'no-invalid-this': 'error',
		'no-iterator': 'error',
		'no-labels': 'error',
		'no-lone-blocks': 'error',
		'no-loop-func': 'error',
		'no-multi-str': 'error',
		'no-new': 'error',
		'no-new-func': 'error',
		'no-new-wrappers': 'error',
		'no-octal-escape': 'error',
		'no-proto': 'error',
		'no-return-assign': [ 'error', 'always' ],
		'no-return-await': 'error',
		'no-script-url': 'error',
		'no-self-compare': 'error',
		'no-sequences': 'error',
		'no-throw-literal': 'error',
		'no-unmodified-loop-condition': 'error',
		'no-unused-expressions': [
			'error', {
				allowShortCircuit: true,
				allowTaggedTemplates: true
			}
		],
		'no-useless-call': 'error',
		'no-useless-concat': 'error',
		'no-useless-return': 'error',
		'no-void': 'error',
		'no-warning-comments': 'error',
		'prefer-promise-reject-errors': 'error',
		radix: [ 'error', 'as-needed' ],
		'require-await': 'error',
		'wrap-iife': [ 'error', 'outside' ],

		// ----- Variables ---------------------------------------------------------

		'init-declarations': [ 'error', 'always' ],
		'no-catch-shadow': 'error',
		'no-label-var': 'error',
		'no-shadow': 'error',
		'no-shadow-restricted-names': 'error',
		'no-undef-init': 'error',
		'no-undefined': 'error',
		'no-use-before-define': [ 'error', 'nofunc' ],

		// ----- Node.js and CommonJS ----------------------------------------------

		'callback-return': 'error',
		'handle-callback-err': 'error',
		'no-buffer-constructor': 'error',
		'no-mixed-requires': [ 'error', {allowCall: true} ],
		'no-new-require': 'error',
		'no-path-concat': 'error',
		'no-process-env': 'error',
		'no-process-exit': 'error',
		'no-sync': 'error',

		// ----- Stylistic Issues --------------------------------------------------

		'array-bracket-newline': [ 'error', 'consistent' ],
		'block-spacing': [ 'error', 'never' ],
		'capitalized-comments': 'error',
		'computed-property-spacing': 'error',
		'consistent-this': [ 'error', 'self' ],
		'func-call-spacing': 'error',
		'func-name-matching': 'error',
		'func-names': [ 'error', 'as-needed' ],
		'func-style': [ 'error', 'declaration' ],
		'function-paren-newline': 'error',
		'id-length': [ 'error', {exceptions: [ 'i', 'n', 'x' ]} ],
		'implicit-arrow-linebreak': 'error',
		indent: [ 'error', 'tab', {SwitchCase: 1} ],
		'jsx-quotes': 'error',
		'lines-between-class-members': 'error',
		'max-depth': 'error',
		'max-len': [
			'error', {
				code: 120,
				tabWidth: 1,
				ignoreRegExpLiterals: true,
				ignoreTrailingComments: true,
				ignoreTemplateLiterals: true,
				ignoreUrls: true
			}
		],
		'max-lines': [
			'error', {
				max: 320,
				skipBlankLines: true,
				skipComments: true
			}
		],
		'max-nested-callbacks': 'error',
		'max-params': [ 'error', {max: 8} ],
		'max-statements': [ 'error', 16 ],
		'max-statements-per-line': 'error',
		'multiline-ternary': [ 'error', 'always-multiline' ],
		'new-cap': 'error',
		'newline-per-chained-call': 'error',
		'no-array-constructor': 'error',
		'no-bitwise': 'error',
		'no-lonely-if': 'error',
		'no-multi-assign': 'error',
		'no-negated-condition': 'error',
		'no-nested-ternary': 'error',
		'no-new-object': 'error',
		'no-plusplus': [
			'error', {
				allowForLoopAfterthoughts: true
			}
		],
		'no-restricted-syntax': 'error',
		'no-ternary': 'error',
		'no-underscore-dangle': 'error',
		'no-unneeded-ternary': 'error',
		'no-whitespace-before-property': 'error',
		'nonblock-statement-body-position': 'error',
		'object-curly-newline': [ 'error', {consistent: true} ],
		'object-curly-spacing': 'error',
		'object-property-newline': 'error',
		'one-var': [
			'error', {
				initialized: 'never',
				uninitialized: 'always'
			}
		],
		'operator-assignment': 'error',
		'padded-blocks': [ 'error', 'never' ],
		'padding-line-between-statements': [
			'error', {
				blankLine: 'always',
				prev: '*',
				next: 'return'
			}, {
				blankLine: 'always',
				prev: [ 'const', 'let', 'var' ],
				next: '*'
			}, {
				blankLine: 'always',
				prev: 'directive',
				next: '*'
			}, {
				blankLine: 'any',
				prev: [ 'const', 'let', 'var' ],
				next: [ 'const', 'let', 'var' ]
			}, {
				blankLine: 'any',
				prev: 'directive',
				next: 'directive'
			}
		],
		'quote-props': [ 'error', 'as-needed' ],
		'require-jsdoc': [
			'error', {
				require: {
					FunctionDeclaration: true,
					MethodDefinition: true,
					ClassDeclaration: true
				}
			}
		],
		'semi-spacing': 'error',
		'semi-style': 'error',
		'sort-vars': 'error',
		'spaced-comment': 'error',
		'switch-colon-spacing': 'error',
		'template-tag-spacing': 'error',
		'unicode-bom': 'error',
		'wrap-regex': 'error',

		// ----- ECMAScript 6 ------------------------------------------------------

		'arrow-body-style': 'error',
		'arrow-parens': [ 'error', 'as-needed', {requireForBlockBody: true} ],
		'arrow-spacing': [
			'error', {
				before: false,
				after: true
			}
		],
		'generator-star-spacing': 'error',
		'no-confusing-arrow': 'error',
		'no-duplicate-imports': 'error',
		'no-useless-computed-key': 'error',
		'no-useless-constructor': 'error',
		'no-useless-rename': 'error',
		'no-var': 'error',
		'object-shorthand': 'error',
		'prefer-arrow-callback': 'error',
		'prefer-const': 'error',
		'prefer-numeric-literals': 'error',
		'prefer-rest-params': 'error',
		'prefer-spread': 'error',
		'prefer-template': 'error',
		'rest-spread-spacing': 'error',
		'sort-imports': 'error',
		'symbol-description': 'error',
		'template-curly-spacing': 'error',
		'yield-star-spacing': 'error'
	}
};
