# Eventum Slack unfurl

*Slack app for [unfurling] Eventum issue links*

[unfurling]: https://api.slack.com/docs/message-link-unfurling

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
