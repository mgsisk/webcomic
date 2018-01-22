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

  find([ 'src/css/**/*.css', 'docs/_css/colors.css' ])
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

  find([ 'src/js/**/*.js' ])
    .map( ( file )=> {
      const fileConfig = Object.assign({}, config );

      fileConfig.input = file.absolute;
      fileConfig.file = file.absolute.replace( '/js/', '/srv/' );

      make( fileConfig ).then( ( result )=> result.write( fileConfig ) );
    });
}

/**
 * Test everything.
 *
 * @return {void}
 */
function testAll() {
  testCss();
  testJs();
  testJson();
  testMd();
  testPhp();
  testShell();
  testXml();
  testYml();
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
    .then( ( result )=> process.stdout.write( result.output ) )
    .catch( ( error )=> process.stdout.write( error.message ) );
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

  process.stdout.write( test.getFormatter()( output.results ) );
}

/**
 * Test JSON with jsonlint.
 *
 * @return {void}
 */
function testJson() {
  const test = require( 'jsonlint' ).parse;

  find([ '*.json', '.*.json', '.babelrc', '.markdownlintrc' ])
    .map( read() )
    .map( ( file )=> {
      try {
        test( file.contents );
      } catch ( error ) {
        process.stdout.write( `${file.path} ${error.message}\n\n` );
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

  find( '.markdownlintrc' )
    .map( read() )
    .map( ( file )=> {
      const config = JSON.parse( file.contents );

      find([ 'docs/**/*.md', '*.md' ])
        .map( ( mdFile )=> test({
          files: mdFile.absolute,
          config: config
        }, ( testError, result )=> {
          if ( testError ) {
            process.stdout.write( `${testError.message}\n\n` );
          } else if ( result.toString() ) {
            process.stdout.write( `${result.toString()}\n\n` );
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

  find( '.phpcs.xml' )
    .map( read() )
    .map( ( file )=>
      xml( file.contents, ( xmlError, config )=> {
        for ( const phpFile of config.ruleset.file ) {
          shell`vendor/bin/phpmd ${phpFile} text .phpmd.xml`;
        }
      }) )
    .then( ()=> shell`
      PHAN_DISABLE_XDEBUG_WARN=1 vendor/bin/phan
      vendor/bin/phpcs
      vendor/bin/phpunit --no-coverage && WP_MULTISITE=1 vendor/bin/phpunit`
    );
}

/**
 * Test Shell with shellcheck.
 *
 * @return {void}
 */
function testShell() {
  find([ '*.sh', '.*.sh' ])
    .map( ( file )=> shell`shellcheck -x ${file.path}`.catch( ( rn )=> rn ) );
}

/**
 * Test XML with xmllint.
 *
 * @return {void}
 */
function testXml() {
  find([ '*.xml', '.*.xml' ])
    .map( ( file )=> shell`xmllint --noout ${file.path}`.catch( ( rn )=> rn ) );
}

/**
 * Test YML with js-yaml.
 *
 * @return {void}
 */
function testYml() {
  const test = require( 'js-yaml' ).safeLoad;

  find([ '*.yml', '.*.yml' ])
    .map( read() )
    .map( ( file )=> {
      try {
        test( file.contents, { filename: file.path });
      } catch ( error ) {
        process.stdout.write( `${error.message}\n\n` );
      }
    });
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

    process.stdout.write( 'watching... ' );

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
