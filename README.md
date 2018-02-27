# Eventum Slack unfurl

*Slack app for [unfurling] Eventum issue links*

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

## Development

Install composer deps and start builtin HTTP server:

```
composer install
composer run server
```

To build docker image locally and run it:

```
docker build -t eventum-slack-unfurl .
docker run --rm -p 4390:4390 -v $(pwd)/var/logs:/app/var/logs -v $(pwd)/.env:/app/.env eventum-slack-unfurl
```

The service is accessible from http://eventum-slack-unfurl.127.0.0.1.xip.io:4390/ or just http://localhost:4390/
