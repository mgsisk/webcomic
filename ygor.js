/* eslint-env node */

const { tasks, shell, find, read } = require( 'ygor' );

/**
 * Process everything.
 *
 * @return {void}
 */
function makeAll() {
  makeCss();
  makeImg();
  makeJs();
}

/**
 * Process CSS with postcss.
 *
 * @return {void}
 */
function makeCss() {
  const make = require( 'postcss' );
  const config = require( './.postcss.js' );

  find([ 'src/css/**/*.css', 'docs/_css/colors.css' ], { dot: true })
    .map( read() )
    .map( ( file )=>
      make( config.plugins ).process( file.contents, {
        from: file.absolute,
        to: file.absolute.replace( /\/_?css\//, '/srv/' )
      }).then( ( result )=> {
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

  find([ 'src/js/**/*.js' ], { dot: true })
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
 * Test everything.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testAll( cli ) {
  testCss( cli );
  testJs( cli );
  testJson( cli );
  testMd( cli );
  testPhp( cli );
  testShell( cli );
  testXml( cli );
  testYml( cli );
}

/**
 * Test CSS with stylelint.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testCss( cli ) {
  const test = require( 'stylelint' ).lint;
  const config = require( './.stylelintrc.js' );

  config.files = [ 'src/css/**/*.css' ];

  test( config )
    .catch( ( error )=> process.stdout.write( error.message ) && ciExit() )
    .then( ( result )=> process.stdout.write( result.output ) && result.errored && ciExit() );
}

/**
 * Test JavaScript with eslint.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testJs( cli ) {
  const Engine = require( 'eslint' ).CLIEngine;
  const test = new Engine;
  const output = test.executeOnFiles([ 'src/js/**/*.js', '*.js', '.*.js' ]);

  process.stdout.write( test.getFormatter()( output.results ) ) && output.errorCount && ciExit();
}

/**
 * Test JSON with jsonlint.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testJson( cli ) {
  const test = require( 'jsonlint' ).parse;

  find([ '*.json', '.*.json', '.babelrc', '.markdownlintrc' ], { dot: true })
    .map( read() )
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
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testMd( cli ) {
  const test = require( 'markdownlint' );

  find( '.markdownlintrc', { dot: true })
    .map( read() )
    .map( ( file )=> {
      const config = JSON.parse( file.contents );

      find([ 'docs/**/*.md', '*.md', '!license.md' ], { dot: true })
        .map( ( mdFile )=> test({
          files: mdFile.absolute,
          config: config
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
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testPhp( cli ) {
  const xml = require( 'xml2js' ).parseString;

  find( '.phpcs.xml', { dot: true })
    .map( read() )
    .map( ( file )=>
      xml( file.contents, ( xmlError, config )=> {
        for ( const phpFile of config.ruleset.file ) {
          shell`vendor/bin/phpmd ${phpFile} text .phpmd.xml`.catch( ( sh )=> ciExit( sh.code ) );
        }
      }) )
    .then( ()=>
      shell`PHAN_DISABLE_XDEBUG_WARN=1 vendor/bin/phan`.catch( ( sh )=> ciExit( sh.code ) )
        .then( ()=>
          shell`vendor/bin/phpcs`.catch( ( sh )=> ciExit( sh.code ) )
            .then( ()=>
              shell`vendor/bin/phpunit --no-coverage && WP_MULTISITE=1 vendor/bin/phpunit`.catch( ( sh )=> ciExit( sh.code ) )
            )
        )

    );
}

/**
 * Test Shell with shellcheck.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testShell( cli ) {
  find([ '*.sh', '.*.sh' ], { dot: true })
    .map( ( file )=> shell`shellcheck -x ${file.path}`.catch( ( sh )=> ciExit( sh.code ) ) );
}

/**
 * Test XML with xmllint.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testXml( cli ) {
  find([ '*.xml', '.*.xml' ], { dot: true })
    .map( ( file )=> shell`xmllint --noout ${file.path}`.catch( ( sh )=> ciExit( sh.code ) ) );
}

/**
 * Test YML with js-yaml.
 *
 * @param {object} cli Command-line arguments.
 * @return {void}
 */
function testYml( cli ) {
  const test = require( 'js-yaml' ).safeLoad;

  find([ '*.yml', '.*.yml' ], { dot: true })
    .map( read() )
    .map( ( file )=> {
      try {
        test( file.contents, { filename: file.path });
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

  process.env.TRAVIS && process.exit( code );
}

tasks.cli.quiet = true;

tasks.add( 'make', ()=> tasks()
  .add( 'all', makeAll )
  .add( 'css', makeCss )
  .add( 'img', makeImg )
  .add( 'js', makeJs )
);

tasks.add( 'test', ()=> tasks()
  .add( 'all', testAll )
  .add( 'css', testCss )
  .add( 'js', testJs )
  .add( 'json', testJson )
  .add( 'md', testMd )
  .add( 'php', testPhp )
  .add( 'shell', testShell )
  .add( 'xml', testXml )
  .add( 'yml', testYml )
);

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
