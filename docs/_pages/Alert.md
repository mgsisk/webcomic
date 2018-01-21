---
title: Alert
permalink: Alert
---

[![The settings added to the Collection Settings screen by the Alert
component.][img-1]][img-1]

The **Alert** component adds settings for creating buffer and hiatus email
alerts.

## Settings

Alert settings allow you to setup automated email alerts for your collection to
remind you of important milestones. One alert is sent for each milestone;
you'll want to setup more than one alert if you'd like more than one reminder.

### Buffer

These alerts send email reminders when the number of scheduled comics in your
collection reaches a certain point. Specify how many comics should be
scheduled, then add emails that should receive alerts. Separate multiple emails
with commas.

#### Example buffer alert

```txt
From: webcomic@example.com
To: you@example.com
Subject: [Webcomic] Untitled Comic Buffer Alert - 1 Comic Left

This is an automated reminder from Webcomic that Untitled Comic has 1 comic
left before the buffer runs out.

Schedule more comics or disable these alerts by logging into your site at
https://example.com/wp-login.php
```

### Hiatus

These alerts send email reminders when a certain number of days have passed
since the last published comic in your collection. Specify how many days it
should be since the last comic was published, then add emails that should
receive alerts. Separate multiple emails with commas.

#### Example hiatus alert

```txt
From: webcomic@example.com
To: you@example.com
Subject: [Webcomic] Untitled Comic Hiatus Alert - 30 Days Since Last Comic

This is an automated reminder from Webcomic that Untitled Comic hasn't updated
in 30 days.

Publish more comics or disable these alerts by logging into your site at
https://example.com/wp-login.php
```

[img-1]: srv/Alert.png
