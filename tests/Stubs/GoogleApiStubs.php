<?php

/**
 * Stub classes for Google API Client (used in tests when the actual SDK is not installed).
 */
if (! class_exists('Google_Client')) {
    class Google_Client
    {
        private ?string $redirectUri = null;

        private ?string $state = null;

        public function setAuthConfig($config): void {}

        public function addScope($scope): void {}

        public function setRedirectUri(string $uri): void
        {
            $this->redirectUri = $uri;
        }

        public function setState(string $state): void
        {
            $this->state = $state;
        }

        public function createAuthUrl(): string
        {
            return 'https://accounts.google.com/o/oauth2/auth?'.http_build_query([
                'redirect_uri' => $this->redirectUri,
                'state' => $this->state,
            ]);
        }
    }
}

if (! class_exists('Google_Service_Calendar')) {
    class Google_Service_Calendar
    {
        const CALENDAR = 'https://www.googleapis.com/auth/calendar';

        public $events;
    }
}

if (! class_exists('Google_Service_Calendar_Event')) {
    class Google_Service_Calendar_Event
    {
        protected $data = [];

        public function __construct(array $data = [])
        {
            $this->data = $data;
        }

        public function getId(): ?string
        {
            return $this->data['id'] ?? null;
        }

        public function getSummary(): ?string
        {
            return $this->data['summary'] ?? null;
        }

        public function setSummary(?string $summary): void
        {
            $this->data['summary'] = $summary;
        }

        public function getDescription(): ?string
        {
            return $this->data['description'] ?? null;
        }

        public function setDescription(?string $description): void
        {
            $this->data['description'] = $description;
        }

        public function getStart(): ?stdClass
        {
            if (isset($this->data['start'])) {
                return (object) $this->data['start'];
            }

            return null;
        }

        public function setStart($start): void
        {
            $this->data['start'] = is_array($start) ? $start : (array) $start;
        }

        public function setEnd($end): void
        {
            $this->data['end'] = is_array($end) ? $end : (array) $end;
        }
    }
}

if (! class_exists('Google_Service_Calendar_Events')) {
    class Google_Service_Calendar_Events
    {
        protected $items = [];

        public function __construct(array $data = [])
        {
            $this->items = $data['items'] ?? [];
        }

        public function getItems(): array
        {
            return $this->items;
        }
    }
}
