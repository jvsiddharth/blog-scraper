import axios from "axios";
import * as cheerio from "cheerio";
import "dotenv/config";

// ---------------- CONFIG ----------------
const API = process.env.LARAVEL_API;
const OPENAI_KEY = process.env.OPENAI_API_KEY;
const OPENAI_BASE = process.env.OPENAI_BASE_URL;
const MODEL = process.env.MODEL || "gpt-4o-mini";

// ----------------------------------------

async function fetchLatestArticle() {
  const { data } = await axios.get(`${API}/articles`);
  if (!data.length) throw new Error("No articles found");
  return data[0];
}

async function searchDuckDuckGo(query) {
  const url = `https://duckduckgo.com/html/?q=${encodeURIComponent(query)}`;
  const html = (await axios.get(url)).data;
  const $ = cheerio.load(html);

  const links = [];
  $("a.result__a").each((_, el) => {
    const href = $(el).attr("href");
    if (
      href &&
      !href.includes("beyondchats.com") &&
      href.startsWith("http")
    ) {
      links.push(href);
    }
  });

  return [...new Set(links)].slice(0, 2);
}

async function scrapeArticle(url) {
  const html = (await axios.get(url, { timeout: 15000 })).data;
  const $ = cheerio.load(html);

  const text = $("article").text() || $("body").text();
  return text.replace(/\s+/g, " ").slice(0, 8000);
}

async function callLLM(original, refs) {
  const prompt = `
You are a professional content editor.

Original article:
${original.content}

Reference articles:
${refs.map((r, i) => `(${i + 1}) ${r.content}`).join("\n\n")}

Rewrite the original article to match the structure, clarity, and SEO quality
of the reference articles. Keep it original, professional, and well-formatted.

At the end, include a "References" section listing the reference URLs.
`;

  const res = await axios.post(
    `${OPENAI_BASE}/chat/completions`,
    {
      model: MODEL,
      messages: [{ role: "user", content: prompt }],
    },
    {
      headers: {
        Authorization: `Bearer ${OPENAI_KEY}`,
        "Content-Type": "application/json",
      },
    }
  );

  return res.data.choices[0].message.content;
}

async function saveAIArticle(articleId, title, content, references) {
  await axios.post(`${API}/articles/${articleId}/ai`, {
    title,
    content,
    references,
    model_used: MODEL,
  });
}

// ---------------- MAIN ----------------

(async () => {
  console.log("Fetching latest article...");
  const article = await fetchLatestArticle();

  console.log("Searching reference articles...");
  const refLinks = await searchDuckDuckGo(article.title);
  if (refLinks.length < 2) throw new Error("Not enough reference articles");

  console.log("Scraping references...");
  const refs = [];
  for (const link of refLinks) {
    refs.push({
      url: link,
      content: await scrapeArticle(link),
    });
  }

  console.log("Calling LLM...");
  const rewritten = await callLLM(article, refs);

  console.log("Saving AI article...");
  await saveAIArticle(
    article.id,
    `${article.title} (AI Enhanced)`,
    rewritten,
    refLinks
  );

  console.log("✅ AI article saved successfully");
})();
