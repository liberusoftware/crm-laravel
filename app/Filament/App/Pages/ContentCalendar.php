<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\SocialMediaPost;
use App\Models\Team;
use App\Models\User;
use App\Services\Marketing\WorkspaceAccess;
use Carbon\CarbonInterface;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

final class ContentCalendar extends Page
{
    protected static ?string $title = 'Content calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected string $view = 'filament.app.pages.content-calendar';

    public string $month = '';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        $team = Filament::getTenant();

        return $user instanceof User && $team instanceof Team && ($user->belongsToTeam($team)) && (app(WorkspaceAccess::class)->canManage($user, $team) || $user->getAllPermissions()->contains('name', 'view_social_media_post'));
    }

    public function mount(): void
    {
        abort_unless(self::canAccess(), 403);
        $this->month = now($this->timezone())->format('Y-m');
    }

    public function moveMonth(int $direction): void
    {
        abort_unless(self::canAccess(), 403);
        abort_unless(in_array($direction, [-1, 1], true), 422);
        Validator::make(['month' => $this->month], ['month' => ['required', 'date_format:Y-m']])->validate();
        $this->month = Carbon::createFromFormat('!Y-m', $this->month, $this->timezone())->addMonths($direction)->format('Y-m');
    }

    protected function getViewData(): array
    {
        abort_unless(self::canAccess(), 403);
        Validator::make(['month' => $this->month], ['month' => ['required', 'date_format:Y-m']])->validate();
        $start = Carbon::createFromFormat('!Y-m', $this->month, $this->timezone())->startOfMonth()->startOfWeek(CarbonInterface::MONDAY);
        $end = $start->copy()->addDays(41)->endOfDay();
        $posts = SocialMediaPost::query()->where('team_id', Filament::getTenant()->getKey())->whereBetween('scheduled_at', [$start->copy()->utc(), $end->copy()->utc()])->orderBy('scheduled_at')->get()
            ->groupBy(fn ($post) => $post->scheduled_at->copy()->timezone($this->timezone())->format('Y-m-d'));
        $days = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $days[] = ['date' => $day->copy(), 'posts' => $posts->get($day->format('Y-m-d'), collect())];
        }

        return ['days' => $days, 'timezone' => $this->timezone()];
    }

    private function timezone(): string
    {
        return (string) data_get(Filament::getTenant()?->getAttribute('marketing_preferences'), 'timezone', 'UTC');
    }
}
