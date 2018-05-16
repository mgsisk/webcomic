/* eslint-env node */
/* eslint-disable no-implicit-globals */

const {tasks, shell, find, read} = require( 'ygor' );

/**
 * Process API documentation.
 *
 * @return {void}
 */
function makeApi() {
	shell`php docs/_build/api.php`;
}

/**
 * Process CSS with postcss.
 *
 * @return {void}
 */
function makeCss() {
	const make = require( 'postcss' );
	const config = require( './.postcss.js' );

	find([ 'src/css/**/*.css', 'docs/_css/colors.css' ], {dot: true})
		.map( read() )
		.map( file=> make( config.plugins ).process( file.contents, {
			from: file.absolute,
			to: file.absolute.replace( /\/_?css\//, '/srv/' )
		})
			.then( ( result )=> {
				file.path = file.absolute.replace( /\/_?css\//, '/srv/' );
				file.contents = result.css;
				file.write();
			}) );
}

/**
 * Process images with sips and imageoptim.
 *
 * @return {void}
 */
function makeImg() {
	shell`find ./assets ./docs/srv -name '*.jpg' -o -name '*.png' | imageoptim -a -q`;
}

/**
 * Process JavaScript with rollup.
 *
 * @return {void}
 */
function makeJs() {
	const make = require( 'rollup' ).rollup;
	const config = require( './.rollup.js' );

	find([ 'src/js/**/*.js' ], {dot: true})
		/* eslint-disable-next-line */
		.map( ( file )=> {
			const fileConfig = Object.assign({}, config );

			fileConfig.input = file.absolute;

			make( fileConfig ).then( ( result )=> {
				fileConfig.file = file.absolute.replace( '/js/', '/srv/' );

				result.write( fileConfig );
			});
		});
}

/**
 * Process documentation for the wiki.
 *
 * @return {void}
 */
function makeWiki() {
	shell`php docs/_build/wiki.php`;
}

/**
 * Test CSS with stylelint.
 *
 * @return {void}
 */
function testCss() {
	const test = require( 'stylelint' ).lint;
	const config = require( './.stylelintrc.js' );

	config.files = [ 'src/css/**/*.css' ];

	test( config )
		.catch( error=> process.stdout.write( error.message ) && ciExit() )
		.then( result=> process.stdout.write( result.output ) && result.errored && ciExit() );
}

/**
 * Test JavaScript with eslint.
 *
 * @return {void}
 */
function testJs() {
	const Engine = require( 'eslint' ).CLIEngine;
	const test = new Engine;
	const output = test.executeOnFiles([ 'src/js/**/*.js', '*.js', '.*.js' ]);

	process.stdout.write( test.getFormatter()( output.results ) ) && output.errorCount && ciExit();
}

/**
 * Test JSON with jsonlint.
 *
 * @return {void}
 */
function testJson() {
	const test = require( 'jsonlint' ).parse;

	find([ '*.json', '.*.json', '.babelrc', '.markdownlintrc' ], {dot: true})
		.map( read() )
		/* eslint-disable-next-line array-callback-return */
		.map( ( file )=> {
			try {
				test( file.contents );
			} catch ( error ) {
				process.stdout.write( `${file.path} ${error.message}\n\n` ) && ciExit();
			}
		});
}

/**
 * Test Markdown with markdownlint.
 *
 * @return {void}
 */
function testMd() {
	const test = require( 'markdownlint' );

	find( '.markdownlintrc', {dot: true})
		.map( read() )
		/* eslint-disable-next-line array-callback-return */
		.map( ( file )=> {
			const config = JSON.parse( file.contents );

			find([ 'docs/**/*.md', '*.md', '!license.md' ], {dot: true})
				.map( mdFile=> test({
					files: mdFile.absolute,
					config
				}, ( testError, result )=> {
					if ( testError ) {
						process.stdout.write( `${testError.message}\n\n` ) && ciExit();
					} else if ( result.toString() ) {
						process.stdout.write( `${result.toString()}\n\n` ) && ciExit();
					}
				}) );
		});
}

/**
 * Test PHP with phan, phpcs, phpmd, and phpunit.
 *
 * @return {void}
 */
function testPhp() {
	const xml = require( 'xml2js' ).parseString;

	find( '.phpcs.xml', {dot: true})
		.map( read() )
		.map( file=> xml( file.contents, ( xmlError, config )=> {
			for ( const phpFile of config.ruleset.file ) {
				shell`vendor/bin/phpmd ${phpFile} text .phpmd.xml`.catch( sh=> ciExit( sh.code ) );
			}
		}) )
		.then( ()=> shell`PHAN_DISABLE_XDEBUG_WARN=1 vendor/bin/phan`.catch( sh=> ciExit( sh.code ) )
			.then( ()=> shell`vendor/bin/phpcs`.catch( sh=> ciExit( sh.code ) )
				.then( ()=> shell`vendor/bin/phpunit --no-coverage && WP_MULTISITE=1 vendor/bin/phpunit`.catch( sh=> ciExit( sh.code ) ) ) ) );
}

/**
 * Test Shell with shellcheck.
 *
 * @return {void}
 */
function testShell() {
	find([ '*.sh', '.*.sh' ], {dot: true}).map( file=> shell`shellcheck -x ${file.path}`.catch( sh=> ciExit( sh.code ) ) );
}

/**
 * Test XML with xmllint.
 *
 * @return {void}
 */
function testXml() {
	find([ '*.xml', '.*.xml' ], {dot: true}).map( file=> shell`xmllint --noout ${file.path}`.catch( sh=> ciExit( sh.code ) ) );
}

/**
 * Test YML with js-yaml.
 *
 * @return {void}
 */
function testYml() {
	const test = require( 'js-yaml' ).safeLoad;

	find([ '*.yml', '.*.yml' ], {dot: true})
		.map( read() )
		/* eslint-disable-next-line array-callback-return */
		.map( ( file )=> {
			try {
				test( file.contents, {filename: file.path});
			} catch ( error ) {
				process.stdout.write( `${error.message}\n\n` ) && ciExit();
			}
		});
}

/**
 * Exit with an approriate code during continuous integration.
 *
 * @param {int} code The process exit code.
 * @return {void}
 */
function ciExit( code ) {
	if ( ! code ) {
		code = 1;
	}

	process.env.TRAVIS && process.exit( code ); // eslint-disable-line no-process-env, no-process-exit
}

tasks.cli.quiet = true;

tasks.add( 'make', ()=> tasks()
	.add( 'api', makeApi )
	.add( 'css', makeCss )
	.add( 'img', makeImg )
	.add( 'js', makeJs )
	.add( 'wiki', makeWiki ) );

tasks.add( 'test', ()=> tasks()
	.add( 'css', testCss )
	.add( 'js', testJs )
	.add( 'json', testJson )
	.add( 'md', testMd )
	.add( 'php', testPhp )
	.add( 'shell', testShell )
	.add( 'xml', testXml )
	.add( 'yml', testYml ) );

tasks
	.add( 'clean', ()=> shell`find . -name '*.DS_Store' -type f -delete` )
	.add( 'watch', ()=> {
		const watch = require( 'chokidar' ).watch;

		process.stdout.write( 'watching...\n' );

		watch([
			'assets/**/*.{jpg,png}',
			'docs/srv/*{jpg,png}',
			'docs/_css/**/*.css',
			'src/css/**/*.css',
			'src/js/**/*.js'
		])
			.on( 'change', ( path )=> {
				process.stdout.write( 'working...\n' );

				if ( path.match( /\.css$/ ) ) {
					makeCss();
				} else if ( path.match( /\.(jpg|png)$/ ) ) {
					makeImg();
				} else if ( path.match( /\.js$/ ) ) {
					makeJs();
				}

				process.stdout.write( 'watching...\n' );
			});
	});
