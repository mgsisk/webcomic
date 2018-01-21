---
title: Transcribe
permalink: Transcribe
---

[![The All Comic Transcripts screen, with one published transcript, one pending
transcript, one draft transcript, and one orphaned transcript in the comic
transcripts list.][img-1]][img-1]

The **Transcribe** component adds comic transcription features for
SEO-enhancing text alternatives to your comics.

## Managing transcripts

You should notice a new **Comic Transcripts** link in the sidebar. This will
take you to the **All Comic Transcripts** screen, which is the primary hub for
comic transcript management in Webcomic; from here you can add new comic
transcripts, edit existing comic transcripts, search for comic transcripts, and
more. It's a lot like the Comments screen, with two notable exceptions: extra
statuses and languages.

### Transcript status

Like comments, you can determine a comic transcript's current status using the
colored bar next to it in the comic transcript list:

- A red bar means the comic transcript is a draft and is waiting for you to
  moderate it.
- A blue bar means the comic transcript is pending review. Comic transcripts
  pending review may be edited and resubmitted as drafts if the transcript's
  parent comic allows new transcripts.
- A black bar means the comic transcript is private. Only users with the
  correct capabilities can view private transcripts.

### Transcript languages

[![The comic transcript languages management screen.][img-2]][img-2]

One advantage of using transcripts is the ability to transcribe a
single-language comic into other languages. Webcomic includes a Language
taxonomy, discussed below, to help you tag and organize transcripts in different
languages. This is entirely optional, but if a transcript _has_ an assigned
language you'll be able to see it here.

Comic transcript languages work like Tags, and you can manage them from the
**Languages** sub-screen.

## Publishing a transcript

[![The Edit Comic Transcript screen.][img-3]][img-3]

Clicking one of the **Add New** links will take you to the **Add/Edit Comic
Transcript** screen. This is one of the ways to add a new transcript, and you
may notice that it's a lot like the Add/Edit Post screen, with two notable
exceptions: the Webcomic box and the Authors box.

### Transcript comic

The **Webcomic** box shows you the comic your transcript belongs to. If the
transcript has not been assigned to a comic you can search for and assign it to
a comic using the Search Comics field. Don't forget to save your transcript
after you've selected a comic.

Once you've assigned the transcript to a comic that comic's title and media
will appear in this box for reference. Click the comic media to toggle
media resizing. You can also enable or disable transcribing of the selected
comic from here by checking or unchecking Allow transcripts and saving the
transcript. To change the comic this transcript is assigned to, click the X to
remove the assigned comic and search for a new one.

Like comments, transcripts will be deleted along with their comic if their
parent comic is permanently deleted.

### Transcript authors

The **Authors** box shows you the authors that have contributed to this
transcript. Click the + to add details for a new author, or click the X to
remove an author. You can update an author's name, email, URL, and
transcription date here, as well as view the IP address that they transcribed
from. Authors are always sorted by contribution date, from earliest to most
recent.

Don't forget to save your transcript after you've updated author information.

### Another way

[![The Add New Comic screen, with Webcomic Transcripts box
enabled.][img-4]][img-4]

Like comments, transcripts for an individual comic can also be managed from the
Add/Edit Comic screen. Once you've enabled the Transcribe component, head to
the Add/Edit Comic screen. You should notice a new **Webcomic Transcripts** box.

This box lets you enable or disable user transcribing for your comic. Once the
post has been saved, you can also add new transcripts and manage this comic's
transcripts from here.

Click Add Transcript to add a new transcript to the comic. The transcribing
form allows you to type up a transcript, view the full-size comic media with
the comic button, assign languages to your transcript, and set it's publish
status before saving.

Below the Add Transcript button is the list of available transcripts for this
comic. You may edit these transcripts inline using the Quick Edit link.
Transcripts with an orange background are drafts, while transcripts with a blue
background are pending review.

## Settings

[![The settings added to the Collection Settings screen by the Transcribe
component.][img-5]][img-5]

Transcribe settings allow you to change how comic transcription works in your
collection.

### Comics

These settings control how transcripts interact with comics. **Allow people to
transcribe new comics** will enable transcription for any newly-published
comics. You can also enable or disable transcription on a per-comic basis.
**Automatically close transcripts on comics older than X days** will close
transcription – both the submission of new transcripts and the improvement of
pending transcripts – on any comic that was published more than X days ago.

### Permissions

These settings control who can transcribe comics and what happens when they
submit a transcript. By default, anyone can transcribe a comic, and transcripts
are always saved as drafts when submitted. **Transcript authors must…** requires
transcript authors to either provide a name and email address or to register
and login before they can submit a transcript. **Publish transcripts from
authors that…** will automatically publish transcripts submitted by authors
that either provide a name and email address or register and login before they
submit a transcript.

### Notifications

These settings allow you to receive email notifications when transcripts are
submitted. **Send an email whenever a transcript is published** will send an
email to the comic author whenever a transcript is published. **Send an email
whenever a transcript is held for moderation** will send an email to the site
email address – and the comic author, if they can publish transcripts –
whenever a transcript has been submitted and needs to be reviewed.

[img-1]: srv/Transcribe.png
[img-2]: srv/Transcribe-Languages.png
[img-3]: srv/Transcribe-Edit.png
[img-4]: srv/Transcribe-Box.png
[img-5]: srv/Transcribe-Settings.png
