/* eslint-env node */

const { tasks, shell, find, read, write } = require( 'ygor' );

/**
 * Process CSS with postcss.
 *
 * @return {void}
 */
async function makeCss() {
  const make = require( 'postcss' );
  const config = require( './.postcss.js' );

  await find([ 'src/css/**/*.css', 'docs/_css/colors.css' ]).map( read() ).map( async( file ) =>
    await make( config.plugins ).process( file.contents, {
      from: file.absolute,
      to: file.absolute.replace( /\/_?css\//, '/srv/' )
    }).then( ( result )=> {
      file.path = file.absolute.replace( /\/_?css\//, '/srv/' );
      file.contents = result.css;
      file.write();
    })
  );
}

/**
 * Process images with sips and imageoptim.
 *
 * @return {void}
 */
async function makeImg() {
  await shell`imageoptim -a -q -d assets`;
}

/**
 * Process JavaScript with rollup.
 *
 * @return {void}
 */
async function makeJs() {
  const make = require( 'rollup' ).rollup;
  const config = require( './.rollup.js' );

  await find([ 'src/js/**/*.js' ]).map( async( file )=> {
    const fileConfig = Object.assign({}, config );

    fileConfig.input = file.absolute;
    fileConfig.file = file.absolute.replace( '/js/', '/srv/' );

    await make( fileConfig ).then( ( result )=> result.write( fileConfig ) );
  });
}

/**
 * Test CSS with stylelint.
 *
 * @return {void}
 */
async function testCss() {
  const test = require( 'stylelint' ).lint;
  const config = require( './.stylelintrc.js' );

  config.files = [ 'src/css/**/*.css' ];

  await test( config ).then(
    ( result )=> process.stdout.write( result.output )
  ).catch( ( error )=> process.stdout.write( error.message ) );
}

/**
 * Test JavaScript with eslint.
 *
 * @return {void}
 */
async function testJs() {
  const Engine = require( 'eslint' ).CLIEngine;
  const test = new Engine;

  process.stdout.write(
    test.getFormatter()(
      test.executeOnFiles([ 'src/js/**/*.js', '*.js', '.*.js' ]).results
    )
  );
}

/**
 * Test JSON with jsonlint.
 *
 * @return {void}
 */
async function testJson() {
  const test = require( 'jsonlint' ).parse;

  await find([ '*.json', '.*.json', '.babelrc', '.markdownlintrc' ]).map( read() ).map( ( file )=> {
    try {
      test( file.contents );
    } catch ( error ) {
      process.stdout.write( file.path + ' ' + error.message + '\n\n' );
    }
  });
}

/**
 * Test Markdown with markdownlint.
 *
 * TODO await
 *
 * @return {void}
 */
async function testMd() {
  const test = require( 'markdownlint' );

  find( '.markdownlintrc' ).map( read() ).map( ( file )=> {
    const config = JSON.parse( file.contents );

    find([ 'docs/**/*.md', '*.md' ]).map( ( mdFile )=> test({
      files: mdFile.absolute,
      config: config
    }, ( testError, result )=> {
      if ( testError ) {
        process.stdout.write( testError.message + '\n\n' );
      } else if ( result.toString() ) {
        process.stdout.write( result.toString() + '\n\n' );
      }
    }) );
  });
}

/**
 * Test PHP with phan, phpcs, phpmd, and phpunit.
 *
 * @return {void}
 */
async function testPhp() {
  const xml = require( 'xml2js' ).parseString;

  await find( '.phpcs.xml' ).map( read() ).map( async( file )=>
    await xml( file.contents, async( xmlError, config )=> {
      for ( const phpFile of config.ruleset.file ) {
        await shell`vendor/bin/phpmd ${phpFile} text .phpmd.xml`;
      }
    })
  );

  await shell`PHAN_DISABLE_XDEBUG_WARN=1 vendor/bin/phan`;
  await shell`vendor/bin/phpcs`;
  await shell`vendor/bin/phpunit --no-coverage`;
  await shell`WP_MULTISITE=1 vendor/bin/phpunit`;
}

/**
 * Test Shell with shellcheck.
 *
 * @return {void}
 */
async function testShell() {
  await find([ '*.sh', '.*.sh' ]).map( ( file )=>
    shell`shellcheck -x ${file.path}`.catch( ( rn )=> rn )
  );
}

/**
 * Test XML with xmllint.
 *
 * @return {void}
 */
async function testXml() {
  await find([ '*.xml', '.*.xml' ]).map(
    async( file )=> await shell`xmllint --noout ${file.path}`.catch( ( rn )=> rn )
  );
}

/**
 * Test YML with js-yaml.
 *
 * @return {void}
 */
async function testYml() {
  const test = require( 'js-yaml' ).safeLoad;

  await find([ '*.yml', '.*.yml' ]).map( read() ).map( async( file )=> {
    try {
      await test( file.contents, { filename: file.path });
    } catch ( error ) {
      process.stdout.write( error.message + '\n\n' );
    }
  });
}

/**
 * Watch for file changess and execute tasks.
 *
 * @return {void}
 */
function watchTask() {
  const watch = require( 'chokidar' ).watch;

  watch([
    'src/css/**/*.css',
    'docs/_css/**/*.css',
    'assets/**/*.{jpg,png}',
    'src/js/**/*.js'
  ]).on( 'change', ( path )=> {
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
}

tasks.add( 'clean', async()=> await shell`find . -name '*.DS_Store' -type f -delete` );

tasks.add( 'make', ()=> tasks()
  .add( 'css', makeCss )
  .add( 'img', makeImg )
  .add( 'js', makeJs )
);

tasks.add( 'test', ()=> tasks()
  .add( 'css', testCss )
  .add( 'js', testJs )
  .add( 'json', testJson )
  .add( 'md', testMd )
  .add( 'php', testPhp )
  .add( 'shell', testShell )
  .add( 'xml', testXml )
  .add( 'yml', testYml )
);

tasks.add( 'watch', watchTask );
