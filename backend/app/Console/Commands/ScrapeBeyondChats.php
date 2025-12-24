<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeBeyondChats extends Command
{
    protected $signature = 'scrape:beyondchats';
    protected $description = 'Scrape the 5 oldest articles from BeyondChats blog';

    public function handle(): int
    {
        $baseUrl = 'https://beyondchats.com/blogs';

        $this->info('Detecting last blog page…');

        // 1️⃣ Detect last page number
        $html = Http::timeout(15)->get($baseUrl)->body();
        $crawler = new Crawler($html);

        $pageNumbers = $crawler
            ->filter('a[href*="/blogs/page/"]')
            ->each(function (Crawler $node) {
                if (preg_match('#/blogs/page/(\d+)/?#', $node->attr('href'), $m)) {
                    return (int) $m[1];
                }
                return null;
            });

        $pageNumbers = array_filter($pageNumbers);

        if (empty($pageNumbers)) {
            $this->error('Pagination not found');
            return self::FAILURE;
        }

        $currentPage = max($pageNumbers);
        $this->info("Last page detected: {$currentPage}");

        $collected = collect();

        // 2️⃣ Walk backwards until we have 5 articles
        while ($currentPage >= 1 && $collected->count() < 5) {
            $pageUrl = "{$baseUrl}/page/{$currentPage}/";
            $this->info("Scanning page {$currentPage}");

            $pageHtml = Http::timeout(15)->get($pageUrl)->body();
            $pageCrawler = new Crawler($pageHtml);

            $links = collect(
                $pageCrawler
                    ->filter('article h2 a, article h3 a, h2.entry-title a')
                    ->each(fn ($n) => $n->attr('href'))
            )
                ->filter(fn ($url) =>
                    $url &&
                    str_starts_with($url, 'https://beyondchats.com/blogs/')
                )
                ->unique()
                ->values();

            foreach ($links as $url) {
                if ($collected->count() >= 5) {
                    break;
                }

                if (
                    ! $collected->contains($url) &&
                    ! Article::where('source_url', $url)->exists()
                ) {
                    $collected->push($url);
                }
            }

            $currentPage--;
        }

        if ($collected->count() < 5) {
            $this->error('Could not find 5 unique articles');
            return self::FAILURE;
        }

        // 3️⃣ Scrape collected articles
        foreach ($collected as $url) {
            $this->info("Scraping: {$url}");

            $articleHtml = Http::timeout(15)->get($url)->body();
            $articleCrawler = new Crawler($articleHtml);

            $title = trim(
                $articleCrawler->filter('h1')->count()
                    ? $articleCrawler->filter('h1')->text()
                    : 'Untitled'
            );

            $content = trim(
                $articleCrawler->filter('article')->count()
                    ? $articleCrawler->filter('article')->text()
                    : $articleCrawler->filter('body')->text()
            );

            Article::create([
                'title' => $title,
                'content' => $content,
                'source_url' => $url,
                'is_generated' => false,
            ]);

            $this->info("Saved: {$title}");
        }

        $this->info('Scraping completed successfully');
        return self::SUCCESS;
    }
}
