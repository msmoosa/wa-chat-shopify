<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationJob implements ShouldQueue
{
    use Queueable;

    public $event;
    public $user;
    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($event, $user, $data = [])
    {
        $this->event = $event;
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhookUrl = config('app.services.slack.notifications.' . $this->event) ?? config('app.services.slack.notifications.default');
        if (!$webhookUrl) {
            Log::error('SLACK_WEBHOOK_URL is not configured for ' . $this->event);
            return;
        }

        // get user name and email
        $userName = $this->user->name ?? $this->user->email ?? 'Unknown User';
        $userEmail = $this->user->email ?? 'N/A';

        // Create a Slack Block Kit card with user information
        $payload = [
            'blocks' => [
                [
                    'type' => 'header',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => str_replace('_', ' ', ucwords($this->event)),
                        'emoji' => true,
                    ],
                ],
                [
                    'type' => 'divider',
                ],
                [
                    'type' => 'section',
                    'fields' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Name:*\n{$userName}",
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Email:*\n{$userEmail}",
                        ],
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "*Event:*\n{$this->event}",
                    ],
                ],
            ],
        ];

        // Add additional data if provided
        if (! empty($this->data)) {
            $dataText = "```\n" . json_encode($this->data, JSON_PRETTY_PRINT) . "\n```";
            $payload['blocks'][] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Additional Data:*\n{$dataText}",
                ],
            ];
        }

        try {
            
            $response = Http::post($webhookUrl, $payload);

            if (! $response->successful()) {
                Log::error('Failed to send Slack notification', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending Slack notification', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
