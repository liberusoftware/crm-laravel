<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Livewire\Component;

final class QuoteBuilder extends Component
{
    public string $name = '';

    public string $currency = 'USD';

    /** @var array<int, array{description?: string, unit_price?: float|int|string, quantity?: float|int|string, discount?: float|int|string}> */
    public array $lines = [['description' => '', 'unit_price' => 0, 'quantity' => 1, 'discount' => 0]];

    public function addLine(): void
    {
        $this->lines[] = ['description' => '', 'unit_price' => 0, 'quantity' => 1, 'discount' => 0];
    }

    public function removeLine(int $index): void
    {
        if (array_key_exists($index, $this->lines) && count($this->lines) > 1) {
            array_splice($this->lines, $index, 1);
        }
    }

    public function save(PriceQuote $priceQuote): void
    {
        $user = auth()->user();
        abort_unless($user !== null && (int) $user->current_team_id > 0, 403);
        $this->validate(['name' => ['nullable', 'string', 'max:255'], 'currency' => ['required', Rule::in(['USD', 'EUR', 'GBP'])], 'lines' => ['required', 'array', 'min:1'], 'lines.*.description' => ['required', 'string', 'max:255'], 'lines.*.unit_price' => ['required', 'numeric', 'min:0'], 'lines.*.quantity' => ['required', 'numeric', 'gt:0'], 'lines.*.discount' => ['nullable', 'numeric', 'min:0']]);
        $priceQuote->execute((int) $user->current_team_id, (int) $user->getAuthIdentifier(), ['name' => $this->name, 'currency' => $this->currency, 'lines' => $this->lines]);
        $this->reset('name');
        $this->dispatch('cpq-quote-saved');
    }

    public function render(): View
    {
        abort_unless((int) auth()->user()?->current_team_id > 0, 403);

        return view('crm-cpq-livewire::quote-builder');
    }
}
