<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class KnowledgeBaseController extends Controller
{
    public function index(): Factory|View
    {
        $articles = KnowledgeBaseArticle::latest()->paginate(10);

        return view('knowledge-base.index', ['articles' => $articles]);
    }

    public function show(KnowledgeBaseArticle $article): Factory|View
    {
        return view('knowledge-base.show', ['article' => $article]);
    }
}
