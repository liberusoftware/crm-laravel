<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $knowledgeBaseArticles = KnowledgeBaseArticle::latest()->take(5)->get();

        return view('home', ['knowledgeBaseArticles' => $knowledgeBaseArticles]);
    }

    public function dashboard(): RedirectResponse
    {
        $user = request()->user();

        if ($user?->hasRole('super_admin')) {
            return redirect('/admin');
        }

        $team = $user?->currentTeam ?? $user?->ownedTeams()->first();

        if ($team === null) {
            return redirect()->route('home');
        }

        return redirect()->route('filament.app.pages.dashboard', ['tenant' => $team]);
    }
}
