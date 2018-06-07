# Contributing

Thanks for wanting to contribute to Webcomic; contributions are always welcome!
These guidelines should help get you started.

1. [Review the Code of Conduct][conduct-it]
2. [Submit an issue][submit-it] or [select an existing issue to work on][issues]
3. [Fork this repository][fork-it]
4. [Clone your fork][clone-it]
5. [Create a branch][branch-it]
6. [Make changes][change-it]
7. [Test your changes][test-it]
8. [Create a pull request][pull-it] with [a good commit message][commit-it]

## Development Tools

Webcomic uses a variety of open source software to manage development; you'll
want to at least install [Composer], [Node], [PHP 7] with [php-ast] and
[xdebug], [Vagrant], and [VirtualBox]. If you're working on a Mac you can
install the suggested software using [Homebrew Bundle].

You can install the development dependencies and launch the development
environment with:

```sh
npm install; npm start
```

A set of automation tasks is available through [npm] to help with processing
assets and running tests. You can see a list of available tasks with:

```sh
npm run
```

The `test` box hosts a WordPress multisite install with a set of development
plugins network-activated and the [WordPress Theme Unit Test Data] pre-loaded.
Some box administration tools are available at the `admin` subdomain (e.g.
`admin.local.test`). Use the username and password `root` to login to WordPress
and the box administration tools.

If you're making changes that need cross-browser testing you can test in Edge
and Internet Explorer with:

```sh
npm run start:edge
```

## Coding Standards

Webcomic adheres to a customized version of the [WordPress Coding Standards] and
uses the [WordPress PHPUnit Test Framework]. Before submitting a pull request
you should:

1. Resolve or annotate any coding standard issues.
2. Add tests to cover all changes.
3. Verify that all new and existing tests pass.

The `test` box hosts the database used to run PHPUnit tests locally. Group tests
by component (e.g. `@group collection`); add isolated tests to the `isolated`
group as well as their component group.

[conduct-it]: https://github.com/mgsisk/webcomic/blob/master/code_of_conduct.md
[submit-it]: https://github.com/mgsisk/webcomic/issues/new
[issues]: https://github.com/mgsisk/webcomic/issues
[fork-it]: https://help.github.com/articles/fork-a-repo
[clone-it]: https://help.github.com/articles/cloning-a-repository
[branch-it]: https://help.github.com/articles/creating-and-deleting-branches-within-your-repository
[change-it]: #development-tools
[test-it]: #coding-standards
[pull-it]: https://help.github.com/articles/creating-a-pull-request
[commit-it]: https://chris.beams.io/posts/git-commit
[Homebrew Bundle]: https://github.com/Homebrew/homebrew-bundle
[Composer]: https://getcomposer.org
[Node]: https://nodejs.org
[npm]: https://docs.npmjs.com/misc/scripts
[PHP 7]: https://php.net
[php-ast]: https://github.com/nikic/php-ast
[xdebug]: https://github.com/xdebug/xdebug
[Vagrant]: https://vagrantup.com
[VirtualBox]: https://www.virtualbox.org
[Atom]: https://atom.io
[WordPress Theme Unit Test Data]: https://github.com/WPTRT/theme-unit-test
[WordPress Coding Standards]: https://make.wordpress.org/core/handbook/best-practices/coding-standards
[WordPress PHPUnit Test Framework]: https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit
