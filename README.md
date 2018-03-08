# Extensible Slack App for link unfurling

*Slack app for [unfurling] issue links*

[unfurling]: https://api.slack.com/docs/message-link-unfurling

## Slack App

[Create](https://api.slack.com/apps/new) or [Manage](https://api.slack.com/apps) your app.

You need `Verification Token` (`SLACK_VERIFICATION_TOKEN`) for next step.

Under `Features`:
- enable [Events API](http://api.slack.com/events-api) for [`link_shared`](https://api.slack.com/events/link_shared) event with `links:read` scope.
- enable `Incoming Webhooks`

Obtain `OAuth Access Token` (`SLACK_API_TOKEN`) under `OAuth & Permissions`

## Configuration

```
cp env.example .env
```

## Adding providers

The app itself handles no links, you need to add some providers:

- [Eventum Provider](https://github.com/eventum/slack-unfurl-eventum)
- [GitLab Provider](https://github.com/glensc/slack-unfurl-gitlab)

## Troubleshoot

If the unfurl is not happening check that the domain is not [blacklisted](https://my.slack.com/admin/attachments).

If you modify domains the app domains, you need to `Install App` (`/install-on-team` as url) again to re-authorize.

## Development

Install composer deps and start builtin HTTP server:

```
composer install
composer run server
```

To build docker image locally and run it:

```
docker build -t slack-unfurl .
docker run --rm -p 4390:4390 -v $(pwd)/var/logs:/app/var/logs -v $(pwd)/.env:/app/.env slack-unfurl
```

The service is accessible from http://slack-unfurl.127.0.0.1.xip.io:4390/ or just http://localhost:4390/
