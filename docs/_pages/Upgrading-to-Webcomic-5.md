---
title: Upgrading to Webcomic 5
permalink: Upgrading-to-Webcomic-5
---

![Webcomic 5.0.0](srv/banner.png)

Webcomic 5 is a complete rewrite of the Webcomic plugin undertaken to modernize
the plugin and the development process supporting it. Extra care taken during
development should ensure that existing Webcomic sites are compatible with
Webcomic 5, but there are still some details you should know before upgrading.

## Upgrade process

> **⚠ PHP 7 and WordPress 4.7 required.** Webcomic 5 requires PHP 7 (or
> greater) and WordPress 4.7 (or greater). You'll want to double check your PHP
> version and your WordPress version before upgrading.

The upgrade process is automatic, irreversible, and transparent. You should
[make a complete backup of your site][url-1] and [review the changelog][url-2]
for an overview of important changes before upgrading. The upgrade will:

1. Convert settings and data from your current version of Webcomic.
2. Activate components corresponding to your current version of Webcomic.
3. Run extra upgrade tasks as-needed based on your current version of Webcomic.

> **⚠ Do not disable the Compat component after upgrading.** Compat is a unique
> component that provides Webcomic 5's compatibility features. The extra upgrade
> tasks described below – as well as the Inkblot 4 theme – require this
> component.

### Webcomic 4

> **⚠ Terms must be resorted and integration must be configured.** You'll need
> to resort your storylines if you manually sorted them before; you can
> configure integration options using the new Webcomic section in the
> Customizer if you were using the theme integration option.

Upgrades from Webcomic 4 and themes designed for Webcomic 4 are fully
supported. The Alert, Character, Commerce, Compat, Restrict, Storyline,
Transcribe, and Twitter components will activate during the upgrade. These
extra upgrade tasks will run as needed:

- **Media:** Webcomic 5 will create media data using the media attached to the
  comic if no other comic media exists.
- **Prints:** Webcomic 5 will create print data using collection and Webcomic 4
  print data if no other comic print data exists.
- **Transcripts:** Webcomic 5 will create transcript data using comic
  transcript and Webcomic 4 transcript data if no other comic transcript data
  exists.
- **Transcript Authors:** Webcomic 5 will create transcript author data using
  Webcomic 4 transcript author data if no other transcript author data exists.
- **Character and Storyline Media:** Webcomic 5 will create term meta data for
  any character or storyline terms that had media assigned to them.

### Webcomic 3

> **⚠ Comic media will not be imported and transcripts will not be converted.**
> You'll need to import comic media and match them with comics using the
> Webcomic Matcher; you can create new transcripts using transcript meta data
> and the Webcomic Transcripts box.

Upgrades from Webcomic 3 are partially supported; themes designed for Webcomic
3 are not supported. The Alert, Character, Commerce, Compat, Restrict,
Storyline, and Transcribe components will activate during the upgrade. These
extra upgrade tasks will run as needed:

- **Media:** Webcomic 5 will separate the media data stored in the `webcomic`
  custom field – file paths, alternate text, and descriptions – into individual
  custom fields.
- **Prints:** Webcomic 5 will create print data using collection and Webcomic 3
  print data. The print prices created during this process may not match the
  Webcomic 3 prices.
- **Transcripts:** Webcomic 5 will separate the transcript data stored in the
  `webcomic` custom field – transcript text, publish datetime, author, status,
  and language – into individual custom fields.

### Webcomic 2

> **⚠ Comic media will not be imported and transcripts will not be converted.**
> You'll need to import comic media and match them with comics using the
> Webcomic Matcher; you can create new transcripts using transcript meta data
> and the Webcomic Transcripts box.

Upgrades from Webcomic 2 are partially supported; themes designed for Webcomic
2 are not supported. The Alert, Compat, Storyline, and Transcribe components
will activate during the upgrade. No extra upgrade tasks will run.

### Webcomic 1

> **⚠ Comic media will not be imported and transcripts will not be converted.**
> You'll need to import comic media and match them with comics using the
> Webcomic Matcher; you can create new transcripts using transcript meta data
> and the Webcomic Transcripts box.

Upgrades from Webcomic 1 are partially supported; themes designed for Webcomic
1 are not supported. The Compat, Storyline, and Transcribe components will
activate during the upgrade. No extra upgrade tasks will run.

[url-1]: https://codex.wordpress.org/WordPress_Backups
[url-2]: https://github.com/mgsisk/webcomic/blob/master/changelog.md
