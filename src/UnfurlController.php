<?php

namespace Eventum\SlackUnfurl;

use Eventum\SlackUnfurl\Command\UrlVerification;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UnfurlController
{
    public function __invoke(Request $request, Application $app)
    {
        $content = $request->getContent();
        $data = json_decode($content, 1);
        if (!$data) {
            throw new InvalidArgumentException();
        }

        $type = $data['type'] ?? null;
        $command = $this->getCommand($type);
        return $command->execute($data);
    }

    private function getCommand($type)
    {
        $map = [
            'url_verification' => UrlVerification::class,
        ];

        $className = $map[$type] ?? null;
        if (!$className) {
            throw new InvalidArgumentException();
        }

        return new $className;
    }
}