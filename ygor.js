/* eslint-env node */

const sfs = require( 'spiff' );
const config = {
  css: {
    test: [ 'src/css/**/*.css' ],
    make: [ 'src/css/**/*.css', 'docs/_css/colors.css' ],
    output: ( path )=> path.replace( /\/_?css\//, '/srv/' ),
    watcher: {
      watch: [ 'src/css/**/*.css', 'docs/_css/**/*.css' ],
      change: ( ygor )=> ygor.shell( 'node ygor make css -v' )
    }
  },
  img: {
    watcher: {
      watch: [ 'assets/**/*.{jpg,png}' ],
      change: ( ygor )=> ygor.shell( 'node ygor make img -v' )
    },
    optimize: 'assets'
  },
  js: {
    test: [ 'src/js/**/*.js', '*.js', '.*.js' ],
    make: [ 'src/js/**/*.js' ],
    output: ( path )=> path.replace( '/js/', '/srv/' ),
    watcher: {
      watch: [ 'src/js/**/*.js' ],
      change: ( ygor )=> ygor.shell( 'node ygor make js -v' )
    }
  },
  json: {
    test: [ '*.json', '.*.json', '.babelrc', '.markdownlintrc' ]
  },
  md: {
    test: [ 'docs/**/*.md', '*.md' ]
  },
  shell: {
    test: [ '*.sh', '.*.sh' ]
  },
  xml: {
    test: [ '*.xml', '.*.xml' ]
  },
  yml: {
    test: [ '*.yml', '.*.yml' ]
  }
};

require( 'ygor' )

  /**
   * Delete extraneous project files.
   *
   * @return {void}
   */
  .task( 'clean', ()=> sfs.remove( '**/.DS_Store' ) )

  /**
   * Watch for file changes and process accordingly.
   *
   * @param {object} cli The command line object.
   * @param {object} ygor The task runner object.
   * @return {void}
   */
  .task( 'watch', ( cli, ygor )=> {
    const watch = require( 'chokidar' ).watch;

    process.stdout.write( 'watching...\n' );

    for ( const item in config ) {
      if ( ! config.hasOwnProperty( item ) || ! config[item].watcher ) {
        continue;
      }

      for ( const action in config[item].watcher ) {
        if ( 'watch' === action || ! config[item].watcher.hasOwnProperty( action ) ) {
          continue;
        }

        watch( config[item].watcher.watch ).on( action, ()=> config[item].watcher[action]( ygor ) );
      }
    }
  })

  .task(
    'test', ( __, ygor )=> ygor()

      /**
       * Test CSS with stylelint.
       *
       * @return {void}
       */
      .task( 'css', ()=> {
        const test = require( 'stylelint' ).lint;
        const testConfig = require( './.stylelintrc.js' );

        testConfig.files = config.css.test;

        test( testConfig ).then(
          ( result )=> process.stdout.write( result.output )
        ).catch( ( error )=> process.stdout.write( error.message ) );
      })

      /**
       * Test JavaScript with eslint.
       *
       * @return {void}
       */
      .task( 'js', ()=> {
        const Engine = require( 'eslint' ).CLIEngine;
        const test = new Engine;

        process.stdout.write(
          test.getFormatter()(
            test.executeOnFiles( config.js.test ).results
          )
        );
      })

      /**
       * Test JSON with jsonlint.
       *
       * @return {void}
       */
      .task( 'json', ()=> {
        const test = require( 'jsonlint' ).parse;

        sfs.read( config.json.test, { base: __dirname }).map( ( file )=> {
          try {
            return test( file.contents );
          } catch ( error ) {
            return process.stdout.write( `${file.relative} ${error.message}\n\n` );
          }
        });
      })

      /**
       * Test Markdown with markdownlint.
       *
       * @return {void}
       */
      .task( 'md', ()=> {
        const test = require( 'markdownlint' );

        sfs.read( '.markdownlintrc' ).map( ( file )=> {
          const testConfig = JSON.parse( file.contents );

          return sfs.find( config.md.test, {base: __dirname}).map(
            ( testFile )=> test({
              files: testFile.relative,
              config: testConfig
            }, ( linterError, result )=> {
              if ( linterError ) {
                process.stdout.write( `${linterError.message}\n\n` );
              } else if ( result.toString() ) {
                process.stdout.write( `${result.toString()}\n\n` );
              }
            })
          );
        });
      })

      /**
       * Test PHP with phan, phpcs, phpmd, and phpunit.
       *
       * @return {void}
       */
      .task( 'php', ()=> {
        const xml = require( 'xml2js' ).parseString;

        sfs.read( '.phpcs.xml' ).map(
          ( file )=> xml( file.contents, ( xmlError, testConfig )=> {
            for ( const testFile of testConfig.ruleset.file ) {
              ygor.shell(
                `vendor/bin/phpmd ${testFile} text .phpmd.xml` ).catch( ( rn )=> rn
              );
            }

            ygor.shell( 'PHAN_DISABLE_XDEBUG_WARN=1 vendor/bin/phan' ).catch( ( rn )=> rn );
            ygor.shell( 'vendor/bin/phpcs' ).catch( ( rn )=> rn );
            ygor.shell( 'vendor/bin/phpunit --no-coverage' ).catch( ( rn )=> rn );
            ygor.shell( 'WP_MULTISITE=1 vendor/bin/phpunit' ).catch( ( rn )=> rn );
          })
        );
      })

      /**
       * Test Shell with shellcheck.
       *
       * @return {void}
       */
      .task( 'shell', ()=> {
        sfs.find( config.shell.test, {base: __dirname}).map(
          ( file )=> ygor.shell( `shellcheck -x ${file.relative}` ).catch( ( rn )=> rn )
        );
      })

      /**
       * Test XML with xmllint.
       *
       * @return {void}
       */
      .task( 'xml', ()=> {
        sfs.find( config.xml.test, {base: __dirname}).map(
          ( file )=> ygor.shell( `xmllint --noout ${file.relative}` ).catch( ( rn )=> rn )
        );
      })

      /**
       * Test YML with js-yaml.
       *
       * @return {void}
       */
      .task( 'yml', ()=> {
        const test = require( 'js-yaml' ).safeLoad;

        sfs.read( config.yml.test, {base: __dirname}).map( ( file )=> {
          try {
            return test( file.contents, {filename: file.relative});
          } catch ( error ) {
            return process.stdout.write( `${error.message}\n\n` );
          }
        });
      })
  )

  .task(
    'make', ( __, ygor )=> ygor()

      /**
       * Process CSS with postcss.
       *
       * @return {void}
       */
      .task( 'css', ()=> {
        const make = require( 'postcss' );
        const makeConfig = require( './.postcss.js' );

        sfs.read( config.css.make, {base: __dirname}).map( ( file )=>
          make( makeConfig.plugins ).process( file.contents, {
            from: file.relative,
            to: config.css.output( file.relative )
          }).then( ( result )=> {
            file.path = config.css.output( file.path );
            file.contents = result.css;
            file.write();
          })
        );
      })

      /**
       * Process images with sips and imageoptim.
       *
       * @type {Object}
       */
      .task( 'img', ()=> ygor.shell( `imageoptim -a -q -d ${config.img.optimize}` ) )

      /**
       * Process JavaScript with rollup.
       *
       * @return {void}
       */
      .task( 'js', ()=> {
        const make = require( 'rollup' ).rollup;
        const makeConfig = require( './.rollup.js' );

        sfs.read( config.js.make, {base: __dirname}).map( ( file )=> {
          makeConfig.plugins.unshift({
            load: ()=> file.contents
          });
          makeConfig.input = file.relative;
          makeConfig.file = config.js.output( file.path );

          return make( makeConfig ).then( ( result )=> result.write( makeConfig ) );
        });
      })
  );
