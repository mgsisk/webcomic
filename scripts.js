/* eslint-env node */
/* eslint-disable no-implicit-globals */

run( process.argv[2], {
	makeCss,
	makeJs,
	tagVersion,
	watch
});

/**
 * Run a task.
 *
 * @param {string} task The task to run.
 * @param {object} tasks The available tasks.
 * @return {void}
 */
function run( task, tasks ) {
	if ( ! Object.prototype.hasOwnProperty.call( tasks, task ) ) {
		return process.stdout.write( `Unknown task - ${task}\n` );
	}

	tasks[task]();
}

/**
 * Process CSS with postcss.
 *
 * @return {void}
 */
function makeCss() {
	const fastGlob = require( 'fast-glob' );
	const fs = require( 'fs' );
	const postcss = require( 'postcss' );
	const config = require( './.postcss.js' );

	fastGlob([ 'src/css/**/*.css', 'docs/_css/colors.css' ], {
		absolute: true,
		dot: true
	}).then( ( files )=> {
		for ( const path of files ) {
			fs.readFile( path, ( readError, file )=> {
				const output = path.replace( /\/_?css\//, '/srv/' );

				postcss( config.plugins )
					.process( file, {
						from: path,
						to: output
					})
					.then( result=> fs.writeFile( output, result.css, writeError=> writeError ) );
			});
		}
	});
}

/**
 * Process JavaScript with rollup.
 *
 * @return {void}
 */
function makeJs() {
	const fastGlob = require( 'fast-glob' );
	const rollup = require( 'rollup' );
	const config = require( './.rollup.js' );

	fastGlob([ 'src/js/**/*.js' ], {
		absolute: true,
		dot: true
	}).then( ( files )=> {
		for ( const path of files ) {
			const fileConfig = Object.assign({}, config );

			fileConfig.input = path;

			rollup.rollup( fileConfig ).then( ( result )=> {
				fileConfig.file = path.replace( '/js/', '/srv/' );

				result.write( fileConfig );
			});
		}
	});
}

/**
 * Update version number meta data when a new version is tagged for release.
 *
 * @return {void}
 */
function tagVersion() {
	const fastGlob = require( 'fast-glob' );
	const fs = require( 'fs' );

	fastGlob([ 'readme.md', 'src/readme.txt', 'src/webcomic.php' ], {
		absolute: true,
		dot: true
	}).then( ( files )=> {
		for ( const path of files ) {
			fs.readFile( path, 'utf8', ( readError, file )=> {
				const output = file.replace(
					/((?:Stable tag|Version):?) (0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][a-zA-Z0-9-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][a-zA-Z0-9-]*))*))?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?/,
					`$1 ${process.argv[3]}`
				);

				fs.writeFile( path, output, writeError=> writeError );
			});
		}
	});
}

/**
 * Watch for file changes and process files as needed.
 *
 * @return {void}
 */
function watch() {
	const chokidar = require( 'chokidar' );

	process.stdout.write( 'watching...\n' );

	chokidar.watch([
		'docs/_css/**/*.css',
		'src/css/**/*.css',
		'src/js/**/*.js'
	]).on( 'change', ( path )=> {
		process.stdout.write( 'working...\n' );

		if ( path.match( /\.css$/ ) ) {
			makeCss();
		} else if ( path.match( /\.js$/ ) ) {
			makeJs();
		}

		process.stdout.write( 'watching...\n' );
	});
}
